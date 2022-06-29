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
 * @property int $statusId
 * @property int $departmentId
 * @property int $locationId
 * @property string $reference
 * @property string $name
 * @property int|null $created_userId
 * @property Carbon|null $created_at
 * @property int|null $updated_userId
 * @property Carbon|null $updated_at
 * 
 * @property Department $department
 * @property Location $location
 * @property DurableGoodsStatus $durable_goods_status
 * @property Collection|DurableGoodsImage[] $durable_goods_images
 * @property Collection|History[] $histories
 *
 * @package App\Models
 */
class DurableGood extends Model
{
	protected $table = 'durable_goods';

	protected $casts = [
		'statusId' => 'int',
		'departmentId' => 'int',
		'locationId' => 'int',
		'created_userId' => 'int',
		'updated_userId' => 'int'
	];

	protected $fillable = [
		'statusId',
		'departmentId',
		'locationId',
		'reference',
		'name',
		'created_userId',
		'updated_userId'
	];

	public function department()
	{
		return $this->belongsTo(Department::class, 'departmentId');
	}

	public function location()
	{
		return $this->belongsTo(Location::class, 'locationId');
	}

	public function durable_goods_status()
	{
		return $this->belongsTo(DurableGoodsStatus::class, 'statusId');
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
