<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Redirect extends Model
{

    use SoftDeletes;

    public $fillable = [
        "code",
        "status",
        "url",
        "last_acess",
        "created_at",
        "updated_at"
    ];
}
