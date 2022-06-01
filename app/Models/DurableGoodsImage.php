<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DurableGoodsImage
 * 
 * @property int $id
 * @property int $durablegoodsId
 * @property string $name
 * @property int|null $created_userId
 * @property Carbon|null $created_at
 * 
 * @property DurableGood $durable_good
 *
 * @package App\Models
 */
class DurableGoodsImage extends Model
{
	protected $table = 'durable_goods_image';
	public $timestamps = false;

	protected $casts = [
		'durablegoodsId' => 'int',
		'created_userId' => 'int'
	];

	protected $fillable = [
		'durablegoodsId',
		'name',
		'created_userId'
	];

	public function durable_good()
	{
		return $this->belongsTo(DurableGood::class, 'durablegoodsId');
	}
}
