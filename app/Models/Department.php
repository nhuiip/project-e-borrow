<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Department
 * 
 * @property int $id
 * @property int $facultyId
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Faculty $faculty
 * @property Collection|DurableGood[] $durable_goods
 * @property Collection|Location[] $locations
 * @property Collection|Parcel[] $parcels
 *
 * @package App\Models
 */
class Department extends Model
{
	protected $table = 'department';

	protected $casts = [
		'facultyId' => 'int'
	];

	protected $fillable = [
		'facultyId',
		'name'
	];

	public function faculty()
	{
		return $this->belongsTo(Faculty::class, 'facultyId');
	}

	public function durable_goods()
	{
		return $this->hasMany(DurableGood::class, 'departmentId');
	}

	public function locations()
	{
		return $this->hasMany(Location::class, 'departmentId');
	}

	public function parcels()
	{
		return $this->hasMany(Parcel::class, 'departmentId');
	}
}
