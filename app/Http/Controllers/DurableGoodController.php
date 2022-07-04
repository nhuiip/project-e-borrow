<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\DurableGood;
use App\Models\DurableGoodsImage;
use App\Models\DurableGoodsStatus;
use App\Models\Faculty;
use App\Models\Location;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DurableGoodController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $breadcrumb = [
            ['route' => route('durablegood.index'), 'name' => 'จัดการข้อมูลครุภัณฑ์'],
        ];
        $location = Location::select('name', 'id')->get();
        $status = DurableGoodsStatus::all();
        $department = null;
        if (Auth::user()->hasRole('Admin')) {
            $department = Faculty::with('departments')->get();
        } else {
            $departmentId = Auth::user()->departmentId;
            $department = Department::where('id', $departmentId)->get();
        }
        return view('durablegood.main', [
            'breadcrumb' => $breadcrumb,
            'department' => $department,
            'location' => $location,
            'status' => $status,
        ]);
    }

    public function withdraw()
    {
        $breadcrumb = [
            ['route' => route('durablegood.withdraw'), 'name' => ' เบิกครุภัณฑ์'],
        ];

        $location = Location::select('name', 'id')->get();
        $status = DurableGoodsStatus::find(DurableGoodsStatus::Active);
        $department = null;
        if (Auth::user()->hasRole('Admin')) {
            $department = Faculty::with('departments')->get();
        } else {
            $departmentId = Auth::user()->departmentId;
            $department = Department::where('id', $departmentId)->get();
        }
        return view('durablegood.main_withdraw', [
            'breadcrumb' => $breadcrumb,
            'location' => $location,
            'department' => $department,
            'status' => $status,
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
            ['route' => route('durablegood.index'), 'name' => 'จัดการข้อมูลครุภัณฑ์'],
            ['route' => '#', 'name' => 'เพิ่มข้อมูลครุภัณฑ์'],
        ];

        $location = array('' => 'เลือกที่จัดเก็บ') + Location::select('name', 'id')->get()->pluck('name', 'id')->toArray();
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
        return view('durablegood.form', [
            'breadcrumb' => $breadcrumb,
            'department' => $department,
            'location' => $location,
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
                'reference' => 'required|max:100',
                'name' => 'required|max:100',
                'departmentId' => 'required',
                'locationId' => 'required',
                'file_images.*' => 'mimes:png,jpg,webp',
            ],
            [
                'reference.required' => 'กรุณากรอกเลขที่ครุภัณฑ์',
                'reference.max' => 'เลขที่ครุภัณฑ์ต้องไม่เกิน 100 ตัวอักษร',
                'name.required' => 'กรุณากรอกชื่อ',
                'name.max' => 'ชื่อต้องไม่เกิน 100 ตัวอักษร',
                'departmentId.required' => 'กรุณาเลือกสาขาวิชา',
                'locationId.required' => 'กรุณาเลือกสถานที่จัดเก็บ',
                'file_images.mimes' => 'รูปแบบไฟล์ภาพผิดพลาดกรุณาอัพโหลดเฉพาะไฟล์รูปภาพเท่านั้น',
            ]
        );
        $data = new DurableGood($request->all());
        $data->save();

        if ($request->hasfile('file_images')) {
            foreach ($request->file('file_images') as $image) {
                // new file
                $newFilename = uniqid() . '.' . $image->extension();
                $file = $image;
                $file->move('storage/DurableGoodsImage/', $newFilename);

                $img = new DurableGoodsImage();
                $img->name = $newFilename;
                $img->durablegoodsId = $data->id;
                $img->created_userId = Auth::user()->id;
                $img->save();
            }
        }
        return redirect()->route('durablegood.index')->with('toast_success', 'เพิ่มข้อมูลสำเร็จ!');
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
            ['route' => route('durablegood.index'), 'name' => 'จัดการข้อมูลครุภัณฑ์'],
            ['route' => '#', 'name' => 'แก้ไขข้อมูลครุภัณฑ์'],
        ];

        $location = array('' => 'เลือกที่จัดเก็บ') + Location::select('name', 'id')->get()->pluck('name', 'id')->toArray();
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
        return view('durablegood.form', [
            'breadcrumb' => $breadcrumb,
            'durablegood' => DurableGood::findOrFail($id),
            'department' => $department,
            'location' => $location,
            'image' => DurableGoodsImage::where('durablegoodsId', $id)->get()
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
        if ($request->action == 'durablegood form') {
            $this->validate(
                $request,
                [
                    'reference' => 'required|max:100',
                    'name' => 'required|max:100',
                    'departmentId' => 'required',
                    'locationId' => 'required',
                    'file_images.*' => 'mimes:png,jpg,webp',
                ],
                [
                    'reference.required' => 'กรุณากรอกเลขที่ครุภัณฑ์',
                    'reference.max' => 'เลขที่ครุภัณฑ์ต้องไม่เกิน 100 ตัวอักษร',
                    'name.required' => 'กรุณากรอกชื่อ',
                    'name.max' => 'ชื่อต้องไม่เกิน 100 ตัวอักษร',
                    'departmentId.required' => 'กรุณาเลือกสาขาวิชา',
                    'locationId.required' => 'กรุณาเลือกสถานที่จัดเก็บ',
                    'file_images.mimes' => 'รูปแบบไฟล์ภาพผิดพลาดกรุณาอัพโหลดเฉพาะไฟล์รูปภาพเท่านั้น',
                ]
            );
        }
        $data = DurableGood::findOrFail($id);
        $data->update($request->all());
        $data->save();

        if ($request->hasfile('file_images')) {
            foreach ($request->file('file_images') as $image) {
                // new file
                $newFilename = uniqid() . '.' . $image->extension();
                $file = $image;
                $file->move('storage/DurableGoodsImage/', $newFilename);

                $img = new DurableGoodsImage();
                $img->name = $newFilename;
                $img->durablegoodsId = $data->id;
                $img->created_userId = Auth::user()->id;
                $img->save();
            }
        }
        return redirect()->route('durablegood.index')->with('toast_success', 'แก้ไขข้อมูลสำเร็จ!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = DurableGood::findOrFail($id);
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
        $locationId = $request->get('locationId');
        $statusId = $request->get('statusId');


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

        $data = DurableGood::with('department.faculty', 'location', 'durable_goods_status')
            ->whereHas('department', function ($query) use ($departmentId) {
                if (!empty($departmentId) && $departmentId != 0) {
                    $query->where('id', $departmentId);
                }
            })
            ->when($locationId, function ($query, $locationId) {
                if (!empty($locationId) && $locationId != 0) {
                    $query->where('locationId', $locationId);
                }
            })
            ->when($statusId, function ($query, $statusId) {
                if (!empty($statusId) && $statusId != 0) {
                    $query->where('statusId', $statusId);
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


        $recordsTotal = DurableGood::with('department.faculty', 'location', 'durable_goods_status')
            ->select('id')
            ->whereHas('department', function ($query) use ($departmentId) {
                if (!empty($departmentId) && $departmentId != 0) {
                    $query->where('id', $departmentId);
                }
            })
            ->when($locationId, function ($query, $locationId) {
                if (!empty($locationId) && $locationId != 0) {
                    $query->where('locationId', $locationId);
                }
            })
            ->when($statusId, function ($query, $statusId) {
                if (!empty($statusId) && $statusId != 0) {
                    $query->where('statusId', $statusId);
                }
            })
            ->count();

        $recordsFiltered = DurableGood::with('department.faculty', 'location', 'durable_goods_status')
            ->select('id')
            ->whereHas('department', function ($query) use ($departmentId) {
                if (!empty($departmentId) && $departmentId != 0) {
                    $query->where('id', $departmentId);
                }
            })
            ->when($locationId, function ($query, $locationId) {
                if (!empty($locationId) && $locationId != 0) {
                    $query->where('locationId', $locationId);
                }
            })
            ->when($statusId, function ($query, $statusId) {
                if (!empty($statusId) && $statusId != 0) {
                    $query->where('statusId', $statusId);
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
                $user = User::find($data->created_userId);
                $username = $user != null ? $user->name : "";
                return '<small>' . $username . '<br>' . thaidate('j F Y', strtotime($data->created_at)) . '<br><i class="far fa-clock"></i> ' . date('H:i:s', strtotime($data->created_at)) . '</small>';
            })
            ->editColumn('updated_at', function ($data) {
                $user = User::find($data->updated_userId);
                $username = $user != null ? $user->name : "";
                return '<small>' . $username . '<br>' . thaidate('j F Y', strtotime($data->updated_at)) . '<br><i class="far fa-clock"></i> ' . date('H:i:s', strtotime($data->updated_at)) . '</small>';
            })
            ->addColumn('department_info', function ($data) {
                return $data->department->name . '<small><br><u>คณะ</u>: ' . $data->department->faculty->name . '</small>';
            })
            ->addColumn('location_info', function ($data) {
                return $data->location->name . '<small><br><u>คณะ</u>: ' . $data->department->faculty->name . '</small><small><br><u>สาขา</u>: ' . $data->department->name . '</small>';
            })
            ->addColumn('image', function ($data) {
                $id = $data->id;
                $cover = asset('img/no-image.jpeg');
                if (DurableGoodsImage::where('durablegoodsId', $data->id)->first() != null) {
                    $cover = asset('storage/DurableGoodsImage/' . DurableGoodsImage::where('durablegoodsId', $data->id)->first()->name);
                }
                $image = DurableGoodsImage::where('durablegoodsId', $data->id)->get();

                return view('durablegood._image', compact('id', 'cover', 'image'));
            })
            ->addColumn('actions', function ($data) {
                $id = $data->id;
                $statusId = $data->statusId;
                return view('durablegood._action', compact('id', 'statusId'));
            })
            ->setTotalRecords($recordsTotal)
            ->setFilteredRecords($recordsFiltered)
            ->escapeColumns([])
            ->skipPaging()
            ->addIndexColumn()
            ->make(true);
        return $datatable;
    }

    public function jsontable_withdraw(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $search = $request->get('search');
        $order = $request->get('order');

        // filter
        $departmentId = $request->get('departmentId');
        $locationId = $request->get('locationId');
        $statusId = $request->get('statusId');

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

        $data = DurableGood::with('department.faculty', 'location', 'durable_goods_status')
            ->whereHas('department', function ($query) use ($departmentId) {
                if (!empty($departmentId) && $departmentId != 0) {
                    $query->where('id', $departmentId);
                }
            })
            ->when($locationId, function ($query, $locationId) {
                if (!empty($locationId) && $locationId != 0) {
                    $query->where('locationId', $locationId);
                }
            })
            ->when($statusId, function ($query, $statusId) {
                if (!empty($statusId) && $statusId != 0) {
                    $query->where('statusId', $statusId);
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


        $recordsTotal = DurableGood::with('department.faculty', 'location', 'durable_goods_status')
            ->select('id')
            ->whereHas('department', function ($query) use ($departmentId) {
                if (!empty($departmentId) && $departmentId != 0) {
                    $query->where('id', $departmentId);
                }
            })
            ->when($locationId, function ($query, $locationId) {
                if (!empty($locationId) && $locationId != 0) {
                    $query->where('locationId', $locationId);
                }
            })
            ->when($statusId, function ($query, $statusId) {
                if (!empty($statusId) && $statusId != 0) {
                    $query->where('statusId', $statusId);
                }
            })
            ->count();

        $recordsFiltered = DurableGood::with('department.faculty', 'location', 'durable_goods_status')
            ->select('id')
            ->whereHas('department', function ($query) use ($departmentId) {
                if (!empty($departmentId) && $departmentId != 0) {
                    $query->where('id', $departmentId);
                }
            })
            ->when($locationId, function ($query, $locationId) {
                if (!empty($locationId) && $locationId != 0) {
                    $query->where('locationId', $locationId);
                }
            })
            ->when($statusId, function ($query, $statusId) {
                if (!empty($statusId) && $statusId != 0) {
                    $query->where('statusId', $statusId);
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
                $user = User::find($data->created_userId);
                $username = $user != null ? $user->name : "";
                return '<small>' . $username . '<br>' . thaidate('j F Y', strtotime($data->created_at)) . '<br><i class="far fa-clock"></i> ' . date('H:i:s', strtotime($data->created_at)) . '</small>';
            })
            ->editColumn('updated_at', function ($data) {
                $user = User::find($data->updated_userId);
                $username = $user != null ? $user->name : "";
                return '<small>' . $username . '<br>' . thaidate('j F Y', strtotime($data->updated_at)) . '<br><i class="far fa-clock"></i> ' . date('H:i:s', strtotime($data->updated_at)) . '</small>';
            })
            ->addColumn('department_info', function ($data) {
                return $data->department->name . '<small><br><u>คณะ</u>: ' . $data->department->faculty->name . '</small>';
            })
            ->addColumn('location_info', function ($data) {
                return $data->location->name . '<small><br><u>คณะ</u>: ' . $data->department->faculty->name . '</small><small><br><u>สาขา</u>: ' . $data->department->name . '</small>';
            })
            ->addColumn('image', function ($data) {
                $id = $data->id;
                $cover = asset('img/no-image.jpeg');
                if (DurableGoodsImage::where('durablegoodsId', $data->id)->first() != null) {
                    $cover = asset('storage/DurableGoodsImage/' . DurableGoodsImage::where('durablegoodsId', $data->id)->first()->name);
                }
                $image = DurableGoodsImage::where('durablegoodsId', $data->id)->get();

                return view('durablegood._image', compact('id', 'cover', 'image'));
            })
            ->addColumn('actions', function ($data) {
                $id = $data->id;
                $statusId = $data->statusId;

                return view('durablegood._action_withdraw', compact('id', 'statusId'));
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
