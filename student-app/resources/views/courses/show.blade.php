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
                <form action="{{ route('courses.grades.update', $course) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="table-responsive">
                        <table class="table table-bordered" id="enrolledStudentsTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Student ID</th>
                                    <th>Name</th>
                                    <th>Enrollment Date</th>
                                    <th>Grade</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($course->students as $student)
                                    <tr>
                                        <td>{{ $student->student_id }}</td>
                                        <td>{{ $student->name }}</td>
                                        <td>{{ $student->pivot->enrollment_date->format('M d, Y') }}</td>
                                        <td>
                                            <form action="{{ route('courses.student.grade.update', ['course' => $course->id, 'student' => $student->id]) }}" 
                                                  method="POST" 
                                                  class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <div class="input-group input-group-sm">
                                                    <input type="number" 
                                                           class="form-control form-control-sm" 
                                                           name="grade" 
                                                           value="{{ $student->pivot->grade }}"
                                            <input type="number" class="form-control form-control-sm" 
                                                name="grades[{{ $student->id }}]" 
                                                value="{{ $student->pivot->grade }}"
                                                min="0" max="100" step="0.01">
                                        </td>
                                        <td>
                                            <form action="{{ route('courses.students.remove', [$course, $student]) }}" 
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" 
                                                    onclick="return confirm('Are you sure you want to remove this student from the course?')">
                                                    <i class="fas fa-user-minus"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-save"></i> Update Grades
                        </button>
                    </div>
                </form>
            @else
                <p class="text-center text-muted my-5">No students enrolled in this course yet.</p>
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
    $('#enrolledStudentsTable').DataTable({
        "order": [[1, "asc"]],
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "columnDefs": [
            { "orderable": false, "targets": [3, 4] }
        ]
    });
});
</script>
@endpush 