<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Faculty;
use App\Models\Location;
use Auth;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $breadcrumb = [
            ['route' => route('location.index'), 'name' => 'จัดการข้อมูลที่จัดเก็บพัสดุ-ครุภัณฑ์'],
        ];
        $faculty = null;
        $department = null;
        if (Auth::user()->hasRole('Admin')) {
            $faculty = Faculty::select('name', 'id')->get();
        } else {
            $faculty = Faculty::select('name', 'id')->where('id', Auth::user()->facultyId)->get();
            $department = Department::select('name', 'id')->where('id', Auth::user()->departmentId);
        }
        return view('location.main', [
            'breadcrumb' => $breadcrumb,
            'faculty' => $faculty,
            'department' => $department,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $breadcrumb = [
            ['route' => route('faculty.index'), 'name' => 'จัดการข้อมูลที่จัดเก็บพัสดุ-ครุภัณฑ์'],
            ['route' => '#', 'name' => 'เพิ่มข้อมูลที่จัดเก็บพัสดุ-ครุภัณฑ์'],
        ];

        $faculty = null;
        $department = null;
        if (Auth::user()->hasRole('Admin')) {
            $faculty = array('' => 'เลือกคณะ') + Faculty::select('name', 'id')->get()->pluck('name', 'id')->toArray();
            $department = array('' => 'เลือกสาขาวิชา');
        } else {
            $faculty = Faculty::select('name', 'id')->where('id', Auth::user()->facultyId)->get()->pluck('name', 'id')->toArray();
            $department = Department::select('name', 'id')->where('id', Auth::user()->departmentId)->get()->pluck('name', 'id')->toArray();
        }
        return view('location.form', [
            'breadcrumb' => $breadcrumb,
            'faculty' => $faculty,
            'department' => $department,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate(
            $request,
            [
                'name' => 'required|max:100',
            ],
            [
                'name.required' => 'กรุณากรอกชื่อ',
                'name.max' => 'ชื่อต้องไม่เกิน 100 ตัวอักษร',
            ]
        );
        $data = new Location($request->all());
        $data->save();
        return redirect()->route('location.index')->with('toast_success', 'เพิ่มข้อมูลสำเร็จ!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $breadcrumb = [
            ['route' => route('location.index'), 'name' => 'จัดการข้อมูลที่จัดเก็บพัสดุ-ครุภัณฑ์'],
            ['route' => '#', 'name' => 'แก้ไขข้อมูลที่จัดเก็บพัสดุ-ครุภัณฑ์'],
        ];
        $location = Location::findOrFail($id);
        $faculty = null;
        $department = null;
        if (Auth::user()->hasRole('Admin')) {
            $faculty = array('' => 'เลือกคณะ') + Faculty::select('name', 'id')->get()->pluck('name', 'id')->toArray();
            $department = array('' => 'เลือกสาขาวิชา') + Department::select('name', 'id')->where('facultyId', $location->facultyId)->get()->pluck('name', 'id')->toArray();
        } else {
            $faculty = Faculty::select('name', 'id')->where('id', Auth::user()->facultyId)->get()->pluck('name', 'id')->toArray();
            $department = Department::select('name', 'id')->where('id', Auth::user()->departmentId)->get()->pluck('name', 'id')->toArray();
        }
        return view('location.form', [
            'breadcrumb' => $breadcrumb,
            'location' => $location,
            'faculty' => $faculty,
            'department' => $department,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate(
            $request,
            [
                'name' => 'required|max:100',
            ],
            [
                'name.required' => 'กรุณากรอกชื่อ',
                'name.max' => 'ชื่อต้องไม่เกิน 100 ตัวอักษร',
            ]
        );
        $data = Location::findOrFail($id);
        $data->update($request->all());
        $data->save();
        return redirect()->route('location.index')->with('toast_success', 'เพิ่มข้อมูลสำเร็จ!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Location::findOrFail($id);
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
        $facultyId = $request->get('facultyId');
        $departmentId = $request->get('departmentId');

        $columnorder = array(
            'id',
            'name',
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

        $data = Location::when($keyword, function ($query, $keyword) {
            return $query->where(function ($query) use ($keyword) {
                $query->orWhere('name', 'LIKE', '%' . $keyword . '%');
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
            ->offset($start)
            ->limit($length)
            ->orderBy($sort, $dir)
            ->get();


        $recordsTotal = Location::select('id')->count();
        if (!Auth::user()->hasRole('Admin')) {
            $recordsTotal = Location::select('id')
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
                ->count();
        }

        $recordsFiltered = Location::select('id')
            ->when($keyword, function ($query, $keyword) {
                return $query->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'LIKE', '%' . $keyword . '%');
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
            ->editColumn('created_at', function ($data) {
                return '<small>' . date('d/m/Y', strtotime($data->created_at)) . '<br><i class="far fa-clock"></i> ' . date('h:i A', strtotime($data->created_at)) . '</small>';
            })
            ->editColumn('updated_at', function ($data) {
                return '<small>' . date('d/m/Y', strtotime($data->updated_at)) . '<br><i class="far fa-clock"></i> ' . date('h:i A', strtotime($data->updated_at)) . '</small>';
            })
            ->addColumn('actions', function ($data) {
                $id = $data->id;
                return view('location._action', compact('id'));
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
