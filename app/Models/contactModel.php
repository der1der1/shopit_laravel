<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class contactModel extends Authenticatable
{
    use Notifiable;

    protected $table = 'contact';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'information',
    ];
}
