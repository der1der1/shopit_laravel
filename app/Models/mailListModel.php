<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class mailListModel extends Model
{
    use HasFactory;

    protected $table = 'mail_list';

    protected $fillable = [
        'name',
        'title',
        'email',
        'onoff',
        'updated_at'
    ];

}
