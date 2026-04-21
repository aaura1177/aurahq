<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\ApiJson;
use Spatie\Permission\Models\Permission;

class PermissionApiController extends Controller
{
    public function index()
    {
        return ApiJson::ok(Permission::orderBy('name')->get(['id', 'name'])->values()->all());
    }
}
