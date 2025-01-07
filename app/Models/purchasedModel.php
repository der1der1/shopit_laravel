<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;


class purchasedModel extends Model
{
    // use HasFactory;
    use Notifiable;


    protected $table = 'purchased';

    protected $fillable = [
        'account',
        'purchased',
        'bill',
        'payed',
        'delivered',
        'recieved',
        'show',
    ];
}
