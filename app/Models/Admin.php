<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $table = 'admins';

    protected $fillable = [
        'nama_admin',
        'username',
        'password',
        'email',
        'role_admin'
    ];
}