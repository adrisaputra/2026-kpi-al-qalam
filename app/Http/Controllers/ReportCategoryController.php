<?php

namespace App\Http\Controllers;

use App\Models\ReportCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\DataTables;

class ReportCategoryController extends Controller
{
    ## Show Data
    public function index()
    {
        $title = "Kategori Rapor ";
        return view('admin.report_category.index',compact('title'));
    }

    ## Get Data
    public function get_report_category_index(Request $request)
    {

        if ($request->ajax()) {
            $counter = 1;

            $report_category = ReportCategory::limit(10);

            return DataTables::of($report_category)
            ->addIndexColumn()
            ->addColumn('number', function () use (&$counter) {
                return $counter++;
            })
            ->addColumn('report', function ($v) {
                $url = url('report', Crypt::encrypt($v->id));
                $btn = '<a href="' . $url . '" class="btn btn-info btn-sm position-relative me-5" data-toggle="tooltip" data-placement="top" title="Data">
                            <span>Lihat Rapor</span>';

                    if ($v->reports->count() > 0) {
                        $btn .= '<span class="badge badge-danger counter">'.$v->reports->count().'</span>';

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
            ->rawColumns(['report','action'])->make(true);
        }
        
    }

    public function validate(Request $request, $action)
    {

        if ($request->ajax()) {

            $attributes = [
                'name' => 'Nama Kategori Rapor '
            ];

            if($action==="Simpan"){
                $rules = [
                    'name' => 'required|max:255'
                ];
            } else {
                $rules = [
                    'name' => 'required|max:255'
                ];
            }
            
            $request->validate($rules, [],$attributes);
            
            return response()->json(['success' => true]);
        }
    }

    ## Save Data
    public function store(Request $request)
    {
        if ($request->ajax()) {
            $report_category = New ReportCategory();
            $report_category->name = $request->name;
            $report_category->save();
            
            activity()->log('Create Data Rapor Category');
            return response()->json(['success' => true,'message' => 'Tambah Data Berhasil']);
        }
    }

    ## Get Data
    public function edit(Request $request, ReportCategory $report_category)
    {
        if ($request->ajax()) {
            return response()->json(['success' => true,'data' => $report_category]);
        }
    }

    ## Edit Data
    public function update(Request $request, ReportCategory $report_category)
    {
        if ($request->ajax()) {
            $report_category->name = $request->name;
            $report_category->save();
    
            activity()->log('Edit Data Rapor Category With ID = '.$report_category->id);
            return response()->json(['success' => true,'message' => 'Ubah Data Berhasil']);
        }
    }

    ## Delete Data
    public function delete(Request $request, ReportCategory $report_category)
    {
        if ($request->ajax()) {
            $report_category->delete();
            activity()->log('Delete Data Rapor Category With ID = '.$report_category->id);
            return response()->json(['success' => true,'message' => 'Hapus Data Berhasil']);
        }
    }

}
