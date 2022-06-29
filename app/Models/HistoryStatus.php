<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class HistoryStatus
 *
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Collection|History[] $histories
 *
 * @package App\Models
 */
class HistoryStatus extends Model
{
    const Status_Pending_Approval = 1;
    const Status_Approval = 2;
    const Status_Returned = 3;
    public function statuslabel($status)
    {
        $label = "";

        switch ($status) {
            case static::Status_Pending_Approval:
                $label = "รออนุมัติ";
                break;
            case static::Status_Approval:
                $label = "อนุมัติแล้ว";
                break;
            case static::Status_Returned:
                $label = "คืนแล้ว";
                break;
            default:
                $label = "";
                break;
        }

        return $label;
    }

	protected $table = 'history_status';

	protected $fillable = [
		'name'
	];

	public function histories()
	{
		return $this->hasMany(History::class, 'statusId');
	}
}
