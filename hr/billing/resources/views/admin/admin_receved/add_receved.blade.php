@extends('admin.layout.master')

@section('content')

<style>
      #add_payment {
        max-width: 1200px;
        margin: 30px auto;
        padding: 30px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        font-family: 'Segoe UI', sans-serif;
    }

    #add_payment h2 {
        text-align: center;
        margin-bottom: 25px;
        color: #2c3e50;
    }

    .form-input {
        margin-bottom: 20px;
    }

    .form-input label {
        display: block;
        font-weight: 500;
        margin-bottom: 6px;
        color: #2c3e50;
    }

    .form-input input[type="text"],
    .form-input input[type="number"],
    .form-input input[type="date"],
    .form-input input[type="file"],
    .form-input select {
        width: 90%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 8px;
        background-color: #f9f9f9;
        font-size: 14px;
    }

    .form-input input:focus,
    .form-input select:focus {
        outline: none;
        border-color: #3498db;
        background-color: #fff;
    }

    .form-input p {
        margin: 0;
        padding: 4px 0;
        font-weight: 500;
    }

    button[type="submit"] {
        background: #3498db;
        color: white;
        padding: 12px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
        width: 25%;
        transition: background 0.3s ease;
        margin-top: 10px;
    }

    button[type="submit"]:hover {
        background: #2980b9;
    }

    div[style*="color:green"] {
        margin-bottom: 15px;
        font-weight: bold;
    }

    h4 {
        margin-top: 25px;
        margin-bottom: 10px;
        color: #2d3436;
    }

    ul {
        padding-left: 18px;
        margin: 0;
    }

    ul li {
        padding: 6px 0;
        font-size: 14px;
    }

</style>

<section id="add_payment">
    <h2>Add Payment</h2>

    <div id="form_container">
        <!-- Success and error messages dummy -->
        <div style="color:green; display:none;">Payment added successfully!</div>
        <div style="color: red; margin-bottom: 10px; display:none;">
            <ul style="margin: 0; padding-left: 20px;">
                <li>Error message 1</li>
                <li>Error message 2</li>
            </ul>
        </div>


        <form action="{{ route('admin.payament.store', ['user_id' => $user_id, 'project_id' => $project_id]) }}" method="POST" enctype="multipart/form-data">
    @csrf

    <!-- Hidden Fields -->
    <input type="hidden" name="user_id" value="{{ $user_id }}">


    <div class="form-input">
        <label>Project:</label>
        <input type="text" value="{{ $projectTitle }}" disabled>
        <input type="hidden" name="project_id" value="{{ $project_id }}">
    </div>

    <div class="form-input" style="background: #f0f8ff; padding: 15px; border-radius: 8px;">
<p><strong>Total Working Time:</strong> {{ $formattedTime  ?? '00:00:00' }}</p>

<p><strong>Pending Amount:</strong> ₹{{ $pendingAmount ?? '0.00' }}</p>
<p><strong>Total Amount:</strong> ₹{{ $totalAmount ?? '0.00' }}</p>
<p><strong>Paid Amount:</strong> ₹{{ $paidAmount?? '0.00' }}</p>
    </div>

    <div class="form-input">
        <label for="paid_amount">Paid Amount:</label>
        <input type="number" id="paid_amount" name="paid_amount" step="0.01" required>
    </div>

    <div class="form-input">
        <label for="payment_date">Payment Date:</label>
        <input type="date" id="payment_date" name="payment_date" value="{{ date('Y-m-d') }}" required>
    </div>

    <button type="submit">Add Payment</button>
</form>

    </div>
</section>


@endsection
