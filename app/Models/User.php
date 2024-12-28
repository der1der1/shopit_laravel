<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;


class User extends Authenticatable
{
    use Notifiable;
    protected $table = 'users';


    protected $fillable = [
        'name',
        'want',
        'account',
        'password',
        'prvilige',
        'to_shop',
        'to_address',
        'bank_account',
        'shop1_addr2',
        'info0',
        'info1',
        'info2',
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];


}
