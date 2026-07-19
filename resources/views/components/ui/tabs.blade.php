@props(['tabs' => []])
{{-- $tabs = ['Label' => route(...), ...]; active detected by current URL --}}
<div class="border-b border-slate-200 mb-6">
    <nav class="flex gap-1 -mb-px">
        @foreach($tabs as $label => $url)
            @php $active = request()->url() === $url; @endphp
            <a href="{{ $url }}"
               class="px-4 py-2 text-sm font-medium border-b-2 transition
                      {{ $active ? 'border-brand-600 text-brand-700' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                {{ $label }}
            </a>
        @endforeach
    </nav>
</div>
