<?php

namespace App\Http\Controllers;

use App\Models\KpiIndicator;
use App\Models\KpiIndicatorItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\DataTables;

class KpiIndicatorItemController extends Controller
{
    public function index($kpi_indicator)
    {
        $title = "Indikator KPI";
        $kpi_indicator = Crypt::decrypt($kpi_indicator);
        $kpi_indicator = KpiIndicator::where('id', $kpi_indicator)->first();
        return view('admin.kpi_indicator_item.index', compact('title', 'kpi_indicator'));
    }


    public function get_kpi_indicator_item_index(Request $request, $kpi_indicator)
    {
        if ($request->ajax()) {
            $counters = 1;

            $kpi_indicator = Crypt::decrypt($kpi_indicator);
            $kpi_indicator = KpiIndicator::where('id', $kpi_indicator)->first();

            $kpi_indicator_item = KpiIndicatorItem::where('kpi_indicator_id', $kpi_indicator->id)->where('id','!=',0)->limit(10);

            return DataTables::of($kpi_indicator_item)
            ->addIndexColumn()
            ->addColumn('number', function () use (&$counters) {
                return $counters++;
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
                'measurement_tool' => 'Alat Ukur/Indikator'
            ];

            if ($action === "Simpan") {
                $rules = [
                    'measurement_tool' => 'required|max:255'
                ];
            } else {
                $rules = [
                    'measurement_tool' => 'required|max:255'
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

            $kpi_indicator_item = new KpiIndicatorItem();
            $kpi_indicator_item->kpi_indicator_id = $request->kpi_indicator_id;
            $kpi_indicator_item->measurement_tool = $request->measurement_tool;
            $kpi_indicator_item->physical_evidence = $request->physical_evidence;
            $kpi_indicator_item->save();
            activity()->log('Create Indikator KPI Data');
            return response()->json(['success' => true, 'message' => 'Tambah Indikator KPI Berhasil']);
        }
    }

    ## Get Indikator KPI
    public function edit(Request $request, KpiIndicatorItem $kpi_indicator_item)
    {
        if ($request->ajax()) {
            return response()->json(['success' => true, 'data' => $kpi_indicator_item]);
        }
    }

    ## Edit Indikator KPI
    public function update(Request $request, KpiIndicatorItem $kpi_indicator_item)
    {
        if ($request->ajax()) {

            $kpi_indicator_item->kpi_indicator_id = $request->kpi_indicator_id;
            $kpi_indicator_item->measurement_tool = $request->measurement_tool;
            $kpi_indicator_item->physical_evidence = $request->physical_evidence;
            $kpi_indicator_item->save();

            activity()->log('Edit Indikator KPI Data With ID = ' . $kpi_indicator_item->id);
            return response()->json(['success' => true, 'message' => 'Ubah Indikator KPI Berhasil']);
        }
    }

    ## Delete Indikator KPI
    public function delete(Request $request, KpiIndicatorItem $kpi_indicator_item)
    {
        if ($request->ajax()) {
            $kpi_indicator_item->delete();
            activity()->log('Delete Indikator KPI Data With ID = ' . $kpi_indicator_item->id);
            return response()->json(['success' => true, 'message' => 'Hapus Indikator KPI Berhasil']);
        }
    }

}
