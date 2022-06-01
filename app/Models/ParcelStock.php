<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ParcelStock
 * 
 * @property int $id
 * @property int $parcelId
 * @property int $stock
 * @property int $stock_type
 * @property int|null $created_userId
 * @property Carbon|null $created_at
 * @property int|null $updated_userId
 * @property Carbon|null $updated_at
 * 
 * @property Parcel $parcel
 *
 * @package App\Models
 */
class ParcelStock extends Model
{
	protected $table = 'parcel_stock';

	protected $casts = [
		'parcelId' => 'int',
		'stock' => 'int',
		'stock_type' => 'int',
		'created_userId' => 'int',
		'updated_userId' => 'int'
	];

	protected $fillable = [
		'parcelId',
		'stock',
		'stock_type',
		'created_userId',
		'updated_userId'
	];

	public function parcel()
	{
		return $this->belongsTo(Parcel::class, 'parcelId');
	}
}
