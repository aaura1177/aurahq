@php $v = $venture ?? null; @endphp
<div>
    <label class="block text-sm font-medium text-slate-700 mb-1">Name</label>
    <input type="text" name="name" required value="{{ old('name', $v->name ?? '') }}"
           class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
    @error('name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
</div>
<div>
    <label class="block text-sm font-medium text-slate-700 mb-1">Slug <span class="text-slate-400 font-normal">(optional)</span></label>
    <input type="text" name="slug" value="{{ old('slug', $v->slug ?? '') }}"
           class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm" placeholder="auto from name">
    @error('slug')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
</div>
<div>
    <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
    <textarea name="description" rows="3" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">{{ old('description', $v->description ?? '') }}</textarea>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
        <select name="status" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm bg-white">
            @foreach(\App\Models\Venture::STATUSES as $st)
                <option value="{{ $st }}" {{ old('status', $v->status ?? 'active') === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Partner name</label>
        <input type="text" name="partner_name" value="{{ old('partner_name', $v->partner_name ?? '') }}"
               class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
    </div>
</div>
<label class="inline-flex items-center gap-2 text-sm text-slate-700">
    <input type="checkbox" name="partner_funded" value="1" class="rounded border-slate-300"
           {{ old('partner_funded', $v->partner_funded ?? false) ? 'checked' : '' }}>
    Partner-funded
</label>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Color</label>
        <input type="color" name="color" value="{{ old('color', $v->color ?? '#6C63FF') }}"
               class="h-10 w-full border border-slate-200 rounded-lg px-1 py-1 bg-white">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Icon (Font Awesome class)</label>
        <input type="text" name="icon" value="{{ old('icon', $v->icon ?? 'fa-rocket') }}"
               class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm" placeholder="fa-rocket">
    </div>
</div>
