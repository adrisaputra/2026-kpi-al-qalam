<?php

namespace App\Http\Controllers;

use App\Models\EmployeeReport;
use App\Models\EmployeeReportFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Yajra\DataTables\DataTables;

class EmployeeReportFileController extends Controller
{
    public function index($employee_report)
    {
        $title = "File";
        $employee_report = Crypt::decrypt($employee_report);
        $employee_report = EmployeeReport::where('id', $employee_report)->first();
        $employee_report_file = EmployeeReportFile::where('id', $employee_report->id)->first();
        return view('admin.employee_report_file.index', compact('title', 'employee_report', 'employee_report_file'));
    }

    public function get_employee_report_file_index(Request $request, $employee_report)
    {
        if ($request->ajax()) {
            $counters = 1;

            $employee_report_file = EmployeeReportFile::where('employee_report_id', $employee_report)->get();

            return DataTables::of($employee_report_file)
                ->addIndexColumn()
                ->addColumn('number', function () use (&$counters) {
                    return $counters++;
                })
                ->addColumn('display_image', function ($v) {
                    $url_image = asset('storage/upload/employee_report_file/' . $v->image);
                    $image = '<a href=' . $url_image . ' target="_blank"><img src=' . $url_image . ' width="100%"></a>';
                    return $image;
                })
                ->addColumn('action', function ($v) {
                    $btn = '<a href="#" onClick="getData(' . $v->id . ')" id="' . $v->id . '" title="Edit" data-toggle="modal" data-target="#exampleModal">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 text-success"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
                        </a>';
                    $btn .= '<a href="#" onclick="deleteData(' . $v->id . ')" id="' . $v->id . '" class="warning confirm" data-toggle="tooltip" data-placement="top" title="Hapus">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 text-danger"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                        </a>';
                    return $btn;
                })
                ->rawColumns(['display_image','action'])
                ->make(true);
        }
    }
    public function validate(Request $request, $action)
    {

        if ($request->ajax()) {

            $attributes = [
                'image'  => 'Gambar',
                'desc' => 'Keterangan'
            ];

            if ($action === "Simpan") {
                $rules = [
                    'image' => 'required|image',
                    'desc' => 'string|max:255'
                ];
            } else {
                $rules = [
                    'image' => 'required|image',
                    'desc' => 'string|max:255'
                ];
            }

            $request->validate($rules, [], $attributes);

            return response()->json(['success' => true]);
        }
    }

    ## Save Data
    public function store(Request $request)
    {
        if ($request->ajax()) {
            $employee_report_file = new EmployeeReportFile();
            $employee_report_file->employee_report_id = $request->employee_report_id;

            if ($request->hasFile('image')) {

                $file = $request->file('image');

                $fileName = time() . '.webp';

                // Ukuran maksimal
                // $width = 1938;
                // $height = 1028;

                // Buat Image Manager - Intervention Image 4.x
                $manager = ImageManager::usingDriver(Driver::class);

                // Decode file upload
                $image = $manager->decode($file);

                // Resize dengan mempertahankan aspect ratio
                // $image->scaleDown(
                //     width: $width,
                //     height: $height
                // );

                // Encode menjadi WebP quality 75
                $encoded = $image->encodeUsingFileExtension(
                    'webp',
                    quality: 75
                );

                // Simpan ke storage
                Storage::put(
                    'upload/employee_report_file/' . $fileName,
                    (string) $encoded
                );

                // Simpan nama file ke database
                $employee_report_file->image = $fileName;
            }

            $employee_report_file->desc = $request->desc;
            $employee_report_file->save();

            activity()->log('Create Data Employee Report File');
            return response()->json(['success' => true, 'message' => 'Tambah Data Berhasil']);
        }
    }

    ## Get Data
    public function edit(Request $request, EmployeeReportFile $employee_report_file)
    {
        if ($request->ajax()) {
            return response()->json(['success' => true, 'data' => $employee_report_file]);
        }
    }

    ## Edit Data
    public function update(Request $request, EmployeeReportFile $employee_report_file)
    {
        if ($request->ajax()) {
            if ($request->hasFile('image')) {

                // Simpan nama file lama
                $oldImage = $employee_report_file->image;

                $file = $request->file('image');
                $fileName = time() . '.webp';

                // Ukuran maksimal
                // $width = 1938;
                // $height = 1028;

                // Image Manager Intervention Image 4.x
                $manager = ImageManager::usingDriver(Driver::class);

                // Decode file upload
                $image = $manager->decode($file);

                // Resize dengan mempertahankan aspect ratio
                // dan tidak memperbesar gambar yang lebih kecil
                // $image->scaleDown(
                //     width: $width,
                //     height: $height
                // );

                // Encode ke WebP kualitas 75
                $encoded = $image->encodeUsingFileExtension(
                    'webp',
                    quality: 75
                );

                // Path storage
                $path = 'upload/employee_report_file/' . $fileName;

                // Simpan file baru
                Storage::put($path, (string) $encoded);

                // Pastikan file benar-benar tersimpan
                if (Storage::exists($path)) {

                    // Update nama file di database
                    $employee_report_file->image = $fileName;

                    // Hapus file lama
                    if ($oldImage) {
                        Storage::delete('upload/employee_report_file/' . $oldImage);
                    }
                }
            }
            $employee_report_file->desc = $request->desc;
            $employee_report_file->save();

            activity()->log('Edit Data Employee Report File With ID = ' . $employee_report_file->id);
            return response()->json(['success' => true, 'message' => 'Ubah Data Berhasil']);
        }
    }

    ## Delete Data
    public function delete(Request $request, EmployeeReportFile $employee_report_file)
    {
        if ($request->ajax()) {
            $image = $employee_report_file->image;
            $employee_report_file->delete();

            if ($image) {
                Storage::delete('upload/employee_report_file/' . $image);
            }
            activity()->log('Delete Data Employee Report File With ID = ' . $employee_report_file->id);
            return response()->json(['success' => true, 'message' => 'Hapus Data Berhasil']);
        }
    }
}
