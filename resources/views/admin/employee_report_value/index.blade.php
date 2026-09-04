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
                                    <div class="col-md-10">: <b>{{ $employee_report_period->employee->name }}</b></div>
                                    <div class="col-md-2">NIK</div>
                                    <div class="col-md-10">: {{ $employee_report_period->employee->nik }}</div>
                                    <div class="col-md-2">NIY</div>
                                    <div class="col-md-10">: {{ $employee_report_period->employee->niy }}</div>
                                    <div class="col-md-2">Unit Kerja</div>
                                    <div class="col-md-10">: {{ $employee_report_period->employee->work_unit?->name }}</div>
                                    <div class="col-md-2">Kategori KPI</div>
                                    <div class="col-md-10">: {{ $employee_report_period->employee_report_category->report_category->name }}</div>
                                </div>
                            </p>	
                        
                            <hr>
                            <div class="row">
                                <div class="col-xl-6 col-md-12 col-sm-12 col-12">
                                    <a href="{{ url('employee_report_period/'.Crypt::encrypt($employee_report_period->employee_report_category_id)) }}" class="btn mb-2 mr-1 btn-danger" data-toggle="tooltip" data-placement="top" title="Kembali"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left-circle"><circle cx="12" cy="12" r="10"></circle><polyline points="12 8 8 12 12 16"></polyline><line x1="16" y1="12" x2="8" y2="12"></line></svg></a>
                                </div>
							</div>
						</div>
						
                        <div class="widget-content widget-content-area" style="padding-top: 0px;">
						    <p style="font-size:18px;font-weight:bold;text-align:center">Hari/Tanggal : {{ $employee_report_period->day }}, {{ date('d-m-Y', strtotime($employee_report_period->date)) }}</p>
                            <div class="table-responsive">
								<table class="table table-bordered table-hover mb-12" id="employee-kpi-indicator-table">
									<thead>
										<tr>
											<th style="width: 2%">Number</th>
											<th style="width: 2%">No</th>
											<th>Nama Penilaian</th>
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
				url: "{{ route('employee_report_value.list', ['employee_report_period' => Crypt::encrypt($employee_report_period->id)]) }}",
				type: 'GET',
				dataType: 'json'
			},
            columns: [
				{data: 'id', name: 'id', visible: false},
				{data: 'number', name: 'number'}, // Kolom nomor urut
                {data: 'name', name: 'name'},
                {data: 'value', name: 'value'}
            ],
			order: [
				[0, 'asc'] // Mengatur pengurutan kolom pertama (id) secara descending
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
    
        var url = "{{ url('/employee_report_value/edit') }}";
        $.ajax({
            url: url + "/" + id,
            type: "PUT",
            data: {
                value : value,
                _token: "{{ csrf_token() }}"
            },
            success: function (response) {
                showSuccessToast(response.message); // Tampilkan notifikasi toast untuk keberhasilan
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