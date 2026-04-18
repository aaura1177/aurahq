<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceLog extends Model
{
    protected $fillable = [
        'ip_address',
        'browser',
        'browser_version',
        'platform',
        'platform_version',
        'device',
        'is_desktop',
        'is_mobile',
        'user_agent',
        'referer',
    ];
}

