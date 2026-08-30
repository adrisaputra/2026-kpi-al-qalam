<?php

namespace App\Http\Controllers;

use App\Models\Kpi;
use App\Models\KpiCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\DataTables;

class KpiController extends Controller
{
    public function index($kpi_category)
    {
        $title = "KPI";
        $kpi_category = Crypt::decrypt($kpi_category);
        $kpi_category = KpiCategory::where('id', $kpi_category)->first();
        return view('admin.kpi.index', compact('title', 'kpi_category'));
    }


    public function get_kpi_index(Request $request, $kpi_category)
    {
        if ($request->ajax()) {
            $counters = 1;

            $kpi_category = Crypt::decrypt($kpi_category);
            $kpi_category = KpiCategory::where('id', $kpi_category)->first();

            $kpi = Kpi::where('kpi_category_id', $kpi_category->id)->where('id','!=',0)->limit(10);

            return DataTables::of($kpi)
            ->addIndexColumn()
            ->addColumn('number', function () use (&$counters) {
                return $counters++;
            })
            ->addColumn('kpi_indicator', function ($v) {
                $url = url('kpi_indicator', Crypt::encrypt($v->id));
                $btn = '<a href="' . $url . '" class="btn btn-info btn-sm position-relative me-5" data-toggle="tooltip" data-placement="top" title="Data">
                            <span>Lihat Indikator KPI</span>';

                    if ($v->kpi_indicators->count() > 0) {
                        $btn .= '<span class="badge badge-danger counter">'.$v->kpi_indicators->count().'</span>';

                    }

                    $btn .= '</a>';
                    
                return $btn;
            })
            ->addColumn('action', function ($v) {
                $btn = '<a href="#" onClick="getData('.$v->id.')" id="'.$v->id.'" title="Edit" data-toggle="modal" data-target="#exampleModal">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 text-success"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
                        </a>';
                $btn .= '<a href="#" onclick="deleteData('.$v->id.')" id="'.$v->id.'" class="warning confirm" data-toggle="tooltip" data-placement="top" title="Hapus">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 text-danger"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                        </a>';
                return $btn;
            })
            ->rawColumns(['kpi_indicator', 'action'])
            ->make(true);
        }
    }

    public function validate(Request $request, $action)
    {
        if ($request->ajax()) {

            $attributes = [
                'name' => 'Nama KPI'
            ];

            if ($action === "Simpan") {
                $rules = [
                    'name' => 'required|max:255'
                ];
            } else {
                $rules = [
                    'name' => 'required|max:255'
                ];
            }

            $request->validate($rules, [], $attributes);

            return response()->json(['success' => true]);
        }
    }

    ## Save KPI 
    public function store(Request $request)
    {
        if ($request->ajax()) {

            $kpi = new Kpi();
            $kpi->kpi_category_id = $request->kpi_category_id;
            $kpi->name = $request->name;
            $kpi->save();
            activity()->log('Create KPI Data');
            return response()->json(['success' => true, 'message' => 'Tambah KPI Berhasil']);
        }
    }

    ## Get KPI
    public function edit(Request $request, Kpi $kpi)
    {
        if ($request->ajax()) {
            return response()->json(['success' => true, 'data' => $kpi]);
        }
    }

    ## Edit KPI
    public function update(Request $request, Kpi $kpi)
    {
        if ($request->ajax()) {

            $kpi->kpi_category_id = $request->kpi_category_id;
            $kpi->name = $request->name;
            $kpi->save();

            activity()->log('Edit KPI Data With ID = ' . $kpi->id);
            return response()->json(['success' => true, 'message' => 'Ubah KPI Berhasil']);
        }
    }

    ## Delete KPI
    public function delete(Request $request, Kpi $kpi)
    {
        if ($request->ajax()) {
            $kpi->delete();
            activity()->log('Delete KPI Data With ID = ' . $kpi->id);
            return response()->json(['success' => true, 'message' => 'Hapus KPI Berhasil']);
        }
    }

    // ## Get Data
    public function get($kpi_category, $kpi_id = NULL)
    {
        $kpi = Kpi::where('kpi_category_id', $kpi_category)
            ->orderBy('id', 'ASC')->get();

        echo "<option value=''>- Pilih KPI -</option>";
        foreach ($kpi as $v) {
            if ($kpi_id) {
                if ($kpi_id == $v->id) {
                    echo "<option value='" . $v->id . "' selected>" . $v->name . "</option>";
                } else {
                    echo "<option value='" . $v->id . "' >" . $v->name . "</option>";
                }
            } else {
                echo "<option value='" . $v->id . "' >" . $v->name . "</option>";
            }
        }
    }
}
