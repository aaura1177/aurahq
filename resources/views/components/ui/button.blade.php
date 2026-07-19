@props(['variant' => 'primary', 'href' => null, 'type' => 'button'])
@php
$base = 'inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition';
$variants = [
  'primary' => 'bg-brand-600 text-white hover:bg-brand-700',
  'secondary' => 'bg-white text-slate-700 border border-slate-300 hover:bg-slate-50',
  'danger' => 'bg-red-600 text-white hover:bg-red-700',
];
$cls = $base . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp
@if($href)
  <a href="{{ $href }}" {{ $attributes->merge(['class' => $cls]) }}>{{ $slot }}</a>
@else
  <button type="{{ $type }}" {{ $attributes->merge(['class' => $cls]) }}>{{ $slot }}</button>
@endif
