<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class User
 *
 * @property int $id
 * @property int $roleId
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */


class User extends Authenticatable
{
    use HasRoles;

    protected $table = 'users';

    protected $casts = [
        'facultyId' => 'int',
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
        'facultyId',
        'departmentId',
        'roleId',
        'name',
        'email',
        'email_verified_at',
        'password',
        'remember_token'
    ];

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

    public function department()
    {
        return $this->belongsTo(Department::class, 'departmentId');
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'facultyId');
    }
}
