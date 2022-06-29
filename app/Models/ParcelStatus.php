<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ParcelStatus
 *
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Collection|Parcel[] $parcels
 *
 * @package App\Models
 */
class ParcelStatus extends Model
{
    const Inactive = 1;
    const Out_Of_Stock = 2;
    const Active = 3;

    public function statuslabel($status)
    {
        $status_label = "";

        switch ($status) {
            case static::Inactive:
                $status_label = "ปิดไม่ให้เบิก";
                break;
            case static::Out_Of_Stock:
                $status_label = "ของหมด";
                break;
            case static::Active:
                $status_label = "เบิกได้";
                break;
            default:
                $status_label = "";
                break;
        }

        return $status_label;
    }

	protected $table = 'parcel_status';

	protected $fillable = [
		'name'
	];

	public function parcels()
	{
		return $this->hasMany(Parcel::class, 'statusId');
	}
}
