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
                                    <div class="col-md-10">: <b>{{ $employee_report->employee_report_period->employee->name }}</b></div>
                                    <div class="col-md-2">NIK</div>
                                    <div class="col-md-10">: {{ $employee_report->employee_report_period->employee->nik }}</div>
                                    <div class="col-md-2">NIY</div>
                                    <div class="col-md-10">: {{ $employee_report->employee_report_period->employee->niy }}</div>
                                    <div class="col-md-2">Unit Kerja</div>
                                    <div class="col-md-10">: {{ $employee_report->employee_report_period->employee->work_unit?->name }}</div>
                                    <div class="col-md-2">Kategori Rapor</div>
                                    <div class="col-md-10">: {{ $employee_report->employee_report_period->employee_report_category->report_category->name }}</div>
                                </div>
                            </p>	
								<div class="row">
									<div class="@if(Auth::user()->group->name == 'Admin KPI') col-xl-5 @else col-xl-8 @endif col-md-12 col-sm-12 col-12">
										<a href="#" class="btn mb-2 mr-1 btn-success" data-placement="top" data-toggle="modal" data-target="#exampleModal" title="Tambah Data" onClick="clearForm()"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus-circle"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg></a>
                                        
									<a href="{{ url('employee_report_value/'.Crypt::encrypt( $employee_report->employee_report_period->id)) }}" class="btn mb-2 mr-1 btn-danger" data-toggle="tooltip" data-placement="top" title="Kembali"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left-circle"><circle cx="12" cy="12" r="10"></circle><polyline points="12 8 8 12 12 16"></polyline><line x1="16" y1="12" x2="8" y2="12"></line></svg></a>
                                    </div> 
								</div>
							</div>
						
						    @include('admin.employee_report_file.create')
								
                            <div class="widget-content widget-content-area" style="padding-top: 0px;">
							<p style="font-size:18px;font-weight:bold;text-align:center">
                                Hari/Tanggal : {{ $employee_report->employee_report_period->day }}, {{ date('d-m-Y', strtotime( $employee_report->employee_report_period->date)) }}<br>
							    Nama Penilaian : {{ $employee_report->report->name }}
                            </p>
							<div class="table-responsive">
								<table class="table table-bordered table-hover mb-12" id="employee-report-file-table">
									<thead>
										<tr>
											<th style="width: 2%">Number</th>
											<th style="width: 2%">No</th>
											<th style="width: 40%">Gambar</th>
											<th>Keterangan</th>
											<th style="width: 10%"></th>
										</tr>
									</thead>
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

    $(document).ready(function () {
        table = $('#employee-report-file-table').DataTable({
            processing: true,
            serverSide: true,
			ajax: {
                url: "{{ route('employee_report_file.list', ['employee_report' => $employee_report->id]) }}",
			},
            columns: [
				{data: 'id', name: 'id', visible: false},
				{data: 'number', name: 'number'}, // Kolom nomor urut
                {data: 'display_image', name: 'image'},
                {data: 'desc', name: 'desc'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
			order: [
				[2, 'asc'] // Mengatur pengurutan kolom pertama (id) secara descending
			],
            paging: true,
            pageLength: 25, // 👈 jumlah data per halaman
			drawCallback: function () {
                var api = this.api();
                var startIndex = api.context[0]._iDisplayStart; // Indeks baris pertama di halaman
                api.column(1, {page: 'current'}).nodes().each(function (cell, i) {
                    cell.innerHTML = startIndex + i + 1; // Menghitung nomor urut berdasarkan indeks baris dan nomor halaman
                });
            }
        });
        
        $('#myForm').submit(function (e) {
            e.preventDefault(); // Hindari pengiriman form secara default

            var action = document.getElementById('action').innerText;
            var id_employee_report_file = $('#id_employee_report_file').val();

            // Buat objek FormData untuk mengirim data form, termasuk file
            var formData = new FormData();
            formData.append('id', id_employee_report_file);
            formData.append('_token', "{{ csrf_token() }}");

            var fileInput = document.getElementById('image');
            if (fileInput.files.length > 0) {
                formData.append('image', fileInput.files[0]);
            }

            // Kirim permintaan validasi ke controller via Ajax
            var url = "{{ url('/employee_report_file/validate') }}";
            $.ajax({
                url: url + "/" + action,
                type: "POST",
                data: formData,
                contentType: false, // Tidak mengatur contentType secara otomatis
                processData: false, // Tidak memproses data secara otomatis
                success: function (response) {
                   
                    $('.invalid-feedback').html(''); // Hapus pesan kesalahan
                    $('.is-invalid').removeClass('is-invalid'); // Hapus kelas is-invalid dari bidang-bidang yang divalidasi

                    if (action === "Simpan") {
                        send();
                    } else {
                        update(id_employee_report_file);
                    }

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

    function clearForm(){
        document.getElementById("head_title").textContent = "Tambah {{ __($title) }}";
        $('#myForm')[0].reset();
        document.getElementById("action").textContent = "Simpan";
    }

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
    function send() {
        var formData = new FormData($('#myForm')[0]); // Buat objek FormData dari formulir

        // Kirim data formulir ke server menggunakan AJAX
        $.ajax({
            url: "{{ url('employee_report_file/store') }}",
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

        var url = "{{ url('/employee_report_file/edit') }}";
        $.ajax({
            url: url + "/" + id,
            type: "GET",
            success: function (response) {
                document.getElementById("id_employee_report_file").value = response.data.id;
                document.getElementById("employee_report_id").value = response.data.employee_report_id;
                document.getElementById("desc").value = response.data.desc;
                
                if(response.data.image){
                    var image = '<br><a href="{{ asset("storage/upload/employee_report_file/") }}/' + response.data.image + '" class="btn mb-2 mr-1 btn-sm btn-info snackbar-bg-info" target="_blank">Lihat Gambar Sebelumnya</a>';
                    document.getElementById("show_image").innerHTML = image;
                } else {
                    document.getElementById("show_image").innerHTML = '';
                }
                
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

        var url = "{{ url('/employee_report_file/edit') }}";
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
					var url = "{{ url('/employee_report_file/delete') }}";
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