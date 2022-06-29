<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class User
 *
 * @property int $id
 * @property int|null $departmentId
 * @property int $roleId
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Department|null $department
 *
 * @package App\Models
 */
class User extends Authenticatable
{
    use HasRoles;

    protected $table = 'users';

    protected $casts = [
        'departmentId' => 'int',
        'roleId' => 'int'
    ];

    protected $dates = [
        'email_verified_at'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected $fillable = [
        'departmentId',
        'roleId',
        'name',
        'email',
        'email_verified_at',
        'password',
        'remember_token'
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'departmentId');
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    public function isAdmin()
    {
        foreach ($this->roles()->get() as $role) {
            if ($role->name == 'Admin') {
                return true;
            }
        }
    }
}
