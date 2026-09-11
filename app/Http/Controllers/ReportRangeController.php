<?php

namespace App\Http\Controllers;

use App\Models\ReportCategory;
use App\Models\ReportRange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\DataTables;

class ReportRangeController extends Controller
{
    public function index($report_category)
    {
        $title = "Range Rapor";
        $report_category = Crypt::decrypt($report_category);
        $report_category = ReportCategory::where('id', $report_category)->first();
        return view('admin.report_range.index', compact('title', 'report_category'));
    }

    public function get_report_range_index(Request $request, $report_category)
    {
        if ($request->ajax()) {
            $counters = 1;

            $report_category = Crypt::decrypt($report_category);
            $report_category = ReportCategory::where('id', $report_category)->first();

            $report_range = ReportRange::where('report_category_id', $report_category->id)->limit(10);

            return DataTables::of($report_range)
            ->addIndexColumn()
            ->addColumn('number', function () use (&$counters) {
                return $counters++;
            })
            ->addColumn('category', function ($v) {
                if ($v->category == 1) {
                    $category = '<span class="badge badge-success">Inputan Pilihan 0 dan 1</span>';
                } elseif ($v->category == 2) {
                    $category = '<span class="badge badge-primary">Inputan Pilihan 0, 1 dan 2</span>';
                } elseif ($v->category == 3) {
                    $category = '<span class="badge badge-danger">Inputan Manual</span>';
                } elseif ($v->category == 4) {
                    $category = '<span class="badge badge-warning">Inputan Manual + Rumus</span>';
                } elseif ($v->category == 5) {
                    $category = '<span class="badge badge-info">Inputan File Gambar</span>';
                } else {
                    $category = '<span class="badge badge-default">-</span>';
                }
                return $category;
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
            ->rawColumns(['category','action'])
            ->make(true);
        }
    }

    public function validate(Request $request, $action)
    {
        if ($request->ajax()) {

            $attributes = [
                'name' => 'Nama',
                'min_value' => 'Nilai Min',
                'max_value' => 'Nilai Max',
                'score' => 'Skor'
            ];

            if ($action === "Simpan") {
                $rules = [
                    'name' => 'required|max:255',
                    'min_value' => 'numeric|nullable',
                    'max_value' => 'numeric|nullable',
                    'score' => 'numeric|nullable',
                ];
            } else {
                $rules = [
                    'name' => 'required|max:255',
                    'min_value' => 'numeric|nullable',
                    'max_value' => 'numeric|nullable',
                    'score' => 'numeric|nullable',
                ];
            }

            $request->validate($rules, [], $attributes);

            return response()->json(['success' => true]);
        }
    }

    ## Save Report Range 
    public function store(Request $request)
    {
        if ($request->ajax()) {

            $report_range = new ReportRange();
            $report_range->report_category_id = $request->report_category_id;
            $report_range->name = $request->name;
            $report_range->min_value = $request->min_value;
            $report_range->max_value = $request->max_value;
            $report_range->score = $request->score;
            $report_range->save();

            activity()->log('Create Report Range Data');
            return response()->json(['success' => true, 'message' => 'Tambah Range Rapor Berhasil']);
        }
    }

    ## Get ReportRange
    public function edit(Request $request, ReportRange $report_range)
    {
        if ($request->ajax()) {
            return response()->json(['success' => true, 'data' => $report_range]);
        }
    }

    ## Edit Report Range
    public function update(Request $request, ReportRange $report_range)
    {
        if ($request->ajax()) {

            $report_range->report_category_id = $request->report_category_id;
            $report_range->name = $request->name;
            $report_range->min_value = $request->min_value;
            $report_range->max_value = $request->max_value;
            $report_range->score = $request->score;
            $report_range->save();

            activity()->log('Edit Report Range Data With ID = ' . $report_range->id);
            return response()->json(['success' => true, 'message' => 'Ubah Range Rapor Berhasil']);
        }
    }

    ## Delete Report Range
    public function delete(Request $request, ReportRange $report_range)
    {
        if ($request->ajax()) {
            $report_range->delete();
            activity()->log('Delete Report Range Data With ID = ' . $report_range->id);
            return response()->json(['success' => true, 'message' => 'Hapus Range Rapor Berhasil']);
        }
    }

}
