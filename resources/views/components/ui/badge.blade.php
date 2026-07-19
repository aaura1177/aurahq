@props(['color' => 'slate'])
@php
$map = [
  'slate' => 'bg-slate-100 text-slate-700',
  'brand' => 'bg-brand-100 text-brand-700',
  'success' => 'bg-green-100 text-green-700',
  'danger' => 'bg-red-100 text-red-700',
  'warning' => 'bg-amber-100 text-amber-700',
  'blue' => 'bg-blue-100 text-blue-700',
];
@endphp
<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $map[$color] ?? $map['slate'] }}">{{ $slot }}</span>
