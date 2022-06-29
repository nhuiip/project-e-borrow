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
        $department = null;
        if (Auth::user()->hasRole('Admin')) {
            $department = Faculty::with('departments')->get();
        } else {
            $departmentId = Auth::user()->departmentId;
            $department = Department::where('id', $departmentId)->get();
        }
        return view('location.main', [
            'breadcrumb' => $breadcrumb,
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
            ['route' => route('location.index'), 'name' => 'จัดการข้อมูลที่จัดเก็บพัสดุ-ครุภัณฑ์'],
            ['route' => '#', 'name' => 'เพิ่มข้อมูลที่จัดเก็บพัสดุ-ครุภัณฑ์'],
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
        return view('location.form', [
            'breadcrumb' => $breadcrumb,
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
                'departmentId' => 'required',
            ],
            [
                'name.required' => 'กรุณากรอกชื่อ',
                'name.max' => 'ชื่อต้องไม่เกิน 100 ตัวอักษร',
                'departmentId.required' => 'กรุณาเลือกสาขาวิชา',
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
        return view('location.form', [
            'breadcrumb' => $breadcrumb,
            'location' => $location,
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
                'departmentId' => 'required',
            ],
            [
                'name.required' => 'กรุณากรอกชื่อ',
                'name.max' => 'ชื่อต้องไม่เกิน 100 ตัวอักษร',
                'departmentId.required' => 'กรุณาเลือกสาขาวิชา',
            ]
        );
        $data = Location::findOrFail($id);
        $data->update($request->all());
        $data->save();
        return redirect()->route('location.index')->with('toast_success', 'แก้ไขข้อมูลสำเร็จ!');
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

        $data = Location::with('department.faculty')
            ->whereHas('department', function ($query) use ($departmentId) {
                if (!empty($departmentId) && $departmentId != 0) {
                    $query->where('id', $departmentId);
                }
            })
            ->when($keyword, function ($query, $keyword) {
                return $query->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'LIKE', '%' . $keyword . '%');
                });
            })
            ->offset($start)
            ->limit($length)
            ->orderBy($sort, $dir)
            ->get();


        $recordsTotal = Location::with('department.faculty')
            ->select('id')
            ->whereHas('department', function ($query) use ($departmentId) {
                if (!empty($departmentId) && $departmentId != 0) {
                    $query->where('id', $departmentId);
                }
            })
            ->count();

        $recordsFiltered = Location::with('department.faculty')
            ->select('id')
            ->whereHas('department', function ($query) use ($departmentId) {
                if (!empty($departmentId) && $departmentId != 0) {
                    $query->where('id', $departmentId);
                }
            })
            ->when($keyword, function ($query, $keyword) {
                return $query->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'LIKE', '%' . $keyword . '%');
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
            ->editColumn('created_at', function ($data) {
                return '<small>' . date('d/m/Y', strtotime($data->created_at)) . '<br><i class="far fa-clock"></i> ' . date('h:i A', strtotime($data->created_at)) . '</small>';
            })
            ->editColumn('updated_at', function ($data) {
                return '<small>' . date('d/m/Y', strtotime($data->updated_at)) . '<br><i class="far fa-clock"></i> ' . date('h:i A', strtotime($data->updated_at)) . '</small>';
            })
            ->addColumn('department_name', function ($data) {
                return $data->department->name . '<small><br><u>คณะ</u>: ' . $data->department->faculty->name . '</small>';
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
