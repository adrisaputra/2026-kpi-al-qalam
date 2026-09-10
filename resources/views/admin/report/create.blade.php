<form id="myForm" action="{{ url('/'.Request::segment(1)) }}" method="POST" enctype="multipart/form-data" class="form-horizontal">
    {{ csrf_field() }}

    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="head_title">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>

                <div class="modal-body">
                    <input type="hidden" class="form-control form-control-sm" name="id" id="id_report" />
                    <input type="hidden" class="form-control form-control-sm" name="report_category_id" id="report_category_id" value="{{ $report_category->id }}"/>

                    <div class="form-group">
                        <p>{{ __('Nama Item Penilaian') }} <span class="required" style="color: #dd4b39;">*</span></p>
                        <input type="text" class="form-control form-control-sm" name="name" id="name">
                        <div id="name-error" class="fv-plugins-message-container invalid-feedback" style="display: block;"></div>
                    </div>

					<div class="form-group" style="margin-top:20px">
						<p>{{ __('Jenis Inputan') }} <span class="required" style="color: #dd4b39;">*</span></p>
						<select class="form-control form-control-sm" name="category" id="category">
							<option value="">- Pilih Jenis Inputan -</option>
							<option value="1">Inputan Pilihan 0 dan 1</option>
							<option value="2">Inputan Pilihan 0, 1 dan 2</option>
							<option value="3">Inputan Manual</option>
							<option value="4">Inputan Manual + Rumus </option>
							<option value="5">Inputan File Gambar</option>
						</select>
						<div id="category-error" class="fv-plugins-message-container invalid-feedback" style="display: block;"></div>
					</div>

                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Tutup</button>
                    <button type="submit" class="btn btn-primary" id="action" title="Tambah Data"> Simpan</button>
                </div>
            </div>
        </div>
    </div>

</form>