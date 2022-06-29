<?php

namespace App\Exports;

use App\Models\History;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class HistoryExport implements FromView, ShouldAutoSize
{

    private $departmentId;
    private $locationId;
    private $statusId;
    private $typeId;
    private $startDate;
    private $endDate;
    public function __construct(int $departmentId = null, int $locationId = null, int $statusId = null, int $typeId = null, string $startDate = null, string $endDate = null)
    {
        $this->departmentId = $departmentId;
        $this->locationId = $locationId;
        $this->statusId = $statusId;
        $this->typeId = $typeId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function view(): View
    {
        $data =  History::with('parcel.department', 'history_status', 'history_type')
            ->when($this->departmentId, function ($query, $departmentId) {
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
            ->when($this->locationId, function ($query, $locationId) {
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
            ->when($this->statusId, function ($query, $statusId) {
                return $query->where(function ($query) use ($statusId) {
                    if (!empty($statusId) && $statusId != 0) {
                        $query->where('statusId', $statusId);
                    }
                });
            })
            ->when($this->typeId, function ($query, $typeId) {
                return $query->where(function ($query) use ($typeId) {
                    if (!empty($typeId) && $typeId != 0) {
                        $query->where('typeId', $typeId);
                    }
                });
            })
            ->when($this->startDate, function ($query, $startDate) {
                return $query->where(function ($query) use ($startDate) {
                    if (!empty($startDate) && ($startDate != null || $startDate != 'null')) {
                        $query->where('created_at', '>=', $startDate);
                    }
                });
            })
            ->when($this->endDate, function ($query, $endDate) {
                return $query->where(function ($query) use ($endDate) {
                    if (!empty($endDate) && ($endDate != null || $endDate != 'null')) {
                        $query->where('created_at', '<=', $endDate);
                    }
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('report._export', [
            'data' => $data,
        ]);
    }
}
