@extends('admin.layout.link')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Counters List</h3>
            <a href="{{ route('admin.counter.add') }}" class="btn btn-primary">+ Add Counter</a>
        </div>

        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>SR</th>
                    <th>Counter Name</th>
                    <th>Prefix</th>
                    <th>count</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($counters as $key => $counter)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $counter->counter_name ?? 'N/A' }}</td>
                        <td>{{ $counter->prefix ?? 'N/A' }}</td>
                        <td>{{ $counter->count ?? 'N/A' }}</td>



                        @php
                            $statusColors = [
                                '1' => 'bg-success text-white',
                                '0' => 'bg-danger text-white',
                            ];
                        @endphp

                        <td>
                            <span
                                class="badge rounded-pill {{ $statusColors[$counter->status] ?? 'bg-secondary text-white' }}">
                                {{ $counter->status == '1' ? 'Active' : ($counter->status == '0' ? 'Inactive' : 'Unknown') }}
                            </span>
                        </td>
                        <td> <button class="btn btn-sm btn-warning editCounterBtn" data-bs-toggle="modal"
                                data-bs-target="#editCounterModal" data-id="{{ $counter->id }}"
                                data-name="{{ $counter->counter_name }}" data-prefix="{{ $counter->prefix }}"
                                data-status="{{ $counter->status }}">
                                Edit
                            </button>


                            {{-- <form action="{{ route('admin.counter.destroy', $counter->id) }}" method="POST"
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


    <!-- Edit Task Modal -->
    <div class="modal fade" id="editCounterModal" tabindex="-1" aria-labelledby="editCounterModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCounterModalLabel">Edit Counter</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editTaskForm" action="{{ route('admin.counter.edit') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <input type="hidden" name="id" id="edit_counter_id">
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Counter Name</label>
                                <input type="text" name="counter_name" id="edit_counter_name" class="form-control"
                                    required>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Prefix</label>
                                <input type="text" name="prefix" id="edit_prefix" class="form-control" required>
                            </div>



                            <div class="mb-3 col-md-4">
                                <label class="form-label">Status</label>
                                <select name="status" id="edit_status" class="form-control">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>


                        </div>

                        <div class="text-end">
                            <button type="submit" id="UpdateTaskBtn" class="btn btn-success">Update Counter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script>
        $(document).on("click", ".editCounterBtn", function() {
            var counterId = $(this).data("id");
            var counterName = $(this).data("name");
            var prefix = $(this).data("prefix");
            var status = $(this).data("status");


            $("#edit_counter_id").val(counterId);
            $("#edit_counter_name").val(counterName);
            $("#edit_prefix").val(prefix);
            $("#edit_status").val(status);

        });
    </script>
@endsection
