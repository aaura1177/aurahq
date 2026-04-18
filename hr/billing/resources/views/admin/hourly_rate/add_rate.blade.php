@extends('admin.layout.master')

@section('content')


<style>
    #add_payment {
        max-width: 600px;
        margin: 30px auto;
        padding: 25px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        font-family: 'Segoe UI', sans-serif;
    }

    #add_payment h2 {
        text-align: center;
        margin-bottom: 20px;
        color: #333;
    }

    #form_container {
        display: flex;
        flex-direction: column;
    }

    .form-input {
        margin-bottom: 20px;
    }

    .form-input label {
        display: block;
        font-weight: 600;
        margin-bottom: 6px;
        color: #444;
    }

    .form-input input,
    .form-input select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 14px;
        background: #f9f9f9;
    }

    .form-input input:focus,
    .form-input select:focus {
        outline: none;
        border-color: #3498db;
        background-color: #fff;
    }

    .form-input button {
        background: #3498db;
        color: white;
        padding: 12px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
        width: 100%;
        transition: background 0.3s ease;
    }

    .form-input button:hover {
        background: #2980b9;
    }

    div[style*="color:green"] {
        margin-bottom: 15px;
        font-weight: 600;
    }
</style>

<section id="add_payment">
    <h2>Add Hourly Rate</h2>
    <div id="form_container">

        @if(session('success'))
            <div style="color:green">{{ session('success') }}</div>
        @endif

        <form action="{{ route('admin.rate.store') }}" method="POST">
            @csrf
            <div class="form-input">
                <label for="user_id">Select User:</label>
                <select name="user_id" id="user_id" required>
                    <option value="">-- Select User --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select><br><br>
            </div>
            <div class="form-input">
                <label for="project_id">Select Project:</label>
                <select name="project_id" id="project_id" required>
                    <option value="">-- Select Project --</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                            {{ $project->title }}
                        </option>
                    @endforeach
                </select><br><br>
            </div>


           <div class="form-input">
    <label for="h_rate">Hourly Rate (₹):</label>
    <input type="number" name="h_rate" min="0" step="0.01" value="{{ old('h_rate') }}" required><br><br>
</div>

            <div class="form-input">
                <label for="date">Date:</label>
                <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required><br><br>
            </div>

            <div class="form-input">
                <button type="submit">Save Rate</button>
            </div>

        </form>

    </div>
</section>

@endsection
