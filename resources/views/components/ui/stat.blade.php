@props(['label', 'value', 'trend' => null, 'color' => 'brand', 'icon' => null])
<div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
    <div class="flex items-center justify-between">
        <span class="text-xs font-medium text-slate-500 uppercase tracking-wide">{{ $label }}</span>
        @if($icon)<i class="fas {{ $icon }} text-{{ $color }}-500"></i>@endif
    </div>
    <div class="mt-2 text-2xl font-bold text-slate-900">{{ $value }}</div>
    @if($trend)<div class="mt-1 text-xs text-slate-500">{{ $trend }}</div>@endif
</div>
