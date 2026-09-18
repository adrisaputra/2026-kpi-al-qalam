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
                    <input type="hidden" class="form-control form-control-sm" name="id" id="id_employee_kpi" />
                    <input type="hidden" class="form-control form-control-sm" name="employee_id" id="employee_id" value="{{ $employee->id }}"/>

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

                    <div class="form-group" style="margin-bottom: 0rem;">
                        <p>{{ __('Bulan') }} <span class="required" style="color: #dd4b39;">*</span></p>
                        <select id="month" name="month" class="basic form-control form-control-sm" style="margin-bottom: 0rem;">
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
                        <div id="month-error" class="fv-plugins-message-container invalid-feedback" style="display: block;"></div>
                    </div>

                    <div class="form-group">
                        <p>{{ __('Tahun') }} <span class="required" style="color: #dd4b39;">*</span></p>
                        <select id="year" name="year" class="basic form-control form-control-sm">
                            @for($i=2026;$i<=date('Y');$i++)
                                <option value="{{ $i }}" @if(date('Y')==$i) selected @endif>{{ $i }}</option>
                            @endfor
                        </select>
                        <div id="year-error" class="fv-plugins-message-container invalid-feedback" style="display: block;"></div>
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
