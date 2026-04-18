<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

class PermissionApiController extends Controller
{
    public function index()
    {
        return response()->json(['data' => Permission::orderBy('name')->get(['id', 'name'])]);
    }
}
