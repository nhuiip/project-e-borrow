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
}
