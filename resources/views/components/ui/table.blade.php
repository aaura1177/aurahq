@props(['headers' => []])
<div class="overflow-x-auto bg-white rounded-xl border border-slate-200 shadow-sm">
    <table class="min-w-full divide-y divide-slate-200">
        @if(count($headers))
        <thead class="bg-slate-50">
            <tr>
                @foreach($headers as $h)
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ $h }}</th>
                @endforeach
            </tr>
        </thead>
        @endif
        <tbody class="divide-y divide-slate-100">{{ $slot }}</tbody>
    </table>
</div>
