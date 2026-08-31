@extends('admin.layout')
@section('content')
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
                                    <div class="col-md-10">: {{ $employee->employee_kpi->kpi->kpi_category->name }}</div>
                                    <div class="col-md-2">KPI</div>
                                    <div class="col-md-10">: {{ $employee->employee_kpi->kpi->name }}</div>
                                </div>
                            </p>	
                        
                            <hr>
                            <div class="row">
                                <div class="col-xl-6 col-md-12 col-sm-12 col-12">
                                    <a href="#" class="btn mb-2 mr-1 btn-success" onClick="generateKpiIndicator({{ $employee->id }});"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus-circle"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg></a>
                                    <a href="{{ url('employee') }}" class="btn mb-2 mr-1 btn-danger" data-toggle="tooltip" data-placement="top" title="Kembali"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left-circle"><circle cx="12" cy="12" r="10"></circle><polyline points="12 8 8 12 12 16"></polyline><line x1="16" y1="12" x2="8" y2="12"></line></svg></a>
                                </div>
                                
                                <div class="col-xl-3 col-md-12 col-sm-12 col-12">
                                    <select id="get_month" name="get_month" class="basic form-control form-control-sm" style="height: 38px;padding: 5px;">
                                        <option value="01" @if(date('m') == '01') selected @endif>Januari</option>
                                        <option value="02" @if(date('m') == '02') selected @endif>Februari</option>
                                        <option value="03" @if(date('m') == '03') selected @endif>Maret</option>
                                        <option value="04" @if(date('m') == '04') selected @endif>April</option>
                                        <option value="05" @if(date('m') == '05') selected @endif>Mei</option>
                                        <option value="06" @if(date('m') == '06') selected @endif>Juni</option>
                                        <option value="07" @if(date('m') == '07') selected @endif>Juli</option>
                                        <option value="08" @if(date('m') == '08') selected @endif>Agustus</option>
                                        <option value="09" @if(date('m') == '09') selected @endif>September</option>
                                        <option value="10" @if(date('m') == '10') selected @endif>Oktober</option>
                                        <option value="11" @if(date('m') == '11') selected @endif>November</option>
                                        <option value="12" @if(date('m') == '12') selected @endif>Desember</option>
									</select>
								</div>
								<div class="col-xl-3 col-md-12 col-sm-12 col-12">
                                    <select id="get_year" name="get_year" class="basic form-control form-control-sm" style="height: 38px;padding: 5px;">
                                        @for($i=2026;$i<=date('Y');$i++)
                                            <option value="{{ $i }}" @if(date('Y')==$i) selected @endif>{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
								</div>
							</div>
						
						{{-- @include('admin.kpi.create') --}}
								
                        <div class="widget-content widget-content-area" style="padding-top: 0px;">
						{{-- <p style="font-size:18px;font-weight:bold;text-align:center">{{ $kpi_category->name}}</p>	 --}}
							<div class="table-responsive">
								<table class="table table-bordered table-hover mb-12" id="employee-kpi-indicator-table">
									<thead>
										<tr>
											<th style="width: 2%">Number</th>
											<th style="width: 2%">No</th>
											<th>Indikator Id</th>
											<th>Indikator KPI</th>
											<th>Target</th>
											<th>Bobot</th>
											<th>Skor</th>
											<th>Nilai</th>
											<th style="width: 15%"></th>
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
				url: "{{ route('employee_kpi_period.list') }}",
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
                {data: 'value', name: 'value'},
                {data: 'value', name: 'value'},
                {data: 'value', name: 'value'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
			order: [
				[2, 'asc'] // Mengatur pengurutan kolom pertama (id) secara descending
			],
            paging: true,
            pageLength: 100, // Menampilkan 100 data per halaman
			drawCallback: function () {
                var api = this.api();
                var startIndex = api.context[0]._iDisplayStart; // Indeks baris pertama di halaman
                api.column(1, {page: 'current'}).nodes().each(function (cell, i) {
                    cell.innerHTML = startIndex + i + 1; // Menghitung nomor urut berdasarkan indeks baris dan nomor halaman
                });
            }
        });

    });

    // Tambahkan event listener untuk perubahan combo box office
    $('#get_month').on('change', function () {
        table.draw(); // Panggil ulang DataTable untuk memperbarui data berdasarkan filter office
    });

    // Tambahkan event listener untuk perubahan combo box office
    $('#get_year').on('change', function () {
        table.draw(); // Panggil ulang DataTable untuk memperbarui data berdasarkan filter office
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
    
    // Create Employee KPI Indicator
    function generateKpiIndicator(employee_id) {
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
                            employee_id: employee_id,
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


    // Create Data
    function send() {
        var formData = new FormData($('#myForm')[0]); // Buat objek FormData dari formulir

        // Kirim data formulir ke server menggunakan AJAX
        $.ajax({
            url: "{{ url('kpi/store') }}",
            type: "POST",
            data: formData,
            contentType: false, // Biarkan jQuery menentukan contentType secara otomatis
            processData: false, // Biarkan jQuery menangani proses data secara otomatis
            success: function (response) {
                showSuccessToast(response.message); // Tampilkan notifikasi toast
                $('#myForm')[0].reset(); // Reset form setelah berhasil menambahkan data
                $('#exampleModal').modal('hide');
                table.ajax.reload(null, false);
            },
            error: function (xhr) {
                // Tangani kesalahan jika pengiriman formulir gagal
                console.error("Error pengiriman formulir:", xhr);
            }
        });
    }
        
    // Get Data
    function getData(id){
        document.getElementById("head_title").textContent = "Ubah {{ __($title) }}";
        document.getElementById("action").textContent = "Update";
        // Kirim data formulir ke server menggunakan AJAX

        var url = "{{ url('/kpi/edit') }}";
        $.ajax({
            url: url + "/" + id,
            type: "GET",
            success: function (response) {
                document.getElementById("id_kpi").value = response.data.id;
                document.getElementById("name").value = response.data.name;
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

        var url = "{{ url('/kpi/edit') }}";
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
                table.ajax.reload(null, false); // Muat ulang DataTables setelah update
            },
            error: function (xhr) {
                // Tangani kesalahan jika pengiriman formulir gagal
                showFailedToast(xhr); // Tampilkan notifikasi toast untuk keberhasilan
                console.error("Error pengiriman formulir:", xhr);
            }
        });
    }
    
    // Delete Data
    function deleteData(id) {
        swal({
			title: 'Apakah Kamu Yakin?',
			text: "Anda tidak akan dapat mengembalikan ini!",
			type: 'warning',
			showCancelButton: true,
			confirmButtonText: 'Delete',
			padding: '2em'
		}).then(function (result) {
			if (result.value) {
				swal(
					'Deleted!',
					'Data Berhasil Dihapus.',
					'success'
				).then(function () {
					var url = "{{ url('/kpi/delete') }}";
                    $.ajax({
                        url: url + "/" + id,
                        success: function (response) {
                            showSuccessToast(response.message);
                            $('#myForm')[0].reset();
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



</script>
@endsection