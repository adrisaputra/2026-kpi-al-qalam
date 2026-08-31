<?php

namespace App\Http\Controllers;

use App\Models\Kpi;
use App\Models\KpiIndicator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\DataTables;

class KpiIndicatorController extends Controller
{
    public function index($kpi)
    {
        $title = "Indikator KPI";
        $kpi = Crypt::decrypt($kpi);
        $kpi = Kpi::where('id', $kpi)->first();
        return view('admin.kpi_indicator.index', compact('title', 'kpi'));
    }


    public function get_kpi_indicator_index(Request $request, $kpi)
    {
        if ($request->ajax()) {
            $counters = 1;

            $kpi = Crypt::decrypt($kpi);
            $kpi = Kpi::where('id', $kpi)->first();

            $kpi_indicator = KpiIndicator::where('kpi_id', $kpi->id)->where('id','!=',0)->limit(10);

            return DataTables::of($kpi_indicator)
            ->addIndexColumn()
            ->addColumn('number', function () use (&$counters) {
                return $counters++;
            })
            ->addColumn('kpi_indicator_item', function ($v) {
                $url = url('kpi_indicator_item', Crypt::encrypt($v->id));
                $btn = '<a href="' . $url . '"  target="_blank" class="btn btn-info btn-sm position-relative me-5" data-toggle="tooltip" data-placement="top" title="Data">
                            <span>Lihat Indikator Item</span>';

                    if ($v->kpi_indicator_items->count() > 0) {
                        $btn .= '<span class="badge badge-danger counter">'.$v->kpi_indicator_items->count().'</span>';

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
            ->rawColumns(['kpi_indicator_item', 'action'])
            ->make(true);
        }
    }

    public function validate(Request $request, $action)
    {
        if ($request->ajax()) {

            $attributes = [
                'name' => 'Nama Indikator KPI'
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

    ## Save Indikator KPI 
    public function store(Request $request)
    {
        if ($request->ajax()) {

            $kpi_indicator = new KpiIndicator();
            $kpi_indicator->kpi_id = $request->kpi_id;
            $kpi_indicator->name = $request->name;
            $kpi_indicator->indicator = $request->indicator;
            $kpi_indicator->target = $request->target;
            $kpi_indicator->weight = $request->weight;
            $kpi_indicator->save();
            activity()->log('Create Indikator KPI Data');
            return response()->json(['success' => true, 'message' => 'Tambah Indikator KPI Berhasil']);
        }
    }

    ## Get Indikator KPI
    public function edit(Request $request, KpiIndicator $kpi_indicator)
    {
        if ($request->ajax()) {
            return response()->json(['success' => true, 'data' => $kpi_indicator]);
        }
    }

    ## Edit Indikator KPI
    public function update(Request $request, KpiIndicator $kpi_indicator)
    {
        if ($request->ajax()) {

            $kpi_indicator->kpi_id = $request->kpi_id;
            $kpi_indicator->name = $request->name;
            $kpi_indicator->indicator = $request->indicator;
            $kpi_indicator->target = $request->target;
            $kpi_indicator->weight = $request->weight;
            $kpi_indicator->save();

            activity()->log('Edit Indikator KPI Data With ID = ' . $kpi_indicator->id);
            return response()->json(['success' => true, 'message' => 'Ubah Indikator KPI Berhasil']);
        }
    }

    ## Delete Indikator KPI
    public function delete(Request $request, KpiIndicator $kpi_indicator)
    {
        if ($request->ajax()) {
            $kpi_indicator->delete();
            activity()->log('Delete Indikator KPI Data With ID = ' . $kpi_indicator->id);
            return response()->json(['success' => true, 'message' => 'Hapus Indikator KPI Berhasil']);
        }
    }

}
