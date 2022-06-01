<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Faculty
 * 
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|Department[] $departments
 * @property Collection|DurableGood[] $durable_goods
 * @property Collection|Location[] $locations
 * @property Collection|Parcel[] $parcels
 *
 * @package App\Models
 */
class Faculty extends Model
{
	protected $table = 'faculty';

	protected $fillable = [
		'name'
	];

	public function departments()
	{
		return $this->hasMany(Department::class, 'facultyId');
	}

	public function durable_goods()
	{
		return $this->hasMany(DurableGood::class, 'facultyId');
	}

	public function locations()
	{
		return $this->hasMany(Location::class, 'facultyId');
	}

	public function parcels()
	{
		return $this->hasMany(Parcel::class, 'facultyId');
	}
}
