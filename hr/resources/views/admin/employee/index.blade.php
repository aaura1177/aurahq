@extends('admin.layout.link')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Employee List</h3>
        <a href="{{ route('admin.employee.add') }}" class="btn btn-primary">+ Add Employee</a>
    </div>

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>SR</th>
                <th>Employee Image</th>
                <th>Employee ID</th>
                <th>Employee Name</th>
                <th>Employee Email</th>
                <th>Mobile</th>
                <th>Monthly Leave</th>
                <th>Status</th>
                @if (Auth::user()->id == 1)
                <th>Actions</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach ($employee as $key => $employee)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>
                    <img src="{{ asset('storage/' . $employee->image) }}" alt="Employee Image" class="img-fluid rounded-circle" width="100">
                </td>


              <td onclick="copyToClipboard(this)" style="cursor: pointer; position: relative;" title="Click to copy">
    {{ $employee->emp_id ?? 'N/A' }}
</td>

<td onclick="copyToClipboard(this)" style="cursor: pointer; position: relative;" title="Click to copy">
    {{ $employee->name ?? 'N/A' }}
</td>

<td onclick="copyToClipboard(this)" style="cursor: pointer; position: relative;" title="Click to copy">
    {{ $employee->email ?? 'N/A' }}
</td>

<td onclick="copyToClipboard(this)" style="cursor: pointer; position: relative;" title="Click to copy">
    {{ $employee->mobile ?? 'N/A' }}
</td>

                <td>{{ $employee->monthly_leave ?? 'N/A' }}</td>
                @php
                $statusColors = [
                1 => 'bg-success text-white',
                0 => 'bg-danger text-white',
                ];
                @endphp
                <td>
                    <span class="badge rounded-pill {{ $statusColors[$employee->status] ?? 'bg-secondary text-white' }}">
                        {{ $employee->status == 1 ? 'Active' : 'Inactive' }}
                    </span>
                </td>


                <td>
                    @if (Auth::user()->id == 1)

                    <a href="{{ route('admin.employee.edit', ['id' => $employee->id]) }}" class="btn btn-sm btn-warning">Edit</a>
                    @endif

                    {{-- <form action="#" method="POST"
                        style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </form> --}}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<script>
    function copyToClipboard(element) {
        const text = element.innerText.trim();
        if (text && text !== 'N/A') {
            navigator.clipboard.writeText(text).then(function () {
                // Create the popup
                const popup = document.createElement("span");
                popup.innerText = "Copied!";
                popup.style.position = "absolute";
                popup.style.top = "50%";
                popup.style.left = "50%";
                popup.style.transform = "translate(-50%, -50%)";
                popup.style.background = "#28a745";
                popup.style.color = "#fff";
                popup.style.padding = "2px 6px";
                popup.style.fontSize = "12px";
                popup.style.borderRadius = "4px";
                popup.style.zIndex = "1000";
                popup.style.pointerEvents = "none";
                popup.style.opacity = "0.9";

                element.appendChild(popup);

                // Remove after 1 second
                setTimeout(() => {
                    popup.remove();
                }, 1000);
            }).catch(function (err) {
                console.error('Failed to copy: ', err);
            });
        }
    }
</script>


@endsection