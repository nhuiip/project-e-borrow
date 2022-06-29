<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ParcelStockType
 *
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class ParcelStockType extends Model
{
    const Add = 1;
    const Withdraw = 2;

	protected $table = 'parcel_stock_type';

	protected $fillable = [
		'name'
	];
}
