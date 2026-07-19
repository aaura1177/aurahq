@props(['icon' => 'fa-inbox', 'title' => 'Nothing here yet', 'message' => null])
<div class="text-center py-12">
    <i class="fas {{ $icon }} text-3xl text-slate-300"></i>
    <h3 class="mt-3 text-sm font-semibold text-slate-700">{{ $title }}</h3>
    @if($message)<p class="mt-1 text-sm text-slate-500">{{ $message }}</p>@endif
    <div class="mt-4">{{ $slot }}</div>
</div>
