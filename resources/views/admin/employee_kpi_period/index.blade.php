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
                                    <div class="col-md-10">: <b>{{ $employee->name }}</b></div>
                                    <div class="col-md-2">NIK</div>
                                    <div class="col-md-10">: {{ $employee->nik }}</div>
                                    <div class="col-md-2">NIY</div>
                                    <div class="col-md-10">: {{ $employee->niy }}</div>
                                    <div class="col-md-2">Unit Kerja</div>
                                    <div class="col-md-10">: {{ $employee->work_unit?->name }}</div>
                                    <div class="col-md-2">Kategori KPI</div>
                                    <div class="col-md-10">: {{ $employee_kpi->kpi->kpi_category->name }}</div>
                                    <div class="col-md-2">KPI</div>
                                    <div class="col-md-10">: {{ $employee_kpi->kpi->name }}</div>
                                </div>
                            </p>	
                        
                            <hr>
                            <div class="row">
                                <div class="col-xl-8 col-md-12 col-sm-12 col-12">
                                    <a href="#" class="btn mb-2 mr-1 btn-success" id="create_period" onClick="generateKpiIndicator({{ $employee_kpi->id }});"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus-circle"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg></a>
                                    <a href="{{ url(Request::segment(1).'/'.Request::segment(2)) }}" class="btn mb-2 mr-1 btn-warning" data-toggle="tooltip" data-placement="top" title="Refresh"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-refresh-ccw"><polyline points="1 4 1 10 7 10"></polyline><polyline points="23 20 23 14 17 14"></polyline><path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15"></path></svg></a>
									<a href="{{ url('employee_kpi_detail/'.Crypt::encrypt($employee->id)) }}" class="btn mb-2 mr-1 btn-danger" data-toggle="tooltip" data-placement="top" title="Kembali"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left-circle"><circle cx="12" cy="12" r="10"></circle><polyline points="12 8 8 12 12 16"></polyline><line x1="16" y1="12" x2="8" y2="12"></line></svg></a>
                                </div>
                                
                                <div class="col-xl-2 col-md-12 col-sm-12 col-12">
                                    <input type="hidden" value="{{ $employee_kpi->month }}" id="get_month" name="get_month" class="form-control form-control-sm" style="height: 38px;padding: 5px;">
                                    <input type="hidden" value="{{ $employee_kpi->year }}" id="get_year" name="get_year" class="form-control form-control-sm" style="height: 38px;padding: 5px;">
								</div>

                                {{--<div class="col-xl-2 col-md-12 col-sm-12 col-12">    
                                    <select id="get_month" name="get_month" class="basic form-control form-control-sm" style="height: 38px;padding: 5px;" disabled>
                                        <option value="01" @if($employee_kpi->month == '01') selected @endif>Januari</option>
                                        <option value="02" @if($employee_kpi->month == '02') selected @endif>Februari</option>
                                        <option value="03" @if($employee_kpi->month == '03') selected @endif>Maret</option>
                                        <option value="04" @if($employee_kpi->month == '04') selected @endif>April</option>
                                        <option value="05" @if($employee_kpi->month == '05') selected @endif>Mei</option>
                                        <option value="06" @if($employee_kpi->month == '06') selected @endif>Juni</option>
                                        <option value="07" @if($employee_kpi->month == '07') selected @endif>Juli</option>
                                        <option value="08" @if($employee_kpi->month == '08') selected @endif>Agustus</option>
                                        <option value="09" @if($employee_kpi->month == '09') selected @endif>September</option>
                                        <option value="10" @if($employee_kpi->month == '10') selected @endif>Oktober</option>
                                        <option value="11" @if($employee_kpi->month == '11') selected @endif>November</option>
                                        <option value="12" @if($employee_kpi->month == '12') selected @endif>Desember</option>
									</select>
								</div>
								<div class="col-xl-2 col-md-12 col-sm-12 col-12">
                                    <select id="get_year" name="get_year" class="basic form-control form-control-sm" style="height: 38px;padding: 5px;" disabled>
                                        @for($i=2026;$i<=date('Y');$i++)
                                            <option value="{{ $i }}" @if($employee_kpi->year==$i) selected @endif>{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>--}}
                            </div>
                        </div>
						
						@include('admin.employee_kpi_period.create') 
								
                        <div class="widget-content widget-content-area" style="padding-top: 0px;">
						<p style="font-size:20px;font-weight:bold;text-align:center">Bulan {{ \App\Helpers\Helpers::month_name($employee_kpi->month) }} Tahun {{ $employee_kpi->year }}</p>
						<p style="font-size:18px;font-weight:bold;text-align:center">Indikator KPI</p>
						
                            <div class="table-responsive" style="background-color: white;padding:10px 10px 10px 10px;border-radius: 15px;">
                                <table class="table table-bordered table-hover mb-12" id="employee-kpi-indicator-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 2%">Number</th>
                                            <th style="width: 2%">No</th>
                                            <th>Indikator Id</th>
                                            <th>Indikator KPI</th>
                                            <th>Target</th>
                                            <th style="width: 5%">Bobot (%)</th>
                                            <th style="width: 5%">Skor (1-5)</th>
                                            <th style="width: 5%">Nilai</th>
                                            <th style="width: 10%"></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="5" style="text-align:right;">TOTAL</th>
                                            <th id="total_weight">0</th>
                                            <th id="total_score">0</th>
                                            <th id="total_value">0</th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                                
                            </div>	   

                        </div>
                        <hr>
                        <div class="widget-content widget-content-area" style="padding-top: 0px;" >
						<p style="font-size:18px;font-weight:bold;text-align:center">Bonus</p>
						
                            <div class="table-responsive" style="background-color: white;padding:10px 10px 10px 10px;border-radius: 15px;">
                                <table class="table table-bordered table-hover mb-12" id="employee-kpi-bonus-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 2%">Number</th>
                                            <th style="width: 2%">No</th>
                                            <th>Indikator Id</th>
                                            <th>Indikator KPI</th>
                                            <th>Target</th>
                                            <th style="width: 5%">Bobot (%)</th>
                                            <th style="width: 5%">Skor (Rp)</th>
                                            <th style="width: 5%">Nilai (Rp)</th>
                                            <th style="width: 10%"></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="5" style="text-align:right;">TOTAL</th>
                                            <th id="total_weight_bonus">0</th>
                                            <th id="total_score_bonus">0</th>
                                            <th id="total_value_bonus">0</th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                                
                            </div>	                         

                        </div>
                    </div>
                </div>

            </div>
<script src="{{ asset('backend/assets/js/jquery-3.4.1.min.js')}}"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
    var table;
    var table2;

    $(document).ready(function () {
        table = $('#employee-kpi-indicator-table').DataTable({
            processing: true,
            serverSide: true,
			ajax: {
				url: "{{ route('employee_kpi_period.list', ['employee_kpi' => $employee_kpi->id]) }}",
				type: 'GET',
				dataType: 'json',
				data: function (d) {
					d.get_month = $('#get_month').val(); // Kirim nilai combobox office dalam request
					d.get_year = $('#get_year').val(); // Kirim nilai combobox office dalam request
				}
			},
            columns: [
				{data: 'id', name: 'id', visible: false},
				{data: 'number', name: 'number'}, // Kolom nomor urut
                {data: 'indicator_id', name: 'indicator_id', visible: false},
                {data: 'indicator', name: 'kpi_indicator'},
                {data: 'target', name: 'target'},
                {data: 'weight', name: 'weight'},
                {data: 'score', name: 'score'},
                {data: 'value', name: 'value'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
			order: [
				[2, 'asc'] // Mengatur pengurutan kolom pertama (id) secara descending
			],
            paging: false,
            pageLength: -1, // Menampilkan 100 data per halaman
            // TOTAL
            footerCallback: function (row, data, start, end, display) {

                let totalWeight = 0;
                let totalScore = 0;
                let totalValue = 0;

                data.forEach(function (item) {

                    totalWeight += parseFloat(item.weight) || 0;
                    totalScore += parseFloat(item.score) || 0;
                    totalValue += parseFloat(item.value) || 0;

                });

                $('#total_weight').text(totalWeight);
                $('#total_score').text(totalScore);
                $('#total_value').text(totalValue.toFixed(2));
            },
			drawCallback: function () {
                var api = this.api();

                var startIndex = api.context[0]._iDisplayStart;

                api.column(1, {page: 'current'}).nodes().each(function (cell, i) {
                    cell.innerHTML = startIndex + i + 1;
                });

                
                // Disable tombol jika DataTables kosong
                if (api.rows({ page: 'current' }).count() === 0) {
                    $('#create_period').removeClass('disabled').removeAttr('aria-disabled');
                } else {
                    $('#create_period').addClass('disabled').attr('aria-disabled', 'true');
                }
            }
        });

        table2 = $('#employee-kpi-bonus-table').DataTable({
            processing: true,
            serverSide: true,
			ajax: {
				url: "{{ route('employee_kpi_period_bonus.list', ['employee_kpi' => $employee_kpi->id]) }}",
				type: 'GET',
				dataType: 'json',
				data: function (d) {
					d.get_month = $('#get_month').val(); // Kirim nilai combobox office dalam request
					d.get_year = $('#get_year').val(); // Kirim nilai combobox office dalam request
				}
			},
            columns: [
				{data: 'id', name: 'id', visible: false},
				{data: 'number', name: 'number'}, // Kolom nomor urut
                {data: 'indicator_id', name: 'indicator_id', visible: false},
                {data: 'indicator', name: 'kpi_indicator'},
                {data: 'target', name: 'target'},
                {data: 'weight', name: 'weight'},
                {data: 'score', name: 'score'},
                {data: 'value', name: 'value'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
			order: [
				[2, 'asc'] // Mengatur pengurutan kolom pertama (id) secara descending
			],
            paging: false,
            pageLength: -1, // Menampilkan 100 data per halaman
            // TOTAL
            footerCallback: function (row, data, start, end, display) {

                let totalWeightBonus = 0;
                let totalScoreBonus = 0;
                let totalValueBonus = 0;

                data.forEach(function (item) {

                    totalWeightBonus += parseFloat(item.weight) || 0;
                    totalScoreBonus += parseNumberIndonesia(item.score);
                    totalValueBonus += parseNumberIndonesia(item.value);

                });

                $('#total_weight_bonus').text(totalWeightBonus);
                $('#total_score_bonus').text(formatRupiah2(totalScoreBonus));
                $('#total_value_bonus').text(formatRupiah2(totalValueBonus));
            },
			drawCallback: function () {
                var api = this.api();

                var startIndex = api.context[0]._iDisplayStart;

                api.column(1, {page: 'current'}).nodes().each(function (cell, i) {
                    cell.innerHTML = startIndex + i + 1;
                });

            }
        });

        $('#myForm').submit(function (e) {
            e.preventDefault(); // Hindari pengiriman form secara default

            var id_employee_kpi_bonus = $('#id_employee_kpi_bonus').val();
            var score = $('#score').val();
            var value = $('#value').val();

            // Buat objek FormData untuk mengirim data form, termasuk file
            var formData = new FormData();
            formData.append('id', id_employee_kpi_bonus);
            formData.append('score', score);
            formData.append('value', value);
            formData.append('_token', "{{ csrf_token() }}");

            // Kirim permintaan validasi ke controller via Ajax
            var url = "{{ url('/employee_kpi_bonus/validate') }}";
            $.ajax({
                url: url,
                type: "POST",
                data: formData,
                contentType: false, // Tidak mengatur contentType secara otomatis
                processData: false, // Tidak memproses data secara otomatis
                success: function (response) {
                   
                    $('.invalid-feedback').html(''); // Hapus pesan kesalahan
                    $('.is-invalid').removeClass('is-invalid'); // Hapus kelas is-invalid dari bidang-bidang yang divalidasi

                    update(id_employee_kpi_bonus);

                },
                error: function (xhr) {
                    var errors = xhr.responseJSON.errors;

                    // Bersihkan semua pesan kesalahan sebelum menampilkan yang baru
                    $('.fv-plugins-message-container').html('');

                    // Tampilkan pesan kesalahan untuk setiap bidang jika ada
                    if (errors) {
                        $.each(errors, function (key, value) {
                            $('#' + key + '-error').html(value[0]);
                        });
                    }
                }
            });
        });
    });

    // Tambahkan event listener untuk perubahan combo box office
    $('#get_month').on('change', function () {
        table.draw(); // Panggil ulang DataTable untuk memperbarui data berdasarkan filter office
        table2.draw(); // Panggil ulang DataTable untuk memperbarui data berdasarkan filter office
    });

    // Tambahkan event listener untuk perubahan combo box office
    $('#get_year').on('change', function () {
        table.draw(); // Panggil ulang DataTable untuk memperbarui data berdasarkan filter office
        table2.draw(); // Panggil ulang DataTable untuk memperbarui data berdasarkan filter office
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
    
    // Get Data
    function getData(id){
        document.getElementById("head_title").textContent = "Ubah Nilai";
        document.getElementById("action").textContent = "Update";
        // Kirim data formulir ke server menggunakan AJAX

        var url = "{{ url('/employee_kpi_bonus/edit') }}";
        $.ajax({
            url: url + "/" + id,
            type: "GET",
            success: function (response) {
                document.getElementById("id_employee_kpi_bonus").value = response.data.id;
                document.getElementById("score").value = formatRupiah2(response.data.score);
                document.getElementById("value").value = formatRupiah2(response.data.value);
            },
            error: function (xhr) {
                // Tangani kesalahan jika pengiriman formulir gagal
                showFailedToast(xhr); // Tampilkan notifikasi toast untuk keberhasilan
                console.error("Error pengiriman formulir:", xhr);
            }
        });
    }

    // Update Data
    function update(id) {
        var formData = new FormData($('#myForm')[0]); // Buat objek FormData dari formulir
        formData.append('_token', "{{ csrf_token() }}");
        formData.append('_method', "PUT");
        
        // Kirim data formulir ke server menggunakan AJAX

        var url = "{{ url('/employee_kpi_bonus/edit') }}";
        $.ajax({
            url: url + "/" + id,
            type: "POST",
            data: formData,
            contentType: false, // Biarkan jQuery menentukan contentType secara otomatis
            processData: false, // Biarkan jQuery menangani proses data secara otomatis
            success: function (response) {
                showSuccessToast(response.message); // Tampilkan notifikasi toast untuk keberhasilan
                $('#myForm')[0].reset(); // Reset form setelah berhasil memperbarui data
                $('#exampleModal').modal('hide'); // Tutup modal setelah berhasil memperbarui data
                table2.ajax.reload(null, false); // Muat ulang DataTables setelah update
            },
            error: function (xhr) {
                // Tangani kesalahan jika pengiriman formulir gagal
                showFailedToast(xhr); // Tampilkan notifikasi toast untuk keberhasilan
                console.error("Error pengiriman formulir:", xhr);
            }
        });
    }
    

    // Create Employee KPI Indicator
    function generateKpiIndicator(employee_kpi_id) {
        swal({
			title: 'Apakah Kamu Yakin Akan Generate Data KPI untuk Bulan '+ $('#get_month').val() +' Tahun ' + $('#get_year').val() + '?',
			type: 'warning',
			showCancelButton: true,
			confirmButtonText: 'Buat KPI',
			padding: '2em'
		}).then(function (result) {
			if (result.value) {
				swal(
					'Berhasil!',
					'Data KPI Berhasil Dibuat.',
					'success'
				).then(function () {
					var url = "{{ url('/employee_kpi_indicator/store') }}";
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            employee_kpi_id: employee_kpi_id,
                            month: $('#get_month').val(),
                            year: $('#get_year').val()
                        },
                        success: function (response) {
                            if(response.success === true){
                                showSuccessToast(response.message);
                            } else {
                                showFailedToast(response.message);
                            }
                            table.ajax.reload(null, false);
                        },
                        error: function (xhr) {
                            showFailedToast(xhr); // Tampilkan notifikasi toast untuk keberhasilan
                            console.error("Error pengiriman formulir:", xhr);
                        }
                    });
				});
			}
		});
	
    }

    // Fungsi bantu untuk format Rupiah
    function formatRupiah2(angka) {
        return new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 0
        }).format(angka);
    }
        
    function parseNumberIndonesia(value) {
        if (value === null || value === undefined || value === '') {
            return 0;
        }

        return parseFloat(
            String(value)
                .replace(/\./g, '')
                .replace(',', '.')
        ) || 0;
    }
</script>
@endsection