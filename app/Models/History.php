<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class History
 * 
 * @property int $id
 * @property int|null $durablegoodsId
 * @property int|null $parcelId
 * @property int $typeId
 * @property int $statusId
 * @property int $unit
 * @property int|null $approved_userId
 * @property Carbon|null $approved_at
 * @property int|null $returned_userId
 * @property Carbon|null $returned_at
 * @property int|null $created_userId
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property DurableGood|null $durable_good
 * @property Parcel|null $parcel
 * @property HistoryStatus $history_status
 * @property HistoryType $history_type
 *
 * @package App\Models
 */
class History extends Model
{
	protected $table = 'history';

	protected $casts = [
		'durablegoodsId' => 'int',
		'parcelId' => 'int',
		'typeId' => 'int',
		'statusId' => 'int',
		'unit' => 'int',
		'approved_userId' => 'int',
		'returned_userId' => 'int',
		'created_userId' => 'int'
	];

	protected $dates = [
		'approved_at',
		'returned_at'
	];

	protected $fillable = [
		'durablegoodsId',
		'parcelId',
		'typeId',
		'statusId',
		'unit',
		'approved_userId',
		'approved_at',
		'returned_userId',
		'returned_at',
		'created_userId'
	];

	public function durable_good()
	{
		return $this->belongsTo(DurableGood::class, 'durablegoodsId');
	}

	public function parcel()
	{
		return $this->belongsTo(Parcel::class, 'parcelId');
	}

	public function history_status()
	{
		return $this->belongsTo(HistoryStatus::class, 'statusId');
	}

	public function history_type()
	{
		return $this->belongsTo(HistoryType::class, 'typeId');
	}
}
