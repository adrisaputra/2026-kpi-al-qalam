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
                    <input type="hidden" class="form-control form-control-sm" name="id" id="id_employee" />

                    <div class="form-group">
                        <p>{{ __('Nama') }} <span class="required" style="color: #dd4b39;">*</span></p>
                        <input type="text" class="form-control form-control-sm" name="name" id="name">
                        <div id="name-error" class="fv-plugins-message-container invalid-feedback" style="display: block;"></div>
                    </div>

                    <div class="form-group">
                        <p>{{ __('NIK') }} <span class="required" style="color: #dd4b39;">*</span></p>
                        <input type="text" class="form-control form-control-sm" name="nik" id="nik">
                        <div id="nik-error" class="fv-plugins-message-container invalid-feedback" style="display: block;"></div>
                    </div>

                    <div class="form-group">
                        <p>{{ __('NIY') }}</p>
                        <input type="text" class="form-control form-control-sm" name="niy" id="niy">
                    </div>

                    <div class="form-group">
                        <p>{{ __('Tempat Lahir') }}</p>
                        <input type="text" class="form-control form-control-sm" name="birthplace" id="birthplace">
                    </div>

                    <div class="form-group">
                        <p>{{ __('Tanggal Lahir') }}</p>
                        <input type="text" class="form-control form-control-sm" id="date" style="display:none">
                        <input type="text" class="form-control form-control-sm" name="birthdate" id="birthdate">
                    </div>

                    <div class="form-group">
                        <p>{{ __('Jenis Kelamin') }}</p>
                        <select class="form-control form-control-sm" name="gender" id="gender">
                            <option value="">- Pilih Jenis Kelamin -</option>
                            <option value="Male">Laki-laki</option>
                            <option value="Female">Perempuan</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <p>{{ __('Alamat') }}</p>
                        <textarea class="form-control form-control-sm" name="address" id="address" rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <p>{{ __('Agama') }}</p>
                        <select class="form-control form-control-sm" name="religion" id="religion">
                            <option value=""> -Pilih Agama-</option>
                            <option value="Islam"> Islam</option>
                            <option value="Katolik"> Katolik</option>
                            <option value="Hindu"> Hindu</option>
                            <option value="Budha"> Budha</option>
                            <option value="Sinto"> Sinto</option>
                            <option value="Konghucu"> Konghucu</option>
                            <option value="Protestan"> Protestan</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <p>{{ __('Golongan Darah') }}</p>
                        <select class="form-control form-control-sm" name="blood_type" id="blood_type">
                            <option value=""> -Pilih Golongan Darah-</option>
                            <option value="A"> A</option>
                            <option value="B"> B</option>
                            <option value="AB"> AB</option>
                            <option value="O"> O</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <p>{{ __('Status Pernikahan') }}</p>
                        <select class="form-control form-control-sm" name="marital_status" id="marital_status">
                            <option value=""> -Pilih Status Pernikahan-</option>
                            <option value="Belum Kawin"> Belum Kawin</option>
                            <option value="Kawin"> Kawin</option>
                            <option value="Cerai Hidup"> Cerai Hidup</option>
                            <option value="Cerai Mati"> Cerai Mati</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <p>{{ __('No. HP') }}</p>
                        <input type="text" class="form-control form-control-sm" name="phone" id="phone">
                        <div id="phone-error" class="fv-plugins-message-container invalid-feedback" style="display: block;"></div>
                    </div>

                    <div class="form-group">
                        <p>{{ __('Suku') }}</p>
                        <input type="text" class="form-control form-control-sm" name="ethnic" id="ethnic">
                        <div id="ethnic-error" class="fv-plugins-message-container invalid-feedback" style="display: block;"></div>
                    </div>

                    <div class="form-group">
                        <p>{{ __('Email') }}</p>
                        <input type="email" class="form-control form-control-sm" name="email" id="email">
                    </div>

                    <div class="form-group">
                        <p>{{ __('Pendidikan Terakhir') }}</p>
                        <input type="text" class="form-control form-control-sm" name="education" id="education">
                    </div>

                    <div class="form-group">
                        <p>{{ __('Instagram') }}</p>
                        <input type="text" class="form-control form-control-sm" name="ig" id="ig">
                    </div>

                    <div class="form-group">
                        <p>{{ __('Facebook') }}</p>
                        <input type="text" class="form-control form-control-sm" name="fb" id="fb">
                    </div>

                    <div class="form-group">
                        <p>{{ __('TikTok') }}</p>
                        <input type="text" class="form-control form-control-sm" name="tiktok" id="tiktok">
                    </div>

                    <div class="form-group">
                        <p>{{ __('TMT') }}  <span class="required" style="color: #dd4b39;">*</span></p>
                        <input type="text" class="form-control form-control-sm" name="tmt" id="tmt">
                        <div id="tmt-error" class="fv-plugins-message-container invalid-feedback" style="display: block;"></div>
                    </div>

                    <div class="form-group">
                        <p>{{ __('Unit Kerja') }} <span class="required" style="color: #dd4b39;">*</span></p>
                        <select class="form-control form-control-sm" name="work_unit_id" id="work_unit_id">
                            <option value="">- Pilih Unit Kerja -</option>
                            @foreach($work_unit as $v)
                                <option value="{{ $v->id }}">{{ $v->name }}</option>
                            @endforeach
                        </select>
                        <div id="work_unit_id-error" class="fv-plugins-message-container invalid-feedback" style="display: block;"></div>
                        <div id="work_unit2_id" class="fv-plugins-message-container invalid-feedback" style="display: none;">Unit kerja diganti di Menu Mutasi</div>
                    </div>

                    <div class="form-group">
                        <p>{{ __('File KTP') }}</p>
                        <input type="file" class="form-control form-control-sm" name="file_ktp" id="file_ktp">
                        <span class="text-red" id="show_file_ktp"></span>
                        <div id="file_ktp-error" class="fv-plugins-message-container invalid-feedback" style="display: block;"></div>
                    </div>

                    <div class="form-group">
                        <p>{{ __('File KK') }}</p>
                        <input type="file" class="form-control form-control-sm" name="file_kk" id="file_kk">
                        <span class="text-red" id="show_file_kk"></span>
                        <div id="file_kk-error" class="fv-plugins-message-container invalid-feedback" style="display: block;"></div>
                    </div>

                    <div class="form-group">
                        <p>{{ __('Foto') }}</p>
                        <input type="file" class="form-control form-control-sm" name="photo" id="photo">
                        <span class="text-red" id="show_photo"></span>
                        <div id="photo-error" class="fv-plugins-message-container invalid-feedback" style="display: block;"></div>
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
<script src="{{ asset('backend/assets/js/jquery-3.4.1.min.js')}}"></script>
<script>
$('#exampleModal').on('shown.bs.modal', function () {

    const tmt = document.getElementById('tmt');

    // 🔥 Destroy jika sudah pernah di-init oleh Cork
    if (tmt._flatpickr) {
        tmt._flatpickr.destroy();
    }

    // 🔥 Init ulang Flatpickr secara manual
    flatpickr(tmt, {
        dateFormat: "Y-m-d",
        allowInput: true,
        yearSelectorType: "dropdown",
        clickOpens: true
    });

    // 🔥 WAJIB: buka kunci input
    tmt.removeAttribute('readonly');

});
</script>
