<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Parcel
 * 
 * @property int $id
 * @property int $facultyId
 * @property int $departmentId
 * @property int $locationId
 * @property string $reference
 * @property string $name
 * @property int $stock
 * @property string $stock_unit
 * @property int $status
 * @property int|null $created_userId
 * @property int|null $updated_userId
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Department $department
 * @property Faculty $faculty
 * @property Location $location
 * @property Collection|History[] $histories
 * @property Collection|ParcelImage[] $parcel_images
 * @property Collection|ParcelStock[] $parcel_stocks
 *
 * @package App\Models
 */
class Parcel extends Model
{
	protected $table = 'parcel';

	protected $casts = [
		'facultyId' => 'int',
		'departmentId' => 'int',
		'locationId' => 'int',
		'stock' => 'int',
		'status' => 'int',
		'created_userId' => 'int',
		'updated_userId' => 'int'
	];

	protected $fillable = [
		'facultyId',
		'departmentId',
		'locationId',
		'reference',
		'name',
		'stock',
		'stock_unit',
		'status',
		'created_userId',
		'updated_userId'
	];

	public function department()
	{
		return $this->belongsTo(Department::class, 'departmentId');
	}

	public function faculty()
	{
		return $this->belongsTo(Faculty::class, 'facultyId');
	}

	public function location()
	{
		return $this->belongsTo(Location::class, 'locationId');
	}

	public function histories()
	{
		return $this->hasMany(History::class, 'parcelId');
	}

	public function parcel_images()
	{
		return $this->hasMany(ParcelImage::class, 'parcelId');
	}

	public function parcel_stocks()
	{
		return $this->hasMany(ParcelStock::class, 'parcelId');
	}
}
