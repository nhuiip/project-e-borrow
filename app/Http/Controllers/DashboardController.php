<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\DurableGood;
use App\Models\DurableGoodsStatus;
use App\Models\Faculty;
use App\Models\History;
use App\Models\HistoryStatus;
use App\Models\HistoryType;
use App\Models\Location;
use App\Models\Parcel;
use App\Models\ParcelStatus;
use Auth;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DashboardController extends Controller
{
    public function index()
    {
        $breadcrumb = [
            ['route' => route('dashboard.index'), 'name' => 'สรุปผลภาพรวม'],
        ];
        $durable_goods = (object)array(
            'count' => DurableGood::all()->count(),
            'status' => array(
                DurableGoodsStatus::Inactive => array(
                    'label'  => DurableGoodsStatus::statuslabel(DurableGoodsStatus::Inactive),
                    'count'  => DurableGood::where('statusId', DurableGoodsStatus::Inactive)->get()->count(),
                    'preset' => DurableGood::all()->count() != 0 ? (DurableGood::where('statusId', DurableGoodsStatus::Inactive)->get()->count() * 100) / DurableGood::all()->count() : 100
                ),
                DurableGoodsStatus::Defective => array(
                    'label'  => DurableGoodsStatus::statuslabel(DurableGoodsStatus::Defective),
                    'count'  => DurableGood::where('statusId', DurableGoodsStatus::Defective)->get()->count(),
                    'preset' => DurableGood::all()->count() != 0 ? (DurableGood::where('statusId', DurableGoodsStatus::Defective)->get()->count() * 100) / DurableGood::all()->count() : 100
                ),
                DurableGoodsStatus::Active => array(
                    'label'  => DurableGoodsStatus::statuslabel(DurableGoodsStatus::Active),
                    'count'  => DurableGood::where('statusId', DurableGoodsStatus::Active)->get()->count(),
                    'preset' => DurableGood::all()->count() != 0 ? (DurableGood::where('statusId', DurableGoodsStatus::Active)->get()->count() * 100) / DurableGood::all()->count() : 100
                ),
                DurableGoodsStatus::Pending_Approval => array(
                    'label'  => DurableGoodsStatus::statuslabel(DurableGoodsStatus::Pending_Approval),
                    'count'  => DurableGood::where('statusId', DurableGoodsStatus::Pending_Approval)->get()->count(),
                    'preset' => DurableGood::all()->count() != 0 ? (DurableGood::where('statusId', DurableGoodsStatus::Pending_Approval)->get()->count() * 100) / DurableGood::all()->count() : 100
                ),
                DurableGoodsStatus::Waiting_Return => array(
                    'label'  => DurableGoodsStatus::statuslabel(DurableGoodsStatus::Waiting_Return),
                    'count'  => DurableGood::where('statusId', DurableGoodsStatus::Waiting_Return)->get()->count(),
                    'preset' => DurableGood::all()->count() != 0 ? (DurableGood::where('statusId', DurableGoodsStatus::Waiting_Return)->get()->count() * 100) / DurableGood::all()->count() : 100
                ),
            )
        );

        $parcel = (object)array(
            'count' => Parcel::all()->count(),
            'status' => array(
                ParcelStatus::Inactive => array(
                    'label'  => ParcelStatus::statuslabel(ParcelStatus::Inactive),
                    'count'  => Parcel::where('statusId', ParcelStatus::Inactive)->get()->count(),
                    'preset' => Parcel::all()->count() != 0 ? (Parcel::where('statusId', ParcelStatus::Inactive)->get()->count() * 100) / Parcel::all()->count() : 100
                ),
                ParcelStatus::Out_Of_Stock => array(
                    'label'  => ParcelStatus::statuslabel(ParcelStatus::Out_Of_Stock),
                    'count'  => Parcel::where('statusId', ParcelStatus::Out_Of_Stock)->get()->count(),
                    'preset' => Parcel::all()->count() != 0 ? (Parcel::where('statusId', ParcelStatus::Out_Of_Stock)->get()->count() * 100) / Parcel::all()->count() : 100
                ),
                ParcelStatus::Active => array(
                    'label'  => ParcelStatus::statuslabel(ParcelStatus::Active),
                    'count'  => Parcel::where('statusId', ParcelStatus::Active)->get()->count(),
                    'preset' => Parcel::all()->count() != 0 ? (Parcel::where('statusId', ParcelStatus::Active)->get()->count() * 100) / Parcel::all()->count() : 100
                ),
            )
        );

        $department = null;
        if (Auth::user()->hasRole('Admin')) {
            $department = Faculty::with('departments')->get();
        } else {
            $departmentId = Auth::user()->departmentId;
            $department = Department::where('id', $departmentId)->get();
        }

        return view('dashboard.main', [
            'breadcrumb' => $breadcrumb,
            'durable_goods' => $durable_goods,
            'parcel' => $parcel,
            'department' => $department,
            'location' => Location::all(),
            'status' => HistoryStatus::all(),
            'type' => HistoryType::all(),
        ]);
    }

    public function jsontable(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $search = $request->get('search');
        $order = $request->get('order');

        // filter
        if (!Auth::user()->hasRole('Admin')) {
            $departmentId = Auth::user()->departmentId;
        }

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

        $data = History::with('durable_good.department', 'parcel.department', 'history_type', 'history_status')
            ->when($keyword, function ($query, $keyword) {
                return $query->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'LIKE', '%' . $keyword . '%');
                });
            })
            ->offset($start)
            ->limit($length)
            ->orderBy($sort, $dir)
            ->get();


        $recordsTotal = History::with('durable_good.department', 'parcel.department', 'history_type', 'history_status')
            ->select('id')
            ->count();

        $recordsFiltered = History::with('durable_good.department', 'parcel.department', 'history_type', 'history_status')
            ->select('id')
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
                return '<small>' . thaidate('j F Y', strtotime($data->created_at)) . '<br><i class="far fa-clock"></i> ' . date('H:i:s', strtotime($data->created_at)) . '</small>';
            })
            ->editColumn('updated_at', function ($data) {
                return '<small>' . thaidate('j F Y', strtotime($data->updated_at)) . '<br><i class="far fa-clock"></i> ' . date('H:i:s', strtotime($data->updated_at)) . '</small>';
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
