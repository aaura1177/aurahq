@extends('layouts.admin')
@section('title', 'Edit Venture')
@section('header', 'Edit Venture')

@section('content')
<div class="max-w-2xl mx-auto">
    <form method="post" action="{{ route('ventures.update', $venture) }}" class="bg-white rounded-xl border border-slate-200 p-6 space-y-4">
        @csrf
        @method('PUT')
        @include('ventures._form')
        <div class="flex gap-2 pt-2">
            <button type="submit" class="hq-btn hq-btn-primary">Save changes</button>
            <a href="{{ route('ventures.show', $venture) }}" class="hq-btn hq-btn-ghost">Cancel</a>
        </div>
    </form>
</div>
@endsection
