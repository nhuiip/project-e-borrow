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

        $faculty = null;
        $department = null;
        $role = null;
        if (Auth::user()->hasRole('Admin')) {
            $faculty = Faculty::select('name', 'id')->get();
            $role = Role::select('name_th', 'id')->get();
        } else {
            $faculty = Faculty::select('name', 'id')->where('id', Auth::user()->facultyId)->get();
            $department = Department::select('name', 'id')->where('id', Auth::user()->departmentId);
            $role = Role::select('name_th', 'id')->where('name', '!=', 'Admin')->get();
        }
        return view('user.main', [
            'breadcrumb' => $breadcrumb,
            'faculty' => $faculty,
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

        $faculty = null;
        $department = null;
        $role = null;
        $permission = Permission::all();
        if (Auth::user()->hasRole('Admin')) {
            $faculty = array('' => 'เลือกคณะ') + Faculty::select('name', 'id')->get()->pluck('name', 'id')->toArray();
            $department = array('' => 'เลือกสาขาวิชา');
            $role = array('' => 'เลือกบทบาทผู้ใช้') + Role::select('name_th', 'id')->get()->pluck('name_th', 'id')->toArray();
        } else {
            $faculty = Faculty::select('name', 'id')->where('id', Auth::user()->facultyId)->get()->pluck('name', 'id')->toArray();
            $department = Department::select('name', 'id')->where('id', Auth::user()->departmentId)->get()->pluck('name', 'id')->toArray();
            $role = array('' => 'เลือกบทบาทผู้ใช้') + Role::select('name_th', 'id')->where('name', '!=', 'Admin')->get()->pluck('name_th', 'id')->toArray();
        }

        return view('user.form', [
            'breadcrumb' => $breadcrumb,
            'faculty' => $faculty,
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
                'facultyId' => 'required_unless,roleId,1',
                'departmentId' => 'required_unless,roleId,1',
                'fname' => 'required|max:100',
                'lname' => 'required|max:100',
                'email' => 'required|email|unique:users|max:255',
                'password' => 'required', 'min:8', 'confirmed',
                'password_confirmation' => 'required',
            ],
            [
                'roleId.required' => 'กรุณาเลือกบทบาทผู้ใช้',
                'facultyId.required' => 'กรุณาเลือกคณะ',
                'departmentId.required' => 'กรุณาเลือกสาขาวิชา',
                'fname.required' => 'กรุณากรอกชื่อผู้ใช้',
                'fname.max' => 'ชื่อต้องไม่เกิน 100 ตัวอักษร',
                'lname.required' => 'กรุณากรอกนามสกุลผู้ใช้',
                'lname.max' => 'นามสกุลต้องไม่เกิน 100 ตัวอักษร',
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
            ['route' => '#', 'name' => 'แก้ข้อมูลผู้ใช้'],
        ];
        $f = array('' => 'เลือกคณะ') + Role::select('name', 'id')->get()->pluck('name_th', 'id')->toArray();
        if (Auth::user()->roles[0]['name'] != 'Admin')

            return view('user.form', [
                'breadcrumb' => $breadcrumb,
                'user' => User::findOrFail($id),
                'role' => array('' => 'เลือกบทบาทผู้ใช้') + Role::select('name_th', 'id')->where('id', '!=', 1)->get()->pluck('name_th', 'id')->toArray(),
                'faculty' => array('' => 'เลือกคณะ') + Role::select('name', 'id')->get()->pluck('name_th', 'id')->toArray(),
            ]);
    }

    public function update(Request $request, $id)
    {
        $this->validate(
            $request,
            [
                'roleId' => 'required',
                'facultyId' => 'required',
                'departmentId' => 'required',
                'fname' => 'required|max:100',
                'lname' => 'required|max:100',
                'email' => 'required|email|unique:users|max:255',
                'password' => 'required', 'min:8', 'confirmed',
                'password_confirmation' => 'required',
            ],
            [
                'roleId.required' => 'กรุณาเลือกบทบาทผู้ใช้',
                'facultyId.required' => 'กรุณาเลือกคณะ',
                'departmentId.required' => 'กรุณาเลือกสาขาวิชา',
                'fname.required' => 'กรุณากรอกชื่อผู้ใช้',
                'fname.max' => 'ชื่อต้องไม่เกิน 100 ตัวอักษร',
                'lname.required' => 'กรุณากรอกนามสกุลผู้ใช้',
                'lname.max' => 'นามสกุลต้องไม่เกิน 100 ตัวอักษร',
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
        $facultyId = $request->get('facultyId');
        $departmentId = $request->get('departmentId');
        $roleId = $request->get('roleId');

        $columnorder = array(
            'id',
            'facultyId',
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

        $data = User::when($keyword, function ($query, $keyword) {
            return $query->where(function ($query) use ($keyword) {
                $query->orWhere('name', 'LIKE', '%' . $keyword . '%')->orWhere('email', 'LIKE', '%' . $keyword . '%');
            });
        })
            ->when($facultyId, function ($query, $facultyId) {
                return $query->where(function ($query) use ($facultyId) {
                    if (!empty($facultyId) && $facultyId != 0) {
                        $query->where('facultyId', $facultyId);
                    }
                });
            })
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
            ->offset($start)
            ->limit($length)
            ->orderBy($sort, $dir)
            ->get();


        $recordsTotal = User::select('id')->count();
        if (!Auth::user()->hasRole('Admin')) {
            $recordsTotal = User::select('id')
                ->when($facultyId, function ($query, $facultyId) {
                    return $query->where(function ($query) use ($facultyId) {
                        if (!empty($facultyId) && $facultyId != 0) {
                            $query->where('facultyId', $facultyId);
                        }
                    });
                })
                ->when($departmentId, function ($query, $departmentId) {
                    return $query->where(function ($query) use ($departmentId) {
                        if (!empty($departmentId) && $departmentId != 0) {
                            $query->where('departmentId', $departmentId);
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
                ->count();
        }

        $recordsFiltered = User::select('id')
            ->when($keyword, function ($query, $keyword) {
                return $query->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'LIKE', '%' . $keyword . '%')->orWhere('email', 'LIKE', '%' . $keyword . '%');
                });
            })
            ->when($facultyId, function ($query, $facultyId) {
                return $query->where(function ($query) use ($facultyId) {
                    if (!empty($facultyId) && $facultyId != 0) {
                        $query->where('facultyId', $facultyId);
                    }
                });
            })
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
            ->offset($start)
            ->limit($length)
            ->orderBy($sort, $dir)
            ->count();

        $datatable = DataTables::of($data)
            ->editColumn('id', function ($data) {
                return str_pad($data->id, 5, "0", STR_PAD_LEFT);
            })
            ->editColumn('facultyId', function ($data) {
                $facultyId = '<span><i>-</i></span>';
                if ($data->facultyId != null) {
                    $facultyId = Faculty::find($data->facultyId);
                    $facultyId = '<span><i><u>' . $facultyId->name . '</u></i></span>';
                }
                return $facultyId;
            })
            ->editColumn('departmentId', function ($data) {
                $departmentId = '<span><i>-</i></span>';
                if ($data->departmentId != null) {
                    $departmentId = Department::find($data->departmentId);
                    $departmentId = '<span><i><u>' . $departmentId->name . '</u></i></span>';
                }
                return $departmentId;
            })
            ->editColumn('roleId', function ($data) {
                $role = Role::find($data->roleId);
                $roleId = '<span><i><u>' . $role->name . '</u></i></span>';
                return $roleId;
            })
            ->editColumn('created_at', function ($data) {
                return '<small>' . date('d/m/Y', strtotime($data->created_at)) . '<br><i class="far fa-clock"></i> ' . date('h:i A', strtotime($data->created_at)) . '</small>';
            })
            ->editColumn('updated_at', function ($data) {
                return '<small>' . date('d/m/Y', strtotime($data->updated_at)) . '<br><i class="far fa-clock"></i> ' . date('h:i A', strtotime($data->updated_at)) . '</small>';
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
