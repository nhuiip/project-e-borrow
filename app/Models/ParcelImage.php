<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ParcelImage
 * 
 * @property int $id
 * @property int $parcelId
 * @property string $name
 * @property int|null $created_userId
 * @property Carbon|null $created_at
 * 
 * @property Parcel $parcel
 *
 * @package App\Models
 */
class ParcelImage extends Model
{
	protected $table = 'parcel_image';
	public $timestamps = false;

	protected $casts = [
		'parcelId' => 'int',
		'created_userId' => 'int'
	];

	protected $fillable = [
		'parcelId',
		'name',
		'created_userId'
	];

	public function parcel()
	{
		return $this->belongsTo(Parcel::class, 'parcelId');
	}
}
