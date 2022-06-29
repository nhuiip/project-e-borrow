<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class HistoryType
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
class HistoryType extends Model
{
    const Type_DurableGoods = 1;
    const Type_Parcel = 2;
    public function typelabel($status)
    {
        $label = "";

        switch ($status) {
            case static::Type_DurableGoods:
                $label = "ครุภัณฑ์";
                break;
            case static::Type_Parcel:
                $label = "พัสดุ";
                break;
            default:
                $label = "";
                break;
        }

        return $label;
    }

	protected $table = 'history_type';

	protected $fillable = [
		'name'
	];

	public function histories()
	{
		return $this->hasMany(History::class, 'typeId');
	}
}
