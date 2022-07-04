<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($facultyId)
    {
        $faculty = Faculty::find($facultyId);
        $breadcrumb = [
            ['route' => route('faculty.index'), 'name' => 'จัดการข้อมูลคณะ'],
            ['route' => route('department.index', $facultyId), 'name' => $faculty->name . ': ข้อมูลสาขาวิชา'],
        ];
        return view('department.main', [
            'breadcrumb' => $breadcrumb,
            'faculty' => $faculty,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($facultyId)
    {
        $faculty = Faculty::find($facultyId);
        $breadcrumb = [
            ['route' => route('faculty.index'), 'name' => 'จัดการข้อมูลคณะ'],
            ['route' => route('department.index', $facultyId), 'name' => $faculty->name . ': ข้อมูลสาขาวิชา'],
            ['route' => route('department.create', $facultyId), 'name' => 'เพิ่มข้อมูลสาขาวิชา'],
        ];
        return view('department.form', [
            'breadcrumb' => $breadcrumb,
            'faculty' => $faculty,
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
        $data = new Department($request->all());
        $data->save();
        return redirect()->route('department.index', $data->facultyId)->with('toast_success', 'เพิ่มข้อมูลสำเร็จ!');
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
        $department = Department::findOrFail($id);
        $faculty = Faculty::find($department->facultyId);
        $breadcrumb = [
            ['route' => route('faculty.index'), 'name' => 'จัดการข้อมูลคณะ'],
            ['route' => route('department.index', $department->facultyId), 'name' => $faculty->name . ': ข้อมูลสาขาวิชา'],
            ['route' => route('department.create', $department->facultyId), 'name' => 'แก้ไขข้อมูลสาขาวิชา'],
        ];
        return view('department.form', [
            'breadcrumb' => $breadcrumb,
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
        $data = Department::findOrFail($id);
        $data->update($request->all());
        $data->save();
        return redirect()->route('department.index', $data->facultyId)->with('toast_success', 'แก้ไขข้อมูลสำเร็จ!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Department::findOrFail($id);
        $data->delete();
        return back()->with('toast_success', 'ลบข้อมูลเรียบร้อยแล้ว!');
    }

    public function getdepartment(Request $request)
    {
        $data = Department::where('facultyId', $request->facultyId)->get();
        return $data;
    }

    public function jsontable(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $search = $request->get('search');
        $order = $request->get('order');
        $facultyId = $request->get('facultyId');


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

        $data = Department::when($keyword, function ($query, $keyword) {
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
            ->offset($start)
            ->limit($length)
            ->orderBy($sort, $dir)
            ->get();


        $recordsTotal = Department::select('id')
            ->when($facultyId, function ($query, $facultyId) {
                return $query->where(function ($query) use ($facultyId) {
                    if (!empty($facultyId) && $facultyId != 0) {
                        $query->where('facultyId', $facultyId);
                    }
                });
            })
            ->count();

        $recordsFiltered = Department::select('id')
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
            ->offset($start)
            ->limit($length)
            ->orderBy($sort, $dir)
            ->count();

        $datatable = DataTables::of($data)
            ->editColumn('id', function ($data) {
                return str_pad($data->id, 5, "0", STR_PAD_LEFT);
            })
            ->editColumn('created_at', function ($data) {
                return '<small>' . thaidate('j F Y', strtotime($data->created_at)) . '<br><i class="far fa-clock"></i> ' . date('H:i:s', strtotime($data->created_at)) . '</small>';
            })
            ->editColumn('updated_at', function ($data) {
                return '<small>' . thaidate('j F Y', strtotime($data->updated_at)) . '<br><i class="far fa-clock"></i> ' . date('H:i:s', strtotime($data->updated_at)) . '</small>';
            })
            ->addColumn('actions', function ($data) {
                $id = $data->id;
                return view('department._action', compact('id'));
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
