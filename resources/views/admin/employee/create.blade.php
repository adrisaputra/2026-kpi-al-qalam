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
                        <p>{{ __('Unit Kerja') }}</p>
                        <input type="text" class="form-control form-control-sm" name="work_unit_name" id="work_unit_name">
                    </div>

                    <div class="form-group">
                        <p>{{ __('Kategori KPI') }} <span class="required" style="color: #dd4b39;">*</span></p>
                        <select class="form-control form-control-sm" name="kpi_category_id" id="kpi_category_id" onchange="getKpi()">
                            <option value="">- Pilih -</option>
                            @foreach($kpi_category as $v)
                                <option value="{{ $v->id }}">{{ $v->name }}</option>
                            @endforeach
                        </select>
                        <div id="kpi_category_id-error" class="fv-plugins-message-container invalid-feedback" style="display: block;"></div>
                    </div>

                    <div class="form-group">
                        <p>{{ __('KPI') }} <span class="required" style="color: #dd4b39;">*</span></p>
                        <select class="form-control form-control-sm" name="kpi_id" id="kpi_id">
                            <option value="">- Pilih -</option>
                        </select>
                        <div id="kpi_id-error" class="fv-plugins-message-container invalid-feedback" style="display: block;"></div>
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
    function getKpi(){
        kpi_category_id = document.getElementById("kpi_category_id").value;
        var url = "{{ url('/kpi/get') }}";
        $.ajax({
            url: url + "/" + kpi_category_id,
            success: function(response){
                $("#kpi_id").html(response);
            }
        });
        return false;
    }
</script>
