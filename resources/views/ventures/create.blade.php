@extends('layouts.admin')
@section('title', 'New Venture')
@section('header', 'New Venture')

@section('content')
<div class="max-w-2xl mx-auto">
    <form method="post" action="{{ route('ventures.store') }}" class="bg-white rounded-xl border border-slate-200 p-6 space-y-4">
        @csrf
        @include('ventures._form')
        <div class="flex gap-2 pt-2">
            <button type="submit" class="hq-btn hq-btn-primary">Create venture</button>
            <a href="{{ route('ventures.index') }}" class="hq-btn hq-btn-ghost">Cancel</a>
        </div>
    </form>
</div>
@endsection
