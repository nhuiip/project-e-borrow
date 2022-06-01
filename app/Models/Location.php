<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Location
 * 
 * @property int $id
 * @property int|null $facultyId
 * @property int|null $departmentId
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Department|null $department
 * @property Faculty|null $faculty
 * @property Collection|DurableGood[] $durable_goods
 * @property Collection|Parcel[] $parcels
 *
 * @package App\Models
 */
class Location extends Model
{
	protected $table = 'location';

	protected $casts = [
		'facultyId' => 'int',
		'departmentId' => 'int'
	];

	protected $fillable = [
		'facultyId',
		'departmentId',
		'name'
	];

	public function department()
	{
		return $this->belongsTo(Department::class, 'departmentId');
	}

	public function faculty()
	{
		return $this->belongsTo(Faculty::class, 'facultyId');
	}

	public function durable_goods()
	{
		return $this->hasMany(DurableGood::class, 'locationId');
	}

	public function parcels()
	{
		return $this->hasMany(Parcel::class, 'locationId');
	}
}
