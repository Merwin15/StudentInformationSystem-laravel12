@extends('layouts.dashboardlayout')

@section('title', 'Course Details')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Course Details: {{ $course->name }}</h1>
        <div>
            <a href="{{ route('courses.edit', $course) }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-edit fa-sm text-white-50"></i> Edit Course
            </a>
            <a href="{{ route('courses.enroll', $course) }}" class="btn btn-success shadow-sm ml-2">
                <i class="fas fa-user-plus fa-sm text-white-50"></i> Enroll Students
            </a>
            <a href="{{ route('courses.index') }}" class="btn btn-secondary shadow-sm ml-2">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Courses
            </a>
        </div>
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

    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Course Code</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $course->code }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bookmark fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Credits</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $course->credits }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-graduation-cap fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Status</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <span class="badge badge-{{ $course->status === 'active' ? 'success' : 'warning' }}">
                                    {{ ucfirst($course->status) }}
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-toggle-on fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <!-- Course Details -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Course Information</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Teacher:</strong>
                        <p class="mb-0">{{ $course->teacher ? $course->teacher->name : 'Not Assigned' }}</p>
                    </div>
                    <div class="mb-3">
                        <strong>Description:</strong>
                        <p class="mb-0">{{ $course->description ?: 'No description available.' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <!-- Course Statistics -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Course Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Total Students:</strong>
                        <p class="mb-0">{{ $course->students->count() }}</p>
                    </div>
                    <div class="mb-3">
                        <strong>Average Grade:</strong>
                        <p class="mb-0">
                            @php
                                $avgGrade = $course->students->avg('pivot.grade');
                            @endphp
                            {{ $avgGrade ? number_format($avgGrade, 2) : 'No grades yet' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enrolled Students -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Enrolled Students</h6>
        </div>
        <div class="card-body">
            @if($course->students->isNotEmpty())
                <!-- Bulk Grade Update Form -->
                <form action="{{ route('courses.grades.bulk.update', ['course' => $course->id]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Student ID</th>
                                    <th>Name</th>
                                    <th>Current Grade</th>
                                    <th>New Grade</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($course->students as $student)
                                <tr>
                                    <td>{{ $student->student_id }}</td>
                                    <td>{{ $student->name }}</td>
                                    <td>{{ $student->pivot->grade ?? 'Not graded' }}</td>
                                    <td>
                                        <input type="number" 
                                               name="grades[{{ $student->id }}]" 
                                               class="form-control"
                                               value="{{ old('grades.' . $student->id, $student->pivot->grade) }}"
                                               min="0"
                                               max="100"
                                               step="0.01">
                                    </td>
                                    <td>
                                        <!-- Individual Grade Update Button -->
                                        <button type="button" 
                                                class="btn btn-primary btn-sm mr-1 update-grade-btn" 
                                                data-toggle="modal" 
                                                data-target="#updateGradeModal"
                                                data-student-id="{{ $student->id }}"
                                                data-student-name="{{ $student->name }}"
                                                data-current-grade="{{ $student->pivot->grade }}"
                                                data-update-url="{{ route('courses.student.grade.update', ['course' => $course->id, 'student' => $student->id]) }}">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <!-- Delete Grade Form -->
                                        <form action="{{ route('courses.student.grade.destroy', ['course' => $course->id, 'student' => $student->id]) }}" 
                                              method="POST" 
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Are you sure you want to remove this grade?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update All Grades
                        </button>
                    </div>
                </form>
            @else
                <p class="text-center text-muted my-5">No students enrolled in this course yet.</p>
            @endif
        </div>
    </div>
</div>

<!-- Individual Grade Update Modal -->
<div class="modal fade" id="updateGradeModal" tabindex="-1" role="dialog" aria-labelledby="updateGradeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateGradeModalLabel">Update Student Grade</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="individualGradeForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="studentName">Student</label>
                        <input type="text" class="form-control" id="studentName" readonly>
                    </div>
                    <div class="form-group">
                        <label for="grade">Grade</label>
                        <input type="number" 
                               class="form-control" 
                               id="grade" 
                               name="grade" 
                               min="0" 
                               max="100" 
                               step="0.01" 
                               required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Grade</button>
                </div>
            </form>
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
    $('#enrolledStudentsTable').DataTable({
        "order": [[1, "asc"]],
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "columnDefs": [
            { "orderable": false, "targets": [3, 4] }
        ]
    });

    // Handle update grade button click
    $('.update-grade-btn').click(function() {
        var button = $(this);
        var modal = $('#updateGradeModal');
        
        // Set modal values
        modal.find('#studentName').val(button.data('student-name'));
        modal.find('#grade').val(button.data('current-grade'));
        
        // Update form action URL
        modal.find('#individualGradeForm').attr('action', button.data('update-url'));
    });

    // Handle form submission
    $('#individualGradeForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                // Close modal
                $('#updateGradeModal').modal('hide');
                
                // Show success message
                Swal.fire({
                    title: 'Success!',
                    text: 'Grade updated successfully',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    // Reload page to show updated data
                    location.reload();
                });
            },
            error: function(xhr) {
                // Show error message
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to update grade',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    });
});
</script>
@endpush 