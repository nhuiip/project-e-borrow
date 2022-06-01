<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DurableGood
 * 
 * @property int $id
 * @property int $facultyId
 * @property int $departmentId
 * @property int $locationId
 * @property string $reference
 * @property string $name
 * @property int $status
 * @property int|null $created_userId
 * @property Carbon|null $created_at
 * @property int|null $updated_userId
 * @property Carbon|null $updated_at
 * 
 * @property Department $department
 * @property Faculty $faculty
 * @property Location $location
 * @property Collection|DurableGoodsImage[] $durable_goods_images
 * @property Collection|History[] $histories
 *
 * @package App\Models
 */
class DurableGood extends Model
{
	protected $table = 'durable_goods';

	protected $casts = [
		'facultyId' => 'int',
		'departmentId' => 'int',
		'locationId' => 'int',
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

	public function durable_goods_images()
	{
		return $this->hasMany(DurableGoodsImage::class, 'durablegoodsId');
	}

	public function histories()
	{
		return $this->hasMany(History::class, 'durablegoodsId');
	}
}
