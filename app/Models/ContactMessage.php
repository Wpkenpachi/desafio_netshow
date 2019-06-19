<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = [
        'name',
        'ip',
        'message',
        'email',
        'phone',
        'attached_file'
    ];
}
