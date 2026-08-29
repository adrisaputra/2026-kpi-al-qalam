<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    ## Show Data
    public function index()
    {
        $title = "User";
		return view('admin.user.index',compact('title'));
    }

    ## Get Data
    public function get_user_index(Request $request)
    {

        if ($request->ajax()) {
            $counter = 1;

            // $query = User::query()
            // ->leftJoin('employees', 'employees.id', '=', 'users.employee_id')
            // ->select('users.*', 'employees.name as employee_name');
        
            $query = User::query()
                ->whereHas('group.group_application', function($query){
                    $query->where('group_id','!=',3)
                    ->where('application_id',2);
                });

            $user = $query->where('users.name','!=','superadmin')->limit(10);

            return DataTables::of($user)
            ->addIndexColumn()
            ->addColumn('number', function () use (&$counter) {
                return $counter++;
            })
            ->addColumn('show_status', function ($v) {
                if($v->status=='Active'){
                    $status ='<span class="badge badge-success">Aktif</span>';
                }else{
                    $status ='<span class="badge badge-danger">Tidak Aktif</span>';
                }
                return $status;
            })
            ->addColumn('action', function ($v) {
                $btn = '<a href="#" onClick="getData('.$v->id.')" id="'.$v->id.'" title="Edit" data-toggle="modal" data-target="#exampleModal">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 text-success"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
                        </a>';
                if($v->id != 1){
                    $btn .= '<a href="#" onclick="deleteData('.$v->id.')" id="'.$v->id.'" class="warning confirm" data-toggle="tooltip" data-placement="top" title="Hapus">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 text-danger"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                            </a>';
                }
                return $btn;
            })
            ->rawColumns(['group','show_status','action'])->make(true);
        }
        
    }

    public function validate(Request $request, $action)
    {
        if ($request->ajax()) {

            $attributes = [
                'name' => 'Nama User',
                'email' => 'Email',
                'password' => 'Password',
                'status' => 'Status'
            ];

            if($action==="Simpan"){
                $rules = [
                    'name' => 'required|string|max:255',
                    'email' => 'required|string|email|max:255|unique:users',
                    'password' => 'required|string|min:8|confirmed',
                    'status' => 'required'
                ];
            } else {
                if($request->password){
                    $rules = [
                        'name' => 'required|string|max:255',
                        'password' => 'required|string|min:8|confirmed',
                        'status' => 'required'
                    ];
                } else {
                    $rules = [
                        'name' => 'required|string|max:255',
                        'status' => 'required',
                    ];
                }
            }

            $request->validate($rules, [],$attributes);
            
            return response()->json(['success' => true]);
        }
    }

    ## Save Data
	public function store(Request $request)
    {
        if ($request->ajax()) {
            $user = New User();
            $user->fill($request->all());
            $user->group_id = 4;
            $user->save();
            
            Activity()->log('Tambah Data Admin KPI');
            return response()->json(['success' => true,'message' => 'Tambah Data Admin KPI Berhasil']);
        }
    }

    ## Get Data
    public function edit(Request $request,$id)
    {
        if ($request->ajax()) {
            $user = User::where('id',$id)->first();
            return response()->json(['success' => true,'data' => $user]);
        }
    }

    ## Edit Data
    public function update(Request $request, User $user)
    {
        if ($request->ajax()) {
            if($request->password){
                $user->name = $request->name;
                $user->email = $request->email;
                $user->password = Hash::make($request->password);
                $user->status = $request->status;
            } else {
                $user->name = $request->name;
                $user->email = $request->email;
                $user->status = $request->status;
            }
            $user->save();
    
            activity()->log('Ubah Data Admin KPI dengan ID = '.$user->id);
            return response()->json(['success' => true,'message' => 'Ubah Data Admin KPI Berhasil']);
        }
    }

    ## Delete Data
    public function delete(Request $request, $user)
    {
        if ($request->ajax()) {
            $user = User::where('id',$user)->first();
            $user->delete();
            activity()->log('Hapus Data Admin KPI dengan ID = '.$user->id);
            return response()->json(['success' => true,'message' => 'Hapus Data Admin KPI Berhasil']);
        }
    }

    public function validate_profile(Request $request, $action)
    {
        if ($request->ajax()) {

            $attributes = [
                'name' => 'Nama User',
                'email' => 'Email',
                'password' => 'Password'
            ];

            if($action==="Simpan"){
                $rules = [
                    'name' => 'required|string|max:255',
                    'email' => 'required|string|email|max:255|unique:users',
                    'password' => 'required|string|min:8|confirmed'
                ];
            } else {
                if($request->password){
                    $rules = [
                        'name' => 'required|string|max:255',
                        'password' => 'required|string|min:8|confirmed',
                    ];
                } else {
                    $rules = [
                        'name' => 'required|string|max:255',
                    ];
                }
            }

            $request->validate($rules, [],$attributes);
            
            return response()->json(['success' => true]);
        }
    }

    ## Edit Data
    public function edit_profil(Request $request, $user)
    {
        $title = "Profil Saya";
        $user = Crypt::decrypt($user);
        $user = User::where('id',$user)->first();
		return view('admin.user.profile',compact('title','user'));
    }

    ## Edit Data
    public function update_profil(Request $request, $user)
    {
        
        $user = Crypt::decrypt($user);
        $user = User::where('id',$user)->first();
        
        $user->name = $request->name;
        $user->email = $request->email;
        
        if($request->password){
            $user->password = Hash::make($request->password);
        } else {
            $cek_user = User::where('id', Auth::user()->id)->first();
            $user->password = $cek_user->password;
        }
        
        if($request->file('photo') == ""){}
        else
        {	
            $user->photo = time().'.'.$request->photo->getClientOriginalExtension();
            Storage::putFileAs('upload/photo',$request->file('photo'), $user->photo);
        }
        
        $user->save();
        
        activity()->log('Ubah Data Profil dengan ID = '.$user->id);
        return response()->json(['success' => true,'message' => 'Update Data Profil User Berhasil']);
    }
}
