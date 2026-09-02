<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\DataTables;

class ReportController extends Controller
{
    public function index($report_category)
    {
        $title = "Rapor";
        $report_category = Crypt::decrypt($report_category);
        $report_category = ReportCategory::where('id', $report_category)->first();
        return view('admin.report.index', compact('title', 'report_category'));
    }

    public function get_report_index(Request $request, $report_category)
    {
        if ($request->ajax()) {
            $counters = 1;

            $report_category = Crypt::decrypt($report_category);
            $report_category = ReportCategory::where('id', $report_category)->first();

            $report = Report::where('report_category_id', $report_category->id)->where('id','!=',0)->limit(10);

            return DataTables::of($report)
            ->addIndexColumn()
            ->addColumn('number', function () use (&$counters) {
                return $counters++;
            })
            ->addColumn('is_special_value', function ($v) {
                if($v->is_special_value==true){
                    $status ='<span class="badge badge-success">Ya</span>';
                }else{
                    $status ='<span class="badge badge-danger">Tidak</span>';
                }
                return $status;
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
            ->rawColumns(['is_special_value','action'])
            ->make(true);
        }
    }

    public function validate(Request $request, $action)
    {
        if ($request->ajax()) {

            $attributes = [
                'name' => 'Nama Item Penilaian'
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

    ## Save Report 
    public function store(Request $request)
    {
        if ($request->ajax()) {

            $report = new Report();
            $report->report_category_id = $request->report_category_id;
            $report->name = $request->name;
            $report->is_special_value = $request->is_special_value;
            $report->save();
            activity()->log('Create Report Data');
            return response()->json(['success' => true, 'message' => 'Tambah Report Berhasil']);
        }
    }

    ## Get Report
    public function edit(Request $request, Report $report)
    {
        if ($request->ajax()) {
            return response()->json(['success' => true, 'data' => $report]);
        }
    }

    ## Edit Report
    public function update(Request $request, Report $report)
    {
        if ($request->ajax()) {

            $report->report_category_id = $request->report_category_id;
            $report->name = $request->name;
            $report->is_special_value = $request->has('is_special_value') ? 1 : 0;
            $report->save();

            activity()->log('Edit Report Data With ID = ' . $report->id);
            return response()->json(['success' => true, 'message' => 'Ubah Report Berhasil']);
        }
    }

    ## Delete Report
    public function delete(Request $request, Report $report)
    {
        if ($request->ajax()) {
            $report->delete();
            activity()->log('Delete Report Data With ID = ' . $report->id);
            return response()->json(['success' => true, 'message' => 'Hapus Report Berhasil']);
        }
    }

    // ## Get Data
    public function get($report_category, $report_id = NULL)
    {
        $report = Report::where('report_category_id', $report_category)
            ->orderBy('id', 'ASC')->get();

        echo "<option value=''>- Pilih Report -</option>";
        foreach ($report as $v) {
            if ($report_id) {
                if ($report_id == $v->id) {
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
