<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\DurableGood;
use App\Models\DurableGoodsImage;
use App\Models\DurableGoodsStatus;
use App\Models\Faculty;
use App\Models\History;
use App\Models\HistoryStatus;
use App\Models\HistoryType;
use App\Models\Location;
use App\Models\Parcel;
use App\Models\ParcelImage;
use App\Models\ParcelStatus;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Carbon;

class HistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function parcel()
    {
        $breadcrumb = [
            ['route' => route('history.parcel'), 'name' => '  รายการรออนุมัติ'],
        ];

        $location = Location::select('name', 'id')->get();
        $status = HistoryStatus::find(HistoryStatus::Status_Pending_Approval);
        $type = HistoryType::find(HistoryType::Type_Parcel);
        $department = null;
        if (Auth::user()->hasRole('Admin')) {
            $department = Faculty::with('departments')->get();
        } else {
            $departmentId = Auth::user()->departmentId;
            $department = Department::where('id', $departmentId)->get();
        }
        return view('parcel.main_approve', [
            'breadcrumb' => $breadcrumb,
            'location' => $location,
            'department' => $department,
            'status' => $status,
            'type' => $type,
        ]);
    }

    public function durablegood_approve()
    {
        $breadcrumb = [
            ['route' => route('history.durablegood_approve'), 'name' => '  รายการรออนุมัติ'],
        ];

        $location = Location::select('name', 'id')->get();
        $status = HistoryStatus::find(HistoryStatus::Status_Pending_Approval);
        $type = HistoryType::find(HistoryType::Type_DurableGoods);
        $department = null;
        if (Auth::user()->hasRole('Admin')) {
            $department = Faculty::with('departments')->get();
        } else {
            $departmentId = Auth::user()->departmentId;
            $department = Department::where('id', $departmentId)->get();
        }
        return view('durablegood.main_approve', [
            'breadcrumb' => $breadcrumb,
            'location' => $location,
            'department' => $department,
            'status' => $status,
            'type' => $type,
        ]);
    }

    public function durablegood_return()
    {
        $breadcrumb = [
            ['route' => route('history.durablegood_return'), 'name' => '  รายการรออนุมัติ'],
        ];

        $location = Location::select('name', 'id')->get();
        $status = HistoryStatus::find(HistoryStatus::Status_Approval);
        $type = HistoryType::find(HistoryType::Type_DurableGoods);
        $department = null;
        if (Auth::user()->hasRole('Admin')) {
            $department = Faculty::with('departments')->get();
        } else {
            $departmentId = Auth::user()->departmentId;
            $department = Department::where('id', $departmentId)->get();
        }
        return view('durablegood.main_return', [
            'breadcrumb' => $breadcrumb,
            'location' => $location,
            'department' => $department,
            'status' => $status,
            'type' => $type,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->action == 'parcel') {
            $stock_now = Parcel::findOrFail($request->parcelId);
            $this->validate(
                $request,
                [
                    'unit' => 'required|numeric|max:' . $stock_now->stock,
                ],
                [
                    'unit.required' => 'กรุณากรอกจำนวน',
                    'unit.max' => 'สามารถเบิกของออกได้ไม่เกิน ' . $stock_now->stock . ' ' . $stock_now->stock_unit,
                ]
            );
        }
        $data = new History($request->all());
        $data->save();

        if ($request->action == 'parcel') {
            $stock_now = Parcel::findOrFail($data->parcelId);
            if ($data->parcelId != null) {
                $stock_now->stock = $stock_now->stock - $data->unit;
                $stock_now->save();
            }
            $stock_now = Parcel::findOrFail($data->parcelId);
            if ($stock_now->stock <= 0) {
                $stock_now->statusId = ParcelStatus::Out_Of_Stock;
                $stock_now->save();
            }
        }

        if ($request->action == 'durablegood' && $data->durablegoodsId != null) {
            $durablegood = DurableGood::findOrFail($data->durablegoodsId);
            $durablegood->statusId = DurableGoodsStatus::Pending_Approval;
            $durablegood->save();
        }

        if ($request->action == 'parcel') {
            return redirect()->route('parcel.withdraw', $data->parcelId)->with('toast_success', 'ทำรายการสำเร็จ!');
        } elseif ($request->action == 'durablegood') {
            return redirect()->route('durablegood.withdraw', $data->durablegoodsId)->with('toast_success', 'ทำรายการสำเร็จ!');
        }
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
        //
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
        $data = History::findOrFail($id);
        $data->update($request->all());
        $data->save();

        if ($request->action == 'approval durablegood') {
            $durablegood = DurableGood::findOrFail($data->durablegoodsId);
            $durablegood->statusId = DurableGoodsStatus::Waiting_Return;
            $durablegood->save();
        }
        if ($request->action == 'return durablegood') {
            $durablegood = DurableGood::findOrFail($data->durablegoodsId);
            $durablegood->statusId = DurableGoodsStatus::Active;
            $durablegood->save();
        }

        if ($request->action == 'approval parcel') {
            return redirect()->route('history.parcel')->with('toast_success', 'ทำรายการสำเร็จ!');
        } elseif ($request->action == 'approval durablegood') {
            return redirect()->route('history.durablegood_approve')->with('toast_success', 'ทำรายการสำเร็จ!');
        } elseif ($request->action == 'return durablegood') {
            return redirect()->route('history.durablegood_return')->with('toast_success', 'ทำรายการสำเร็จ!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
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
        $typeId = $request->get('typeId');
        $statusId = $request->get('statusId');
        $startDate = $request->get('startDate');
        $endDate = $request->get('endDate');

        $columnorder = array(
            'id',
            'created_at',
            'updated_at',
        );

        if (empty($order)) {
            $sort = 'created_at';
            $dir = 'desc';
        } else {
            $sort = $columnorder[$order[0]['column']];
            $dir = $order[0]['dir'];
        }

        $data = History::with('parcel.department', 'history_status', 'history_type')
            ->when($departmentId, function ($query, $departmentId) {
                return $query->where(function ($query) use ($departmentId) {
                    if (!empty($departmentId) && $departmentId != 0) {
                        $query->orWhereHas('parcel.department', function ($query) use ($departmentId) {
                            $query->where('id', $departmentId);
                        })->orWhereHas('durable_good.department', function ($query) use ($departmentId) {
                            $query->where('id', $departmentId);
                        });
                    }
                });
            })
            ->when($locationId, function ($query, $locationId) {
                return $query->where(function ($query) use ($locationId) {
                    if (!empty($locationId) && $locationId != 0) {
                        $query->orWhereHas('parcel.location', function ($query) use ($locationId) {
                            $query->where('id', $locationId);
                        })->orWhereHas('durable_good.location', function ($query) use ($locationId) {
                            $query->where('id', $locationId);
                        });
                    }
                });
            })
            ->when($statusId, function ($query, $statusId) {
                return $query->where(function ($query) use ($statusId) {
                    if (!empty($statusId) && $statusId != 0) {
                        $query->where('statusId', $statusId);
                    }
                });
            })
            ->when($typeId, function ($query, $typeId) {
                return $query->where(function ($query) use ($typeId) {
                    if (!empty($typeId) && $typeId != 0) {
                        $query->where('typeId', $typeId);
                    }
                });
            })
            ->when($startDate, function ($query, $startDate) {
                return $query->where(function ($query) use ($startDate) {
                    if (!empty($startDate) && ($startDate != null || $startDate != 'null')) {
                        $query->where('created_at', '>=', $startDate);
                    }
                });
            })
            ->when($endDate, function ($query, $endDate) {
                return $query->where(function ($query) use ($endDate) {
                    if (!empty($endDate) && ($endDate != null || $endDate != 'null')) {
                        $query->where('created_at', '<=', $endDate);
                    }
                });
            })
            ->offset($start)
            ->limit($length)
            ->orderBy($sort, $dir)
            ->get();


        $recordsTotal = History::with('parcel.department')
            ->select('id')
            ->when($departmentId, function ($query, $departmentId) {
                return $query->where(function ($query) use ($departmentId) {
                    if (!empty($departmentId) && $departmentId != 0) {
                        $query->orWhereHas('parcel.department', function ($query) use ($departmentId) {
                            $query->where('id', $departmentId);
                        })->orWhereHas('durable_good.department', function ($query) use ($departmentId) {
                            $query->where('id', $departmentId);
                        });
                    }
                });
            })
            ->when($locationId, function ($query, $locationId) {
                return $query->where(function ($query) use ($locationId) {
                    if (!empty($locationId) && $locationId != 0) {
                        $query->orWhereHas('parcel.location', function ($query) use ($locationId) {
                            $query->where('id', $locationId);
                        })->orWhereHas('durable_good.location', function ($query) use ($locationId) {
                            $query->where('id', $locationId);
                        });
                    }
                });
            })
            ->when($statusId, function ($query, $statusId) {
                return $query->where(function ($query) use ($statusId) {
                    if (!empty($statusId) && $statusId != 0) {
                        $query->where('statusId', $statusId);
                    }
                });
            })
            ->when($typeId, function ($query, $typeId) {
                return $query->where(function ($query) use ($typeId) {
                    if (!empty($typeId) && $typeId != 0) {
                        $query->where('typeId', $typeId);
                    }
                });
            })
            ->when($startDate, function ($query, $startDate) {
                return $query->where(function ($query) use ($startDate) {
                    if (!empty($startDate) && ($startDate != null || $startDate != 'null')) {
                        $query->where('created_at', '>=', $startDate);
                    }
                });
            })
            ->when($endDate, function ($query, $endDate) {
                return $query->where(function ($query) use ($endDate) {
                    if (!empty($endDate) && ($endDate != null || $endDate != 'null')) {
                        $query->where('created_at', '<=', $endDate);
                    }
                });
            })
            ->count();

        $recordsFiltered = History::with('parcel.department')
            ->select('id')
            ->when($departmentId, function ($query, $departmentId) {
                return $query->where(function ($query) use ($departmentId) {
                    if (!empty($departmentId) && $departmentId != 0) {
                        $query->orWhereHas('parcel.department', function ($query) use ($departmentId) {
                            $query->where('id', $departmentId);
                        })->orWhereHas('durable_good.department', function ($query) use ($departmentId) {
                            $query->where('id', $departmentId);
                        });
                    }
                });
            })
            ->when($locationId, function ($query, $locationId) {
                return $query->where(function ($query) use ($locationId) {
                    if (!empty($locationId) && $locationId != 0) {
                        $query->orWhereHas('parcel.location', function ($query) use ($locationId) {
                            $query->where('id', $locationId);
                        })->orWhereHas('durable_good.location', function ($query) use ($locationId) {
                            $query->where('id', $locationId);
                        });
                    }
                });
            })
            ->when($statusId, function ($query, $statusId) {
                return $query->where(function ($query) use ($statusId) {
                    if (!empty($statusId) && $statusId != 0) {
                        $query->where('statusId', $statusId);
                    }
                });
            })
            ->when($typeId, function ($query, $typeId) {
                return $query->where(function ($query) use ($typeId) {
                    if (!empty($typeId) && $typeId != 0) {
                        $query->where('typeId', $typeId);
                    }
                });
            })
            ->when($startDate, function ($query, $startDate) {
                return $query->where(function ($query) use ($startDate) {
                    if (!empty($startDate) && ($startDate != null || $startDate != 'null')) {
                        $query->where('created_at', '>=', $startDate);
                    }
                });
            })
            ->when($endDate, function ($query, $endDate) {
                return $query->where(function ($query) use ($endDate) {
                    if (!empty($endDate) && ($endDate != null || $endDate != 'null')) {
                        $query->where('created_at', '<=', $endDate);
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
                $user = User::findOrFail($data->created_userId);
                return '<small>' . $user->name . '<br>' . thaidate('j F Y', strtotime($data->created_at)) . '<br><i class="far fa-clock"></i> ' . date('H:i:s', strtotime($data->created_at)) . '</small>';
            })
            ->editColumn('updated_at', function ($data) {
                return '<small>' . thaidate('j F Y', strtotime($data->updated_at)) . '<br><i class="far fa-clock"></i> ' . date('H:i:s', strtotime($data->updated_at)) . '</small>';
            })
            ->editColumn('approved_at', function ($data) {
                $label = "";
                if ($data->approved_userId != null) {
                    $user = User::findOrFail($data->approved_userId);
                    $label = '<small>' . $user->name . '<br>' . thaidate('j F Y', strtotime($data->approved_at)) . '<br><i class="far fa-clock"></i> ' . date('H:i:s', strtotime($data->approved_at)) . '</small>';
                }
                return $label;
            })
            ->editColumn('returned_at', function ($data) {
                $label = "";
                if ($data->returned_userId != null) {
                    $user = User::findOrFail($data->returned_userId);
                    $label = '<small>' . $user->name . '<br>' . thaidate('j F Y', strtotime($data->returned_at)) . '<br><i class="far fa-clock"></i> ' . date('H:i:s', strtotime($data->returned_at)) . '</small>';
                }
                return $label;
            })
            ->addColumn('department_info', function ($data) {
                if ($data->parcelId != null) {
                    return $data->parcel->department->name . '<small><br><u>คณะ</u>: ' . $data->parcel->department->faculty->name . '</small>';
                } else {
                    return $data->durable_good->department->name . '<small><br><u>คณะ</u>: ' . $data->durable_good->department->faculty->name . '</small>';
                }
            })
            ->addColumn('image', function ($data) {
                $cover = asset('img/no-image.jpeg');
                if ($data->parcelId != null) {

                    if (ParcelImage::where('parcelId', $data->parcelId)->first() != null) {
                        $cover = asset('storage/ParcelImage/' . ParcelImage::where('parcelId', $data->parcelId)->first()->name);
                    }
                    $id = $data->parcelId;
                    $image = ParcelImage::where('parcelId', $data->parcelId)->get();

                    return view('parcel._image', compact('id', 'cover', 'image'));
                } else {
                    if (DurableGoodsImage::where('durablegoodsId', $data->durablegoodsId)->first() != null) {
                        $cover = asset('storage/DurableGoodsImage/' . DurableGoodsImage::where('durablegoodsId', $data->durablegoodsId)->first()->name);
                    }
                    $id = $data->durablegoodsId;
                    $image = DurableGoodsImage::where('durablegoodsId', $data->durablegoodsId)->get();

                    return view('durablegood._image', compact('id', 'cover', 'image'));
                }
            })
            ->addColumn('location_info', function ($data) {
                if ($data->parcelId != null) {
                    return $data->parcel->location->name . '<small><br><u>คณะ</u>: ' . $data->parcel->department->faculty->name . '</small><small><br><u>สาขา</u>: ' . $data->parcel->department->name . '</small>';
                } else {
                    return $data->durable_good->location->name . '<small><br><u>คณะ</u>: ' . $data->durable_good->department->faculty->name . '</small><small><br><u>สาขา</u>: ' . $data->durable_good->department->name . '</small>';
                }
            })
            ->addColumn('stock', function ($data) {
                return $data->parcelId != null ? $data->parcel->stock : $data->durable_good->stock;
            })
            ->addColumn('stock_unit', function ($data) {
                return $data->parcelId != null ? $data->parcel->stock_unit : "";
            })
            ->addColumn('department_info', function ($data) {
                if ($data->parcelId != null) {
                    return $data->parcel->department->name . '<small><br><u>คณะ</u>: ' . $data->parcel->department->faculty->name . '</small>';
                } else {
                    return $data->durable_good->department->name . '<small><br><u>คณะ</u>: ' . $data->durable_good->department->faculty->name . '</small>';
                }
            })
            ->addColumn('name', function ($data) {
                return $data->parcelId != null ? $data->parcel->name : $data->durable_good->name;
            })
            ->addColumn('reference', function ($data) {
                return $data->parcelId != null ? $data->parcel->reference : $data->durable_good->reference;
            })
            ->addColumn('actions', function ($data) {
                $id = $data->id;
                if ($data->parcelId != null) {
                    return view('parcel._action_approve', compact('id'));
                } else {
                    if ($data->statusId == HistoryStatus::Status_Pending_Approval) {

                        return view('durablegood._action_approve', compact('id'));
                    } elseif ($data->statusId == HistoryStatus::Status_Approval) {
                        return view('durablegood._action_return', compact('id'));
                    }
                }
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
