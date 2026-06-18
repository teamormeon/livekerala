<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Role;
use App\Models\Transaction;
use App\Traits\Upload;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class ManageRolePermissionController extends Controller
{
    use Upload;

    public function roleList()
    {
        return view('admin.role_permission.role_list');
    }

    public function roleListSearch(Request $request)
    {
        $roles = Role::query()
            ->when(!empty($request->search['value']),function ($query) use ($request){
                $query->where('name','LIKE','%'.$request->search['value'].'%');
            })
            ->orderBy('id', 'desc');

        return DataTables::of($roles)
            ->addColumn('no',function ($item){
                static $counter = 0;
                return ++$counter;
            })
            ->addColumn('name',function ($item){
                return $item->name;
            })
            ->addColumn('status',function ($item){
                $status = $item->status == 1 ? 'Active' : 'Inactive';
                $bg = $item->status == 1 ? 'success' : 'danger';
                return "<span class='badge  bg-soft-success text-$bg bg-soft-$bg text-$bg'><span class='legend-indicator bg-$bg'></span>".$status."</span>";
            })
            ->addColumn('action',function ($item){
                $id = $item->id;
                $deleteUrl = route('admin.role.delete',$item->id);

                $canEdit = adminAccessRoute(config('role.manage_role.access.edit'));
                $canDelete = adminAccessRoute(config('role.manage_role.access.delete'));

                $actions = '';
                if ($canEdit) {
                    $actions .= "<a href='javascript:void(0)' class='btn btn-white btn-sm edit_user_btn edit_role_btn' data-bs-toggle='modal' data-bs-target='#EditRoleModal' data-id='$id'>
                        <i class='bi bi-pencil-square dropdown-item-icon'></i> ".trans('Edit')."
                      </a>";
                }
                if ($canDelete) {
                    $actions .= "<div class='btn-group'>
                      <button type='button' class='btn btn-white btn-icon btn-sm dropdown-toggle dropdown-toggle-empty' id='userEditDropdown' data-bs-toggle='dropdown' aria-expanded='false'></button>
                      <div class='dropdown-menu dropdown-menu-end mt-1' aria-labelledby='userEditDropdown'>
                         <a class='dropdown-item loginAccount DeleteBtn' data-route='$deleteUrl' href='javascript:void(0)' data-bs-toggle='modal' data-bs-target='#deleteModal'>
                           <i class='bi bi-trash dropdown-item-icon'></i>
                          ".trans("Delete")."
                        </a>
                      </div>
                    </div>";
                }
                return $actions ?: '-';
            })
            ->rawColumns(['no','name','status','action'])
            ->make(true);
    }

    public function getRole($id)
    {
        $role = Role::findOrFail($id);
        $configRole = config('role');
        return response()->json([
            'role' => $role,
            'configRole' => $configRole
        ]);
    }

    public function roleCreate(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'status' => 'required|boolean',
            'access' => 'required|array|min:1',
        ]);

        $role = new Role();
        $role->user_id = auth()->id();
        $role->name = $request->name;
        $role->status = $request->status;
        $role->permission =  (isset($request->access)) ? explode(',',join(',',$request->access)) : [];
        $role->save();
        return redirect()->back()->with('success', 'Role Saved Successfully');
    }

    public function roleUpdate(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'status' => 'required|boolean',
            'access' => 'required|array|min:1',
        ]);
        $role = Role::findOrFail($request->id);
        try {
            $role->name = $request->name;
            $role->status = $request->status;
            $role->permission =  explode(',',join(',',$request->access));
            $role->save();
        }catch (\Exception $exception){
            return back()->with('error', $exception->getMessage());
        }
        return redirect()->back()->with('success', 'Role Updated Successfully');
    }

    public function roleDelete($id)
    {
        $role = Role::with(['roleUsers'])->find($id);
        if (count($role->roleUsers) > 0) {
            return back()->with('error','This role has many users');
        }
        $role->delete();
        return back()->with('success', 'Role Delete successfully');
    }


    public function staffList()
    {
        return view('admin.role_permission.staff_list');
    }

    public function getStaffList(Request $request)
    {
        $staffs = Admin::query()->with('role')
            ->where('is_owner', 0)
            ->orderBy('name', 'asc')
            ->when(!empty($request->search['value']),function ($query)use($request){
                $query->where('name','LIKE','%'.$request->search['value'].'%')
                    ->orWhere('username','LIKE','%'.$request->search['value'].'%')
                    ->orWhereHas('role',function ($subQuery)use($request){
                        $subQuery->where('name','LIKE','%'.$request->search['value'].'%');
                    });
            });
        return DataTables::of($staffs)
            ->addColumn('no',function (){
                static  $count = 0;
                return ++$count;
            })
            ->addColumn('user',function ($item){
                $profile = '';
                if (!$item->image) {
                    $firstLetter = substr($item->name, 0, 1);
                    $profile =  '<div class="avatar avatar-sm avatar-soft-primary avatar-circle">
                        <span class="avatar-initials">' . $firstLetter . '</span>
                     </div>';

                } else {
                    $url = getFile($item->driver, $item->image);
                    $profile =  '<div class="avatar avatar-sm avatar-circle">
                        <img class="avatar-img" src="' . $url . '" alt="Image Description">
                     </div>';

                }
                return  '<a class="d-flex align-items-center me-2" href="javascript:void(0)">
                            <div class="flex-shrink-0">
                                    '.$profile.'
                             </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                              <h5 class="text-hover-primary mb-0">' . optional($item)->name.'</h5>
                              <span class="fs-6 text-body">@' . optional($item)->username ?? 'Unknown' . '</span>
                            </div>
                          </a>';
            })
            ->addColumn('role',function ($item){
                return "<span class='badge bg-primary rounded-pill'>".optional($item->role)->name."</span>";
            })
            ->addColumn('status',function ($item){
                $status = $item->status == 1 ? 'Active' : 'Inactive';
                $bg = $item->status == 1 ? 'success' : 'danger';
                return "<span class='badge  bg-soft-success text-$bg bg-soft-$bg text-$bg'><span class='legend-indicator bg-$bg'></span>".$status."</span>";
            })
            ->addColumn('action',function ($item){
                $editUrl = route('admin.edit.staff',$item->id);
                $deleteUrl = route('admin.edit.staff',$item->id);
                $status = $item->status == 1?'Inactive':'Active';
                $statsChangeUrl = route('admin.role.statusChange', $item->id);
                $loginUrl = route('admin.role.usersLogin',$item->id);
                $icon = $item->status==1?"<i class='fa-light fa-circle-xmark dropdown-item-icon'></i>":'<i class="fa-sharp fa-light fa-check dropdown-item-icon"></i>';
                return adminAccessRoute(config('role.manage_staff_role.access.edit'))?"<div class='btn-group' role='group'>
                      <a href='$editUrl' class='btn btn-white btn-sm edit_user_btn'>
                        <i class='bi bi-pencil-square dropdown-item-icon'></i> ".trans('Edit Staff')."
                      </a>
                    <div class='btn-group'>
                      <button type='button' class='btn btn-white btn-icon btn-sm dropdown-toggle dropdown-toggle-empty' id='userEditDropdown' data-bs-toggle='dropdown' aria-expanded='false'></button>
                      <div class='dropdown-menu dropdown-menu-end mt-1' aria-labelledby='userEditDropdown'>
                          <a class='dropdown-item LoginAccount'  href='javascript:void(0)' data-route='$loginUrl' >
                           <i class='fa-sharp fa-light fa-right-to-bracket dropdown-item-icon'></i>
                          ".trans("Login as Staff")."
                        </a>
                        <a class='dropdown-item  statusChangeButton' href='javascript:void(0)' data-route='$statsChangeUrl' data-bs-toggle='modal' data-bs-target='#statusChangeModal' data-status='$status'>
                       $icon
                          ".trans($status)."
                        </a>
                      </div>
                    </div>
                  </div>":'-';
            })
            ->rawColumns(['no','user','role','status','action'])
            ->make(true);
    }

    public function editStaff($id)
    {
        $staff = Admin::findOrFail($id);
        $roles = Role::where('status',1)->get();
        return view('admin.role_permission.edit_staff',compact('staff','roles'));
    }

    public function staffUpdate(Request $request)
    {
        $request->validate([
            'id' => ['required',Rule::exists('admins', 'id')],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($request->id)],
            'username' => ['required', 'string', 'max:50', Rule::unique('admins', 'username')->ignore($request->id)],
            'role' => ['required'],
        ]);

        $user = Admin::findOrFail($request->id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->username = $request->username;
        $user->role_id = $request->role;
        $user->status = $request->status?1:0;
        if ($request->password){
            $user->password = Hash::make($request->password);
        }
        $user->save();
        session()->flash('success', 'User Updated Successfully');
        return redirect()->route('admin.role.staff');
    }

    public function staffCreate()
    {
        $roles = Role::where('status',1)->get();
        return view('admin.role_permission.create_staff',compact('roles'));
    }

    public function staffStore(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'username' => ['required', 'string', 'max:50', 'unique:admins,username'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required'],
        ]);

        $user = new Admin();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->username = $request->username;
        $user->password = Hash::make($request->password);
        $user->role_id = $request->role;
        $user->status = $request->status == 'on' ? 1 : 0;
        $user->save();
        return redirect()->route('admin.role.staff')->with('success', 'User Added Successfully');
    }

    public function statusChange($id)
    {
        $user = Admin::findOrFail($id);
        if ($user) {
            $user = Admin::findOrFail($id);
            $user->status = !$user->status;
            $user->save();
            return back()->with('success', 'Status Updated Successfully');
        }
    }

    public function userLogin($id)
    {
        Auth::guard('admin')->loginUsingId($id);
        $list = collect(config('role'))->pluck(['access','view'])->collapse()->intersect(optional(optional(Auth::guard('admin')->user())->role)->permission);

        if(count($list) == 0){
            $list = collect(['admin.profile']);
        }
        return redirect()->intended(route($list->first()));
    }



}
