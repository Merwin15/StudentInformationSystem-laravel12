@extends('layouts.dashboardlayout')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Students</h1>
        <div>
            <a href="{{ route('students.create') }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50"></i> Add New Student
            </a>
            <button type="button" class="btn btn-danger shadow-sm ml-2" 
                    data-toggle="modal" data-target="#deleteAllModal">
                <i class="fas fa-trash fa-sm text-white-50"></i> Delete All Students
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {{ session('warning') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Students Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Student List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="studentsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Course</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Enrollment Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                            <tr>
                                <td>{{ $student->student_id }}</td>
                                <td>{{ $student->name }}</td>
                                <td>
                                    <span class="badge badge-info">
                                        {{ $student->course }}
                                    </span>
                                </td>
                                <td>{{ $student->email }}</td>
                                <td>
                                    <span class="badge badge-{{ $student->status === 'active' ? 'success' : ($student->status === 'suspended' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($student->status) }}
                                    </span>
                                </td>
                                <td>{{ $student->enrollment_date->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('students.show', $student) }}" class="btn btn-info btn-sm" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('students.edit', $student) }}" class="btn btn-primary btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('students.courses', $student) }}" class="btn btn-success btn-sm" title="Manage Courses">
                                            <i class="fas fa-book"></i>
                                        </a>
                                        @if($student->courses->count() > 0)
                                            <button type="button" class="btn btn-danger btn-sm" disabled 
                                                    data-toggle="tooltip" data-placement="top" 
                                                    title="Cannot delete: Enrolled in {{ $student->courses->count() }} course(s)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @else
                                            <form action="{{ route('students.destroy', $student) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Delete"
                                                        onclick="return confirm('Are you sure you want to delete this student?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $students->links() }}
            </div>
        </div>
    </div>

    <!-- Delete All Confirmation Modal -->
    <div class="modal fade" id="deleteAllModal" tabindex="-1" role="dialog" aria-labelledby="deleteAllModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteAllModalLabel">Delete All Students</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Warning!</strong>
                        <ul>
                            <li>Only students who are not enrolled in any courses will be deleted.</li>
                            <li>Students currently enrolled in courses will be skipped.</li>
                            <li>This action cannot be undone!</li>
                        </ul>
                    </div>
                    <p>Please type <strong>DELETE ALL</strong> to confirm:</p>
                    <input type="text" id="deleteConfirmation" class="form-control" placeholder="Type DELETE ALL">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <form id="deleteAllForm" action="{{ route('students.delete-all') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" id="confirmDeleteAllBtn" disabled>
                            Delete All Students
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle delete confirmation input
    const deleteInput = document.getElementById('deleteConfirmation');
    const deleteButton = document.getElementById('confirmDeleteAllBtn');
    
    deleteInput.addEventListener('input', function() {
        deleteButton.disabled = this.value !== 'DELETE ALL';
    });

    // Handle form submission
    const deleteAllForm = document.getElementById('deleteAllForm');
    deleteAllForm.addEventListener('submit', function(e) {
        const confirmInput = document.getElementById('deleteConfirmation');
        if (confirmInput.value !== 'DELETE ALL') {
            e.preventDefault();
            alert('Please type DELETE ALL to confirm');
            return false;
        }
        // Disable the submit button to prevent double submission
        document.getElementById('confirmDeleteAllBtn').disabled = true;
    });
});
</script>
@endpush 