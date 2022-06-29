<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ParcelStock
 *
 * @property int $id
 * @property int $parcelId
 * @property int $typeId
 * @property int $stock
 * @property int|null $created_userId
 * @property Carbon|null $created_at
 * @property int|null $updated_userId
 * @property Carbon|null $updated_at
 *
 * @property Parcel $parcel
 * @property ParcelStock $parcel_stock
 * @property Collection|ParcelStock[] $parcel_stocks
 *
 * @package App\Models
 */
class ParcelStock extends Model
{
	protected $table = 'parcel_stock';

	protected $casts = [
		'parcelId' => 'int',
		'typeId' => 'int',
		'stock' => 'int',
		'created_userId' => 'int',
		'updated_userId' => 'int'
	];

	protected $fillable = [
		'parcelId',
		'typeId',
		'stock',
		'created_userId',
		'updated_userId'
	];

	public function parcel()
	{
		return $this->belongsTo(Parcel::class, 'parcelId');
	}


	public function parcel_stock_type()
	{
		return $this->belongsTo(ParcelStockType::class, 'typeId');
	}

	public function parcel_stocks()
	{
		return $this->hasMany(ParcelStock::class, 'typeId');
	}
}
