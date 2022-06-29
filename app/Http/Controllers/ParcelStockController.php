<?php

namespace App\Http\Controllers;

use App\Models\Parcel;
use App\Models\ParcelStatus;
use App\Models\ParcelStock;
use App\Models\ParcelStockType;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ParcelStockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($parcelId)
    {
        $parcel = Parcel::find($parcelId);
        $breadcrumb = [
            ['route' => route('parcel.index'), 'name' => 'จัดการข้อมูลพัสดุ'],
            ['route' => route('parcelstock.index', $parcelId), 'name' => $parcel->name . ': ข้อมูลคลังพัสดุ'],
        ];

        $stocktype = ParcelStockType::all();
        return view('parcel_stock.main', [
            'breadcrumb' => $breadcrumb,
            'parcel' => $parcel,
            'stocktype' => $stocktype,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($parcelId)
    {
        $parcel = Parcel::find($parcelId);
        $breadcrumb = [
            ['route' => route('parcel.index'), 'name' => 'จัดการข้อมูลพัสดุ'],
            ['route' => route('parcelstock.index', $parcelId), 'name' => $parcel->name . ': ข้อมูลคลังพัสดุ'],
            ['route' => route('parcelstock.create', $parcelId), 'name' => 'เพิ่มข้อมูล'],
        ];
        $type = array('' => 'เลือกที่ประเภทรายการ') + ParcelStockType::select('name', 'id')->get()->pluck('name', 'id')->toArray();
        return view('parcel_stock.form', [
            'breadcrumb' => $breadcrumb,
            'parcel' => $parcel,
            'type' => $type,
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
        $stock_now = Parcel::findOrFail($request->parcelId);
        $this->validate(
            $request,
            [
                'typeId' => 'required',
            ],
            [
                'typeId.required' => 'กรุณาเลือกประเภทรายการ',
            ]
        );
        if ($request->typeId == ParcelStockType::Withdraw) {
            $this->validate(
                $request,
                [
                    'stock' => 'required|numeric|max:' . $stock_now->stock,
                ],
                [
                    'stock.required' => 'กรุณากรอกจำนวน',
                    'stock.max' => 'สามารถนำของออกได้ไม่เกิน ' . $stock_now->stock . ' ' . $stock_now->stock_unit,
                ]
            );
        }

        $data = new ParcelStock($request->all());
        $data->save();

        if ($data->typeId == ParcelStockType::Add) {
            $stock_now->stock = $stock_now->stock + $data->stock;
            $stock_now->save();
        } else {
            $stock_now->stock = $stock_now->stock - $data->stock;
            $stock_now->save();
        }

        if ($stock_now->stock <= 0) {
            $stock_now->statusId = ParcelStatus::Out_Of_Stock;
            $stock_now->save();
        }

        if($stock_now->stock > 0 && $stock_now->stock != ParcelStatus::Inactive){
            $stock_now->statusId = ParcelStatus::Active;
            $stock_now->save();
        }

        return redirect()->route('parcelstock.index', $data->parcelId)->with('toast_success', 'เพิ่มข้อมูลสำเร็จ!');
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
        //
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

        $parcelId = $request->get('parcelId');


        $columnorder = array(
            'id',
            'name',
            'created_at',
            'updated_at',
        );

        if (empty($order)) {
            $sort = 'created_at';
            $dir = 'asc';
        } else {
            $sort = $columnorder[$order[0]['column']];
            $dir = $order[0]['dir'];
        }

        $data = ParcelStock::with('parcel_stock_type')
            ->when($parcelId, function ($query, $parcelId) {
                return $query->where(function ($query) use ($parcelId) {
                    if (!empty($parcelId) && $parcelId != 0) {
                        $query->where('parcelId', $parcelId);
                    }
                });
            })
            ->offset($start)
            ->limit($length)
            ->orderBy($sort, $dir)
            ->get();


        $recordsTotal = ParcelStock::select('id')
            ->when($parcelId, function ($query, $parcelId) {
                return $query->where(function ($query) use ($parcelId) {
                    if (!empty($parcelId) && $parcelId != 0) {
                        $query->where('parcelId', $parcelId);
                    }
                });
            })
            ->count();

        $recordsFiltered = ParcelStock::select('id')
            ->when($parcelId, function ($query, $parcelId) {
                return $query->where(function ($query) use ($parcelId) {
                    if (!empty($parcelId) && $parcelId != 0) {
                        $query->where('parcelId', $parcelId);
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
                return '<small>' . date('d/m/Y', strtotime($data->created_at)) . '<br><i class="far fa-clock"></i> ' . date('h:i A', strtotime($data->created_at)) . '</small>';
            })
            ->editColumn('updated_at', function ($data) {
                return '<small>' . date('d/m/Y', strtotime($data->updated_at)) . '<br><i class="far fa-clock"></i> ' . date('h:i A', strtotime($data->updated_at)) . '</small>';
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
