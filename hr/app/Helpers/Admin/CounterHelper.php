<?php

namespace App\Helpers\Admin;

use App\Models\Counter;

class CounterHelper
{
  public static function generateCode($counterName)
  {
    $cod = Counter::where('counter_name', $counterName)->first();
    if (!$cod) {
      return null;
    }
    $separator = str_contains($counterName, 'emp_id') ? '/' : '-';

    $code = $cod->prefix . $separator . str_pad($cod->count, 4, '0', STR_PAD_LEFT);
    $cod->increment('count');

    return $code;
  }
}
