@extends('layouts.admin')
@section('header', 'Create / Assign Task')
@section('content')
<div class="max-w-2xl mx-auto bg-white p-6 rounded-xl shadow-sm border border-slate-100">
    <form action="{{ route('tasks.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">Task Category</label>
            <select name="category" class="w-full border rounded-lg p-2.5 bg-white focus:ring-2 focus:ring-blue-500 outline-none" id="catSelect" onchange="toggleAssignee()">
                <option value="employee_assignment">Assign to Employee</option>
                <option value="admin_personal" selected>My Personal Task</option>
            </select>
        </div>

        <!-- Personal Task Type Selection -->
        <div id="freqField">
            <label class="block text-sm font-bold text-slate-700 mb-1">Task Type</label>
            <select name="frequency" class="w-full border rounded-lg p-2.5 bg-white">
                <option value="daily" {{ isset($defaultFreq) && $defaultFreq == 'daily' ? 'selected' : '' }}>Daily Routine (Unlimited)</option>
                <option value="weekly" {{ isset($defaultFreq) && $defaultFreq == 'weekly' ? 'selected' : '' }}>Weekly Routine (Unlimited)</option>
                <option value="top_five" {{ isset($defaultFreq) && $defaultFreq == 'top_five' ? 'selected' : '' }}>Top 5 Goal (Max 5 Active)</option>
            </select>
            <p class="text-xs text-slate-400 mt-1">"Top 5" tasks are strictly limited. If full, new ones are saved as inactive.</p>
        </div>

        <div id="assigneeField" style="display:none;">
            <label class="block text-sm font-bold text-slate-700 mb-1">Assign To</label>
            <select name="assigned_to" class="w-full border rounded-lg p-2.5 bg-white">
                <option value="">Select Employee...</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">Task Title</label>
            <input type="text" name="title" class="w-full border rounded-lg p-2.5" placeholder="e.g. Update Website Content" required>
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">Description</label>
            <textarea name="description" class="w-full border rounded-lg p-2.5" rows="3" placeholder="Details..."></textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Priority</label>
                <select name="priority" class="w-full border rounded-lg p-2.5 bg-white">
                    <option value="normal" {{ $defaultPriority == 'normal' ? 'selected' : '' }}>Normal</option>
                    <option value="urgent" {{ $defaultPriority == 'urgent' ? 'selected' : '' }}>Urgent</option>
                    <option value="critical">Critical</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Due Date</label>
                <input type="date" name="due_date" class="w-full border rounded-lg p-2.5" value="{{ $defaultDate }}">
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">Attachment</label>
            <input type="file" name="media" class="w-full border rounded-lg p-2.5 text-sm">
        </div>

        <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700 transition">Create Task</button>
    </form>
</div>

<script>
    function toggleAssignee() {
        const cat = document.getElementById('catSelect').value;
        const assigneeField = document.getElementById('assigneeField');
        const freqField = document.getElementById('freqField');

        if (cat === 'admin_personal') {
            assigneeField.style.display = 'none';
            freqField.style.display = 'block';
        } else {
            assigneeField.style.display = 'block';
            freqField.style.display = 'none';
        }
    }
    // Initialize state
    toggleAssignee();
</script>
@endsection
