@extends('admin.layout')
@section('content')
<style>
.dataTables_paginate,
.dataTables_info {
    display: none !important;
}
</style>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">

        <!--  BEGIN CONTENT AREA  -->
        <div id="content" class="main-content">
            <div class="layout-px-spacing">

                <div class="row layout-top-spacing">
                    <div id="tableHover" class="col-lg-12 col-12 layout-spacing">
                        <div class="statbox widget box box-shadow">
                            <div class="widget-header">
                                <div class="row">
                                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
							 		<h4>Data {{ __($title) }}</h4>
                                    </div>                 
                                </div>
                            </div>
                            
                        <div class="widget-content widget-content-area">
                            <p style="font-size:16px;margin-top:-20px;">
                                <div class="row">
                                    <div class="col-md-2">Nama</div>
                                    <div class="col-md-10">: <b>{{ $employee_kpi_indicator->employee_kpi_period->employee->name }}</b></div>
                                    <div class="col-md-2">NIK</div>
                                    <div class="col-md-10">: {{ $employee_kpi_indicator->employee_kpi_period->employee->nik }}</div>
                                    <div class="col-md-2">NIY</div>
                                    <div class="col-md-10">: {{ $employee_kpi_indicator->employee_kpi_period->employee->niy }}</div>
                                    <div class="col-md-2">Unit Kerja</div>
                                    <div class="col-md-10">: {{ $employee_kpi_indicator->employee_kpi_period->employee->work_unit?->name }}</div>
                                    <div class="col-md-2">Kategori KPI</div>
                                    <div class="col-md-10">: {{ $employee_kpi_indicator->employee_kpi_period->employee->employee_kpi->kpi->kpi_category->name }}</div>
                                    <div class="col-md-2">KPI</div>
                                    <div class="col-md-10">: {{ $employee_kpi_indicator->employee_kpi_period->employee->employee_kpi->kpi->name }}</div>
                                </div>
                            </p>	
                        
                            <hr>
                            <div class="row">
                                <div class="col-xl-6 col-md-12 col-sm-12 col-12">
                                    <a href="{{ url('employee_kpi_period/'.Crypt::encrypt($employee_kpi_indicator->employee_kpi_period->employee->id)) }}" class="btn mb-2 mr-1 btn-danger" data-toggle="tooltip" data-placement="top" title="Kembali"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left-circle"><circle cx="12" cy="12" r="10"></circle><polyline points="12 8 8 12 12 16"></polyline><line x1="16" y1="12" x2="8" y2="12"></line></svg></a>
                                </div>
							</div>
						</div>
						
                        <div class="widget-content widget-content-area" style="padding-top: 0px;">
						    <p style="font-size:18px;font-weight:bold;text-align:center">Indikator KPI : {{ $employee_kpi_indicator->kpi_indicator->indicator }}</p>
                            <p style="font-size:16px;margin-top:20px;">
                                <div class="row">
                                    <div class="col-md-3">Persentase Ketercapaian %</div>
                                    <div class="col-md-9">: <span id="presentase" style="font-weight:bold"></span></div>
                                    <div class="col-md-3">Konversi skor</div>
                                    <div class="col-md-9">: <span id="score" style="font-weight:bold"></span></div>
                                </div>
                            </p>	
                            <div class="table-responsive">
								<table class="table table-bordered table-hover mb-12" id="employee-kpi-indicator-table">
									<thead>
										<tr>
											<th style="width: 2%">Number</th>
											<th style="width: 2%">No</th>
											<th>KPI Id</th>
											<th>Alat Ukur/Indikator</th>
											<th>Bukti Fisik</th>
											<th style="width: 150px;">Nilai</th>
										</tr>
									</thead>
								</table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
    var table;

    $(document).ready(function () {
        table = $('#employee-kpi-indicator-table').DataTable({
            processing: true,
            serverSide: true,
			ajax: {
				url: "{{ route('employee_kpi_indicator_item.list', ['employee_kpi_indicator' => Crypt::encrypt($employee_kpi_indicator->id)]) }}",
				type: 'GET',
				dataType: 'json'
			},
            columns: [
				{data: 'id', name: 'id', visible: false},
				{data: 'number', name: 'number'}, // Kolom nomor urut
                {data: 'kpi_indicator_item_id', name: 'kpi_indicator_item_id', visible: false},
                {data: 'measurement_tool', name: 'measurement_tool'},
                {data: 'physical_evidence', name: 'physical_evidence'},
                {data: 'value', name: 'value'}
            ],
			order: [
				[2, 'asc'] // Mengatur pengurutan kolom pertama (id) secara descending
			],
            // paging: true,
            // pageLength: 100, // Menampilkan 100 data per halaman
            paging: false,
            pageLength: -1, 
			drawCallback: function () {

                var api = this.api();

                // Nomor
                var startIndex = api.context[0]._iDisplayStart;

                api.column(1, {page: 'current'}).nodes().each(function (cell, i) {
                    cell.innerHTML = startIndex + i + 1;
                });

                // ==========================
                // HITUNG PERSENTASE
                // ==========================

                let totalItem = 0;
                let totalValue = 0;

                api.rows().data().each(function (row) {

                    totalItem++;

                    totalValue += parseInt(row.value_raw) || 0;

                });

                let presentase = 0;

                if (totalItem > 0) {
                    presentase = (totalValue / totalItem) * 100;
                }

                // ==========================
                // KONVERSI SKOR
                // ==========================

                let score = 1;

                if (presentase >= 90) {
                    score = 5;
                } else if (presentase >= 80) {
                    score = 4;
                } else if (presentase >= 70) {
                    score = 3;
                } else if (presentase >= 60) {
                    score = 2;
                } else {
                    score = 1;
                }

                // ==========================
                // TAMPILKAN
                // ==========================

                $('#presentase').text(presentase.toFixed(2) + '%');
                $('#score').text(score);
            }
        });

    });

    // Fungsi untuk menampilkan notifikasi toast dengan ikon centang
    function showSuccessToast(message) {
        Snackbar.show({
            text: message,
            showAction: false,
            actionTextColor: '#fff',
            backgroundColor: '#8dbf42',
            pos: 'top-right'
        });
    }

    function showFailedToast(message) {
        Snackbar.show({
            text: message,
            showAction: false,
            actionTextColor: '#fff',
            backgroundColor: '#e7515a',
            pos: 'top-right'
        });
    }
    

    // Create Data
    function updateValueitem(element, id) {
        let value = $(element).val();

        console.log('ID:', id);
        console.log('Value:', value);
    
        var url = "{{ url('/employee_kpi_indicator_item/edit') }}";
        $.ajax({
            url: url + "/" + id,
            type: "PUT",
            data: {
                value : value,
                _token: "{{ csrf_token() }}"
            },
            success: function (response) {
                showSuccessToast(response.message); // Tampilkan notifikasi toast untuk keberhasilan
                $('#presentase').text(response.presentase.toFixed(2) + '%');
                $('#score').text(response.score);
            },
            error: function (xhr) {
                // Tangani kesalahan jika pengiriman formulir gagal
                showFailedToast(xhr); // Tampilkan notifikasi toast untuk keberhasilan
                console.error("Error pengiriman formulir:", xhr);
            }
        });
    }
        
</script>
@endsection