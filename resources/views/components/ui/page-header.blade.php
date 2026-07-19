@props(['title', 'subtitle' => null])
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-bold text-slate-900">{{ $title }}</h1>
        @if($subtitle)<p class="text-sm text-slate-500 mt-0.5">{{ $subtitle }}</p>@endif
    </div>
    <div class="flex items-center gap-2">{{ $slot }}</div>
</div>
