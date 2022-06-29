<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DurableGoodsStatus
 *
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Collection|DurableGood[] $durable_goods
 *
 * @package App\Models
 */
class DurableGoodsStatus extends Model
{
    const Inactive = 1;
    const Defective = 2;
    const Active = 3;
    const Pending_Approval = 4;
    const Waiting_Return = 5;

    public function statuslabel($status)
    {
        $status_label = "";

        switch ($status) {
            case static::Inactive:
                $status_label = "ปิดไม่ให้เบิก";
                break;
            case static::Defective:
                $status_label = "ชำรุด";
                break;
            case static::Active:
                $status_label = "เบิกได้";
                break;
            case static::Pending_Approval:
                $status_label = "รออนุมัติ";
                break;
            case static::Waiting_Return:
                $status_label = "รอคืน";
                break;
            default:
                $status_label = "";
                break;
        }

        return $status_label;
    }

	protected $table = 'durable_goods_status';

	protected $fillable = [
		'name'
	];

	public function durable_goods()
	{
		return $this->hasMany(DurableGood::class, 'statusId');
	}
}
