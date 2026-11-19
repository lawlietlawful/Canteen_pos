<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Shared PostgreSQL table in app1 schema
     */
    protected $table = 'app1.users';

    /**
     * Primary key is student/employee ID string
     */
    protected $primaryKey = 'idno';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'idno',
        'username',
        'pwd',
        'name',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'pwd',
    ];

    /**
     * Override authentication password column to match 'pwd'
     */
    public function getAuthPassword()
    {
        return $this->pwd;
    }

    /**
     * Disable remember token since the shared table does not contain remember_token column
     */
    public function getRememberTokenName()
    {
        return '';
    }

    /**
     * An app1 user (cashier) can have many orders
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'idno', 'idno');
    }
}
