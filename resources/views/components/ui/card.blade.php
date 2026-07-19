@props(['title' => null, 'padding' => 'p-5'])
<div {{ $attributes->merge(['class' => 'bg-white rounded-xl border border-slate-200 shadow-sm']) }}>
    @if($title)
        <div class="px-5 py-3 border-b border-slate-100">
            <h3 class="text-sm font-semibold text-slate-700">{{ $title }}</h3>
        </div>
    @endif
    <div class="{{ $padding }}">{{ $slot }}</div>
</div>
