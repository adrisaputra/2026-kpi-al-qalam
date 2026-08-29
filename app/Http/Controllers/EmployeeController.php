<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use App\Models\Employee;
use App\Models\ParentHistory;
use App\Models\PartnerHistory;
use App\Models\PeriodizationHistory;
use App\Models\User;
use App\Models\WorkUnit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class EmployeeController extends Controller
{
    ## Show Data
    public function index()
    {
        $title = "Pegawai Aktif";
        $work_unit = WorkUnit::get();
        return view('admin.employee.index', compact('title', 'work_unit'));
    }

    ## Get Data
    public function get_employee_index(Request $request)
    {

        if ($request->ajax()) {
            $counter = 1;

            $query = Employee::query()
                    ->leftJoin('work_units', 'work_units.id', '=', 'employees.work_unit_id')
                    ->select('employees.*', 'work_units.name as work_unit_name');
        
            // Filter by office
            if ($request->has('get_work_unit') && !empty($request->input('get_work_unit'))) {
                $get_work_unit = $request->input('get_work_unit');
                $query->whereHas('work_unit', function($q) use ($get_work_unit) {
                    $q->where('id', $get_work_unit);
                });
            }

            $employee = $query->limit(10);

            // $employee = Employee::with('work_unit')->limit(10);

            return DataTables::of($employee)
                ->addIndexColumn()
                ->addColumn('number', function () use (&$counter) {
                    return $counter++;
                })
                ->addColumn('name_display', function ($v) {
                    return 'NIK : ' . $v->nik . '<br><b>' . $v->name . '</b>';
                })
                ->addColumn('education', function ($v) {
                    // return $v->last_education_history?->major;
                    return $v->education;
                })
                ->addColumn('photo', function ($v) {
                    $url_photo = asset('storage/upload/employee/' . $v->photo);
                    if ($v->photo) {
                        $photo = '<img src=' . $url_photo . ' height="200px">';
                    } else {
                        $photo = NULL;
                    }
                    return $photo;
                })
                ->addColumn('action', function ($v) {
                    $kpi = url('kpi', Crypt::encrypt($v->id));
                    $rapor = url('rapor', Crypt::encrypt($v->id));
                    $btn = '<a href="'.$kpi.'" class="btn btn-sm mb-2 mr-1 btn-success" title="KPI">
                                KPI
                            </a>';
                    $btn .= '<a href="'.$rapor.'" class="btn btn-sm mb-2 mr-1 btn-info" title="Rapor">
                                Rapor
                            </a>';
                    return $btn;
                })
                ->filterColumn('name', function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%")
                            ->orWhere('nik', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('work_unit', function ($query, $keyword) {
                    $query->whereHas('work_unit', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                })
                ->rawColumns(['name_display', 'photo', 'action'])->make(true);
        }
    }

    public function validate(Request $request, $action)
    {

        if ($request->ajax()) {

            $attributes = [
                'name' => 'Nama',
                'nik' => 'NIK',
                'phone' => 'No. HP',
                'file_ktp' => 'File KTP',
                'file_kk' => 'File KK',
                'photo' => 'Foto',
                'tmt' => 'TMT',
                'work_unit_id' => 'Unit Kerja'
            ];

            if ($action === "Simpan") {
                $rules = [
                    'name' => 'required',
                    'nik' => 'required|numeric|digits:16',
                    'phone' => 'nullable|numeric',
                    'file_ktp' => 'mimes:jpg,jpeg,png,pdf',
                    'file_kk' => 'mimes:jpg,jpeg,png,pdf',
                    'photo' => 'mimes:jpg,jpeg,png,pdf',
                    'tmt' => 'required',
                    'work_unit_id' => 'required'
                ];
            } else {
                $rules = [
                    'name' => 'required',
                    'nik' => 'required|numeric|digits:16',
                    'phone' => 'nullable|numeric',
                    'file_ktp' => 'mimes:jpg,jpeg,png,pdf',
                    'file_kk' => 'mimes:jpg,jpeg,png,pdf',
                    'photo' => 'mimes:jpg,jpeg,png,pdf',
                    'tmt' => 'required'
                ];
            }

            $request->validate($rules, [], $attributes);

            return response()->json(['success' => true]);
        }
    }

    public function validate2(Request $request)
    {

        if ($request->ajax()) {

            $attributes = [
                'phone' => 'No. HP',
                'file_ktp' => 'File KTP',
                'file_kk' => 'File KK',
                'photo' => 'Foto'
            ];

            $rules = [
                'phone' => 'nullable|numeric',
                'file_ktp' => 'mimes:jpg,jpeg,png,pdf',
                'file_kk' => 'mimes:jpg,jpeg,png,pdf',
                'photo' => 'mimes:jpg,jpeg,png,pdf'
            ];

            $request->validate($rules, [], $attributes);

            return response()->json(['success' => true]);
        }
    }

    ## Save Data
    public function store(Request $request)
    {
        if ($request->ajax()) {
            DB::beginTransaction();
            try {

                $employee = new Employee();
                $employee->fill($request->all());

                if ($request->file_ktp) {
                    $employee->file_ktp = '1' . time() . '.' . $request->file_ktp->getClientOriginalExtension();
                    Storage::putFileAs('upload/employee', $request->file('file_ktp'), $employee->file_ktp);
                }

                if ($request->file_kk) {
                    $employee->file_kk = '2' . time() . '.' . $request->file_kk->getClientOriginalExtension();
                    Storage::putFileAs('upload/employee', $request->file('file_kk'), $employee->file_kk);
                }

                if ($request->photo) {
                    $employee->photo = '3' . time() . '.' . $request->photo->getClientOriginalExtension();
                    Storage::putFileAs('upload/employee', $request->file('photo'), $employee->photo);
                }

                $employee->save();

                $partner_history = new PartnerHistory();
                $partner_history->employee_id = $employee->id;
                $partner_history->save();

                foreach (['Mom', 'Dad'] as $category) {
                    $parent_history = new ParentHistory();
                    $parent_history->employee_id = $employee->id;
                    $parent_history->category = $category;
                    $parent_history->save();
                }

                $periodization_history = new PeriodizationHistory();
                $periodization_history->employee_id = $employee->id;
                $periodization_history->date_periodization = Carbon::parse($request->tmt)->addYears(1)->addMonths(3);
                $tmt_check = Carbon::parse($employee->tmt)->addYears(1)->addMonths(3);
                $status = $tmt_check->lte(Carbon::now()) ? 'No Pending' : 'Not Yet Time';
                $periodization_history->status = $status;
                $periodization_history->save();

                $user = new User();
                $user->name = $request->nik;
                $user->email = $request->nik . '@gmail.com';
                $user->email_verified_at = date('Y-m-d H:i:s');
                $user->password = Hash::make($request->nik);
                $user->group_id = 3;
                $user->employee_id = $employee->id;
                $user->status = 'Active';
                $user->save();

                DB::commit();

                activity()->log('Create Data Employee');
                return response()->json(['success' => true, 'message' => 'Tambah Data Berhasil']);
            } catch (\Exception $e) {
                DB::rollBack(); // Rollback semua jika terjadi error

                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan data.',
                    'error' => $e->getMessage()
                ], 500);
            }
        }
    }

    ## Get Data
    public function edit(Request $request, $id)
    {
        if ($request->ajax()) {
            $employee = Employee::where('id', $id)->first();
            return response()->json(['success' => true, 'data' => $employee]);
        }
    }

    ## Edit Data
    public function update(Request $request, Employee $employee)
    {
        if ($request->ajax()) {
            $employee->name = $request->name;
            $employee->nik = $request->nik;
            $employee->niy = $request->niy;
            $employee->birthplace = $request->birthplace;
            $employee->birthdate = $request->birthdate;
            $employee->gender = $request->gender;
            $employee->address = $request->address;
            $employee->religion = $request->religion;
            $employee->blood_type = $request->blood_type;
            $employee->marital_status = $request->marital_status;
            $employee->phone = $request->phone;
            $employee->ethnic = $request->ethnic;
            $employee->email = $request->email;
            $employee->education = $request->education;
            $employee->ig = $request->ig;
            $employee->fb = $request->fb;
            $employee->tiktok = $request->tiktok;
            $employee->tmt = $request->tmt;

            if ($employee->file_ktp && $request->file('file_ktp') != "") {
                Storage::delete('upload/employee/' . $employee->file_ktp);
            }

            if ($request->file('file_ktp')) {
                $employee->file_ktp = '1' . time() . '.' . $request->file_ktp->getClientOriginalExtension();
                Storage::putFileAs('upload/employee', $request->file('file_ktp'), $employee->file_ktp);
            }

            if ($employee->file_kk && $request->file('file_kk') != "") {
                Storage::delete('upload/employee/' . $employee->file_kk);
            }

            if ($request->file('file_kk')) {
                $employee->file_kk = '2' . time() . '.' . $request->file_kk->getClientOriginalExtension();
                Storage::putFileAs('upload/employee', $request->file('file_kk'), $employee->file_kk);
            }

            if ($employee->photo && $request->file('photo') != "") {
                Storage::delete('upload/employee/' . $employee->photo);
            }

            if ($request->file('photo')) {
                $employee->photo = '3' . time() . '.' . $request->photo->getClientOriginalExtension();
                Storage::putFileAs('upload/employee', $request->file('photo'), $employee->photo);
            }

            $employee->save();

            activity()->log('Edit Data Employee With ID = ' . $employee->id);
            return response()->json(['success' => true, 'message' => 'Ubah Data Berhasil']);
        }
    }

    ## Edit Data
    public function update2(Request $request, Employee $employee)
    {
        if ($request->ajax()) {
            $employee->birthplace = $request->birthplace;
            $employee->birthdate = $request->birthdate;
            $employee->gender = $request->gender;
            $employee->address = $request->address;
            $employee->religion = $request->religion;
            $employee->blood_type = $request->blood_type;
            $employee->marital_status = $request->marital_status;
            $employee->phone = $request->phone;
            $employee->ethnic = $request->ethnic;
            $employee->email = $request->email;
            $employee->education = $request->education;
            $employee->ig = $request->ig;
            $employee->fb = $request->fb;
            $employee->tiktok = $request->tiktok;

            if ($employee->file_ktp && $request->file('file_ktp') != "") {
                Storage::delete('upload/employee/' . $employee->file_ktp);
            }

            if ($request->file('file_ktp')) {
                $employee->file_ktp = '1' . time() . '.' . $request->file_ktp->getClientOriginalExtension();
                Storage::putFileAs('upload/employee', $request->file('file_ktp'), $employee->file_ktp);
            }

            if ($employee->file_kk && $request->file('file_kk') != "") {
                Storage::delete('upload/employee/' . $employee->file_kk);
            }

            if ($request->file('file_kk')) {
                $employee->file_kk = '2' . time() . '.' . $request->file_kk->getClientOriginalExtension();
                Storage::putFileAs('upload/employee', $request->file('file_kk'), $employee->file_kk);
            }

            if ($employee->photo && $request->file('photo') != "") {
                Storage::delete('upload/employee/' . $employee->photo);
            }

            if ($request->file('photo')) {
                $employee->photo = '3' . time() . '.' . $request->photo->getClientOriginalExtension();
                Storage::putFileAs('upload/employee', $request->file('photo'), $employee->photo);
            }

            $employee->save();

            activity()->log('Edit Data Employee With ID = ' . $employee->id);
            return response()->json(['success' => true, 'message' => 'Ubah Data Berhasil', 'data' => $employee]);
        }
    }

    ## Delete Data
    public function delete(Request $request, $employee)
    {
        if ($request->ajax()) {
            DB::beginTransaction();
            try {
                $employee = Employee::where('id', $employee)->first();

                Storage::delete('upload/employee/' . $employee->file_ktp);
                Storage::delete('upload/employee/' . $employee->file_kk);
                Storage::delete('upload/employee/' . $employee->photo);

                $employee->delete();

                $user = User::where('employee_id', $employee->id)->first();
                $user->status = 'Non Active';
                $user->save();

                DB::commit();

                activity()->log('Delete Data Employee With ID = ' . $employee->id);
                return response()->json(['success' => true, 'message' => 'Hapus Data Berhasil']);
            } catch (\Exception $e) {
                DB::rollBack(); // Rollback semua jika terjadi error

                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan data.',
                    'error' => $e->getMessage()
                ], 500);
            }
        }
    }

    public function print($employee)
    {
        $employee = Employee::where('id', Crypt::decrypt($employee))->first();
    	return view('admin.employee.report',compact('employee'));    
    }
}
