@extends('layouts.dashboardlayout')

@section('title', 'Enroll Students')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Enroll Students in {{ $course->name }}</h1>
        <a href="{{ route('courses.show', $course) }}" class="d-none d-sm-inline-block btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Course
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Available Students</h6>
        </div>
        <div class="card-body">
            @if($availableStudents->isNotEmpty())
                <form action="{{ route('courses.enroll.store', $course) }}" method="POST">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-bordered" id="availableStudentsTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th width="50px">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="selectAll">
                                            <label class="custom-control-label" for="selectAll"></label>
                                        </div>
                                    </th>
                                    <th>Student ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($availableStudents as $student)
                                    <tr>
                                        <td>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input student-checkbox" 
                                                    id="student{{ $student->id }}" name="student_ids[]" 
                                                    value="{{ $student->id }}">
                                                <label class="custom-control-label" for="student{{ $student->id }}"></label>
                                            </div>
                                        </td>
                                        <td>{{ $student->student_id }}</td>
                                        <td>{{ $student->name }}</td>
                                        <td>{{ $student->email }}</td>
                                        <td>
                                            <span class="badge badge-{{ $student->status === 'active' ? 'success' : 'warning' }}">
                                                {{ ucfirst($student->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary" id="enrollButton" disabled>
                            <i class="fas fa-user-plus"></i> Enroll Selected Students
                        </button>
                    </div>
                </form>
            @else
                <p class="text-center text-muted my-5">No students available for enrollment.</p>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function() {
    const table = $('#availableStudentsTable').DataTable({
        "order": [[2, "asc"]],
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "columnDefs": [
            { "orderable": false, "targets": 0 }
        ]
    });

    // Handle select all checkbox
    $('#selectAll').change(function() {
        $('.student-checkbox').prop('checked', $(this).prop('checked'));
        updateEnrollButton();
    });

    // Handle individual checkboxes
    $(document).on('change', '.student-checkbox', function() {
        updateEnrollButton();
        
        // Update select all checkbox
        const allChecked = $('.student-checkbox:checked').length === $('.student-checkbox').length;
        $('#selectAll').prop('checked', allChecked);
    });

    // Update enroll button state
    function updateEnrollButton() {
        const checkedCount = $('.student-checkbox:checked').length;
        $('#enrollButton').prop('disabled', checkedCount === 0);
    }

    // Handle search and pagination
    table.on('draw', function() {
        $('#selectAll').prop('checked', false);
        updateEnrollButton();
    });
});
</script>
@endpush 