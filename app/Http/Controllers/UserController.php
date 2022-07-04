<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    public function index()
    {
        $breadcrumb = [
            ['route' => route('user.index'), 'name' => 'จัดการข้อมูลผู้ใช้'],
        ];

        $role = null;
        if (Auth::user()->hasRole('Admin')) {
            $role = Role::select('name_th', 'id')->get();
        } else {
            $role = Role::select('name_th', 'id')->where('name', '!=', 'Admin')->get();
        }

        $department = null;
        if (Auth::user()->hasRole('Admin')) {
            $department = Faculty::with('departments')->get();
        } else {
            $departmentId = Auth::user()->departmentId;
            $department = Department::where('id', $departmentId)->get();
        }
        return view('user.main', [
            'breadcrumb' => $breadcrumb,
            'department' => $department,
            'role' => $role,
        ]);
    }

    public function create()
    {
        $breadcrumb = [
            ['route' => route('user.index'), 'name' => 'จัดการข้อมูลผู้ใช้'],
            ['route' => '#', 'name' => 'เพิ่มข้อมูลผู้ใช้'],
        ];

        $role = null;
        $permission = Permission::all();
        if (Auth::user()->hasRole('Admin')) {
            $role = array('' => 'เลือกบทบาทผู้ใช้') + Role::select('name_th', 'id')->get()->pluck('name_th', 'id')->toArray();
        } else {
            $role = array('' => 'เลือกบทบาทผู้ใช้') + Role::select('name_th', 'id')->where('name', '!=', 'Admin')->get()->pluck('name_th', 'id')->toArray();
        }

        $department = null;
        if (Auth::user()->hasRole('Admin')) {
            $faculty = Faculty::with('departments')->get();
            $department = array();
            foreach ($faculty as $key => $value) {
                if (count($value->departments) != 0) {
                    $department[$value->name] = array();
                    foreach ($value->departments as $key => $item) {
                        $department[$value->name][$item->id] = $item->name;
                    }
                }
            }
            $department = array("" => "เลือกสาขาวิชา") + $department;
        } else {
            $department = Department::select('name', 'id')->where('id', Auth::user()->departmentId)->get()->pluck('name', 'id')->toArray();
        }

        return view('user.form', [
            'breadcrumb' => $breadcrumb,
            'department' => $department,
            'role' => $role,
            'permission' => $permission,
        ]);
    }

    public function store(Request $request)
    {
        $this->validate(
            $request,
            [
                'roleId' => 'required',
                'departmentId' => 'required_unless:roleId,1',
                'name' => 'required|max:100',
                'email' => 'required|email|unique:users|max:255',
                'password' => 'required', 'min:8', 'confirmed',
                'password_confirmation' => 'required',
            ],
            [
                'roleId.required' => 'กรุณาเลือกบทบาทผู้ใช้',
                'departmentId.required_unless' => 'กรุณาเลือกสาขาวิชา',
                'name.required' => 'กรุณากรอกชื่อผู้ใช้',
                'name.max' => 'ชื่อต้องไม่เกิน 100 ตัวอักษร',
                'email.required' => 'กรุณากรอกอีเมล์',
                'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
                'email.unique' => 'อีเมลนี้ถูกใช้ไปแล้ว',
                'email.max' => 'อีเมลต้องมีความยาวไม่เกิน 255 อักษร',
                'password.required' => 'กรุณาใส่รหัสผ่าน.',
                'password.min' => 'กรุณากรอกรหัสผ่านอย่างน้อย 8 ตัวอักษร.',
                'password.confirmed' => 'ยืนยันรหัสผ่านไม่ถูกต้อง',
                'password_confirmation.required' => 'กรุณายืนยันรหัสผ่านของคุณ',
                'password_confirmation.min' => 'กรุณากรอกยืนยันรหัสผ่านอย่างน้อย 8 ตัวอักษร.',
            ]
        );

        $data = new User($request->all());
        $data->save();

        $role = Role::findOrFail($data->roleId);
        if (!empty($role)) {
            $data->assignRole($role->name);
        }

        if ($role->name == 'Admin') {
            $permissions = Permission::all();
            if (count($permissions) != 0) {
                foreach ($permissions as $key => $value) {
                    $data->givePermissionTo($value->name);
                }
            }
        } else {
            if ($request->permission != null) {
                foreach ($request->permission as $key => $id) {
                    $permission = Permission::find($id);
                    if ($permission != null) {
                        $data->givePermissionTo($permission->name);
                    }
                }
            }
        }

        return redirect()->route('user.index')->with('toast_success', 'เพิ่มข้อมูลสำเร็จ!');
    }

    public function show($id)
    {
        //
    }
    public function edit($id)
    {
        $breadcrumb = [
            ['route' => route('user.index'), 'name' => 'จัดการข้อมูลผู้ใช้'],
            ['route' => '#', 'name' => 'แก้ไขข้อมูลผู้ใช้'],
        ];

        $role = null;
        $permission = Permission::all();
        if (Auth::user()->hasRole('Admin')) {
            $role = array('' => 'เลือกบทบาทผู้ใช้') + Role::select('name_th', 'id')->get()->pluck('name_th', 'id')->toArray();
        } else {
            $role = array('' => 'เลือกบทบาทผู้ใช้') + Role::select('name_th', 'id')->where('name', '!=', 'Admin')->get()->pluck('name_th', 'id')->toArray();
        }

        $department = null;
        if (Auth::user()->hasRole('Admin')) {
            $faculty = Faculty::with('departments')->get();
            $department = array();
            foreach ($faculty as $key => $value) {
                if (count($value->departments) != 0) {
                    $department[$value->name] = array();
                    foreach ($value->departments as $key => $item) {
                        $department[$value->name][$item->id] = $item->name;
                    }
                }
            }
            $department = array("" => "เลือกสาขาวิชา") + $department;
        } else {
            $department = Department::select('name', 'id')->where('id', Auth::user()->departmentId)->get()->pluck('name', 'id')->toArray();
        }

        return view('user.form', [
            'breadcrumb' => $breadcrumb,
            'department' => $department,
            'user' => User::findOrFail($id),
            'role' => $role,
            'permission' => $permission,
        ]);
    }

    public function update(Request $request, $id)
    {
        switch ($request->action) {
            case 'user form':
                $this->validate(
                    $request,
                    [
                        'roleId' => 'required',
                        'departmentId' => 'required_unless:roleId,1',
                        'name' => 'required|max:100',
                        'email' => 'required|email|max:255|unique:users,email,' . $id . ',id',
                    ],
                    [
                        'roleId.required' => 'กรุณาเลือกบทบาทผู้ใช้',
                        'departmentId.required_unless' => 'กรุณาเลือกสาขาวิชา',
                        'name.required' => 'กรุณากรอกชื่อผู้ใช้',
                        'name.max' => 'ชื่อต้องไม่เกิน 100 ตัวอักษร',
                        'email.required' => 'กรุณากรอกอีเมล์',
                        'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
                        'email.unique' => 'อีเมลนี้ถูกใช้ไปแล้ว',
                        'email.max' => 'อีเมลต้องมีความยาวไม่เกิน 255 อักษร',
                    ]
                );
                break;

            case 'change password':
                $this->validate(
                    $request,
                    [
                        'password' => 'required', 'min:8', 'confirmed',
                        'password_confirmation' => 'required',
                    ],
                    [
                        'password.required' => 'กรุณาใส่รหัสผ่าน.',
                        'password.min' => 'กรุณากรอกรหัสผ่านอย่างน้อย 8 ตัวอักษร.',
                        'password.confirmed' => 'ยืนยันรหัสผ่านไม่ถูกต้อง',
                        'password_confirmation.required' => 'กรุณายืนยันรหัสผ่านของคุณ',
                        'password_confirmation.min' => 'กรุณากรอกยืนยันรหัสผ่านอย่างน้อย 8 ตัวอักษร.',
                    ]
                );
                break;
            case 'profile':
                $this->validate(
                    $request,
                    [
                        'name' => 'required|max:100',
                        'email' => 'required|email|max:255|unique:users,email,' . $id . ',id',
                    ],
                    [
                        'name.required' => 'กรุณากรอกชื่อผู้ใช้',
                        'name.max' => 'ชื่อต้องไม่เกิน 100 ตัวอักษร',
                        'email.required' => 'กรุณากรอกอีเมล์',
                        'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
                        'email.unique' => 'อีเมลนี้ถูกใช้ไปแล้ว',
                        'email.max' => 'อีเมลต้องมีความยาวไม่เกิน 255 อักษร',
                    ]
                );
                break;
            case 'profile change password':
                $this->validate(
                    $request,
                    [
                        'password' => 'required', 'min:8', 'confirmed',
                        'password_confirmation' => 'required',
                    ],
                    [
                        'password.required' => 'กรุณาใส่รหัสผ่าน.',
                        'password.min' => 'กรุณากรอกรหัสผ่านอย่างน้อย 8 ตัวอักษร.',
                        'password.confirmed' => 'ยืนยันรหัสผ่านไม่ถูกต้อง',
                        'password_confirmation.required' => 'กรุณายืนยันรหัสผ่านของคุณ',
                        'password_confirmation.min' => 'กรุณากรอกยืนยันรหัสผ่านอย่างน้อย 8 ตัวอักษร.',
                    ]
                );
                break;
        }

        $data = User::find($id);
        switch ($request->action) {
            case 'user form':
                // update role
                $updateRole = $data->roleId != $request->roleId ? true : false;
                if ($updateRole) {
                    $oldrole = Role::findOrFail($data->roleId);
                    if (!empty($oldrole)) {
                        $data->removeRole($oldrole->name);
                    }
                    $newrole = Role::findOrFail($request->roleId);
                    if (!empty($newrole)) {
                        $data->assignRole($newrole->name);
                    }
                }

                // update data
                $data->update($request->all());

                // update permissions
                $role = Role::findOrFail($data->roleId);
                $permissions = Permission::all();
                if ($role->name != 'Admin') {
                    if ($request->permission != null) {
                        foreach ($permissions as $key => $value) {
                            $has_in_input = in_array($value->id, $request->permission);
                            if ($has_in_input) {
                                if (!$data->can($value->name)) {
                                    $data->givePermissionTo($value->name);
                                }
                            } else {
                                if ($data->can($value->name)) {
                                    $data->revokePermissionTo($value->name);
                                }
                            }
                        }
                    }
                } else {

                    foreach ($permissions as $key => $value) {
                        if (!$data->can($value->name)) {
                            $data->givePermissionTo($value->name);
                        }
                    }
                }
                break;

            default:
                // update data
                $data->update($request->all());
                break;
        }

        switch ($request->action) {
            case 'profile change password':
                return back()->with('toast_success', 'แก้ไขข้อมูลสำเร็จ!');
                break;
            case 'profile':
                return back()->with('toast_success', 'แก้ไขข้อมูลสำเร็จ!');
                break;
            default:
                return redirect()->route('user.index')->with('toast_success', 'แก้ไขข้อมูลสำเร็จ!');
                break;
        }
    }

    public function password($id)
    {
        $breadcrumb = [
            ['route' => route('user.index'), 'name' => 'จัดการข้อมูลผู้ใช้'],
            ['route' => '#', 'name' => 'เปลี่ยนรหัสผ่าน'],
        ];
        return view('user.password', [
            'breadcrumb' => $breadcrumb,
            'user' => User::findOrFail($id),
        ]);
    }

    public function profile()
    {
        $id = Auth::user()->id;
        $breadcrumb = [
            ['route' => '#', 'name' => 'ข้อมูลส่วนตัว: ' . Auth::user()->name],
        ];
        $department = null;
        if (Auth::user()->hasRole('Admin')) {
            $faculty = Faculty::with('departments')->get();
            $department = array();
            foreach ($faculty as $key => $value) {
                if (count($value->departments) != 0) {
                    $department[$value->name] = array();
                    foreach ($value->departments as $key => $item) {
                        $department[$value->name][$item->id] = $item->name;
                    }
                }
            }
            $department = array("" => "เลือกสาขาวิชา") + $department;
        } else {
            $department = Department::select('name', 'id')->where('id', Auth::user()->departmentId)->get()->pluck('name', 'id')->toArray();
        }
        return view('user.profile', [
            'breadcrumb' => $breadcrumb,
            'user' => User::with('department.faculty')->findOrFail($id),
            'department' => $department
        ]);
    }

    public function destroy($id)
    {
        $data = User::find($id);
        $data->delete();
        return back()->with('toast_success', 'ลบข้อมูลเรียบร้อยแล้ว!');
    }

    public function jsontable(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $search = $request->get('search');
        $order = $request->get('order');

        // filter
        $is_admin = Auth::user()->hasRole('Admin');
        $departmentId = $request->get('departmentId');
        $roleId = $request->get('roleId');

        $columnorder = array(
            'id',
            'departmentId',
            'roleId',
            'name',
            'email',
            'created_at',
            'updated_at',
        );

        if (empty($order)) {
            $sort = 'name';
            $dir = 'asc';
        } else {
            $sort = $columnorder[$order[0]['column']];
            $dir = $order[0]['dir'];
        }

        $keyword = trim($search['value']);

        $data = User::with('department.faculty')
            ->when($departmentId, function ($query, $departmentId) {
                return $query->where(function ($query) use ($departmentId) {
                    if (!empty($departmentId) && $departmentId != 0) {
                        $query->where('departmentId', $departmentId);
                    }
                });
            })
            ->when($roleId, function ($query, $roleId) {
                return $query->where(function ($query) use ($roleId) {
                    if (!empty($roleId) && $roleId != 0) {
                        $query->where('roleId', $roleId);
                    }
                });
            })
            ->when($is_admin, function ($query, $is_admin) {
                return $query->where(function ($query) use ($is_admin) {
                    if (!$is_admin) {
                        $query->where('roleId', '!=', 1);
                    }
                });
            })
            ->when($keyword, function ($query, $keyword) {
                return $query->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'LIKE', '%' . $keyword . '%')->orWhere('email', 'LIKE', '%' . $keyword . '%');
                });
            })
            ->offset($start)
            ->limit($length)
            ->orderBy($sort, $dir)
            ->get();


        $recordsTotal = User::with('department.faculty')
            ->when($departmentId, function ($query, $departmentId) {
                return $query->where(function ($query) use ($departmentId) {
                    if (!empty($departmentId) && $departmentId != 0) {
                        $query->where('departmentId', $departmentId);
                    }
                });
            })
            ->when($roleId, function ($query, $roleId) {
                return $query->where(function ($query) use ($roleId) {
                    if (!empty($roleId) && $roleId != 0) {
                        $query->where('roleId', $roleId);
                    }
                });
            })
            ->when($is_admin, function ($query, $is_admin) {
                return $query->where(function ($query) use ($is_admin) {
                    if (!$is_admin) {
                        $query->where('roleId', '!=', 1);
                    }
                });
            })
            ->select('id')->count();

        $recordsFiltered = User::with('department.faculty')
            ->when($departmentId, function ($query, $departmentId) {
                return $query->where(function ($query) use ($departmentId) {
                    if (!empty($departmentId) && $departmentId != 0) {
                        $query->where('departmentId', $departmentId);
                    }
                });
            })
            ->select('id')
            ->when($roleId, function ($query, $roleId) {
                return $query->where(function ($query) use ($roleId) {
                    if (!empty($roleId) && $roleId != 0) {
                        $query->where('roleId', $roleId);
                    }
                });
            })
            ->when($is_admin, function ($query, $is_admin) {
                return $query->where(function ($query) use ($is_admin) {
                    if (!$is_admin) {
                        $query->where('roleId', '!=', 1);
                    }
                });
            })
            ->when($keyword, function ($query, $keyword) {
                return $query->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'LIKE', '%' . $keyword . '%')->orWhere('email', 'LIKE', '%' . $keyword . '%');
                });
            })
            ->offset($start)
            ->limit($length)
            ->orderBy($sort, $dir)
            ->count();

        $datatable = DataTables::of($data)
            ->editColumn('id', function ($data) {
                return str_pad($data->id, 5, "0", STR_PAD_LEFT);
            })
            ->editColumn('roleId', function ($data) {
                $role = Role::find($data->roleId);
                $roleId = '<span><i><u>' . $role->name_th . '</u></i></span>';
                return $roleId;
            })
            ->editColumn('created_at', function ($data) {
                return '<small>' . thaidate('j F Y', strtotime($data->created_at)) . '<br><i class="far fa-clock"></i> ' . date('H:i:s', strtotime($data->created_at)) . '</small>';
            })
            ->editColumn('updated_at', function ($data) {
                return '<small>' . thaidate('j F Y', strtotime($data->updated_at)) . '<br><i class="far fa-clock"></i> ' . date('H:i:s', strtotime($data->updated_at)) . '</small>';
            })
            ->addColumn('department_name', function ($data) {
                $department_name = "";
                if ($data->departmentId != null) {
                    $department_name = $data->department->name . '<small><br><u>คณะ</u>: ' . $data->department->faculty->name . '</small>';
                }
                return $department_name;
            })
            ->addColumn('actions', function ($data) {
                $id = $data->id;
                return view('user._action', compact('id'));
            })
            ->setTotalRecords($recordsTotal)
            ->setFilteredRecords($recordsFiltered)
            ->escapeColumns([])
            ->skipPaging()
            ->addIndexColumn()
            ->make(true);
        return $datatable;
    }
}
