<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RedirectLog extends Model
{
    public $fillable = [
        "redirect_id",
        "request_ip",
        "request_user_agent",
        "request_header",
        "request_query_params",
        "date_time_acess",
        "created_at",
        "updated_at"
    ];
}
