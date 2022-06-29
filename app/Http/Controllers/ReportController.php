<?php

namespace App\Http\Controllers;

use App\Exports\HistoryExport;
use App\Models\Department;
use Auth;
use App\Models\Faculty;
use App\Models\HistoryStatus;
use App\Models\HistoryType;
use App\Models\Location;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $breadcrumb = [
            ['route' => route('report.index'), 'name' => 'รายงาน'],
        ];
        $department = null;
        if (Auth::user()->hasRole('Admin')) {
            $department = Faculty::with('departments')->get();
        } else {
            $departmentId = Auth::user()->departmentId;
            $department = Department::where('id', $departmentId)->get();
        }

        return view('report.main', [
            'breadcrumb' => $breadcrumb,
            'department' => $department,
            'location' => Location::all(),
            'status' => HistoryStatus::all(),
            'type' => HistoryType::all(),
        ]);
    }

    public function export($departmentId = null, $locationId = null, $statusId = null, $typeId = null, $startDate = null, $endDate = null)
    {
        $now = date_format(now(), "YmdHis");
        return Excel::download(new HistoryExport($departmentId, $locationId, $statusId, $typeId, $startDate, $endDate), 'history_data_' . $now . '.xlsx');
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
        //
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
}
