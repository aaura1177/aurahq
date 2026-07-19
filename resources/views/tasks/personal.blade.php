@extends('layouts.admin')
@section('title','My Tasks')
@section('header','My Tasks')
@section('content')

<x-ui.page-header title="My Tasks" subtitle="What needs doing">
    <x-ui.button href="{{ route('tasks.create', ['context' => 'personal']) }}">+ Add Task</x-ui.button>
</x-ui.page-header>

@php
$tabs = ['open'=>'Open','starred'=>'Starred','today'=>'Today','done'=>'Done'];
@endphp
<div class="flex gap-1 mb-6 bg-slate-100 p-1 rounded-lg w-fit">
    @foreach($tabs as $key=>$label)
        <a href="{{ route('tasks.personal',['filter'=>$key]) }}"
           class="px-4 py-1.5 rounded-md text-sm font-medium transition
                  {{ $filter===$key ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
            {{ $label }} <span class="text-slate-400">{{ $counts[$key] ?? 0 }}</span>
        </a>
    @endforeach
</div>

<x-ui.card padding="p-0">
    <div class="divide-y divide-slate-100">
        @forelse($tasks as $task)
        <div class="flex items-center gap-3 px-5 py-3 hover:bg-slate-50 group">
            {{-- Complete checkbox --}}
            <form action="{{ route('tasks.toggle',$task->id) }}" method="POST">
                @csrf @method('PATCH')
                <button class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition
                    {{ $task->isDone() ? 'bg-green-500 border-green-500 text-white' : 'border-slate-300 hover:border-green-500' }}">
                    @if($task->isDone())<i class="fas fa-check text-[10px]"></i>@endif
                </button>
            </form>
            {{-- Title + meta --}}
            <div class="flex-1 min-w-0">
                <a href="{{ route('tasks.show',$task->id) }}" class="text-sm font-medium text-slate-800 {{ $task->isDone() ? 'line-through text-slate-400' : '' }} hover:text-brand-600">
                    {{ $task->title }}
                </a>
                <div class="flex items-center gap-2 mt-0.5">
                    @if($task->priority !== 'normal')
                        <x-ui.badge :color="in_array($task->priority,['critical','urgent'])?'danger':'warning'">{{ $task->priority }}</x-ui.badge>
                    @endif
                    @if($task->recurrence !== 'none')<x-ui.badge color="blue">{{ $task->recurrence }}</x-ui.badge>@endif
                    @if($task->due_date)<span class="text-xs text-slate-400">{{ $task->due_date->format('d M') }}</span>@endif
                </div>
            </div>
            {{-- Star --}}
            <form action="{{ route('tasks.star',$task->id) }}" method="POST">
                @csrf @method('PATCH')
                <button class="text-lg {{ $task->is_starred ? 'text-amber-400' : 'text-slate-300 hover:text-amber-400' }}">
                    <i class="{{ $task->is_starred ? 'fas' : 'far' }} fa-star"></i>
                </button>
            </form>
        </div>
        @empty
        <x-ui.empty-state icon="fa-check-circle" title="Nothing here" message="You're all caught up.">
            <x-ui.button href="{{ route('tasks.create',['context'=>'personal']) }}">+ Add a task</x-ui.button>
        </x-ui.empty-state>
        @endforelse
    </div>
</x-ui.card>
@endsection
