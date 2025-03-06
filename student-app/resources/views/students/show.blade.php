@extends('layouts.dashboardlayout')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Student Details</h1>
        <div>
            <a href="{{ route('students.edit', $student) }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-edit fa-sm text-white-50"></i> Edit Student
            </a>
            <a href="{{ route('students.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm ml-2">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Student Information Card -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Student Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>Student ID:</strong>
                            <p class="text-gray-800">{{ $student->student_id }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Course:</strong>
                            <p>
                                <span class="badge badge-info">{{ $student->course }}</span>
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>Full Name:</strong>
                            <p class="text-gray-800">{{ $student->name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Email:</strong>
                            <p class="text-gray-800">{{ $student->email }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>Phone:</strong>
                            <p class="text-gray-800">{{ $student->phone ?? 'Not provided' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Date of Birth:</strong>
                            <p class="text-gray-800">{{ $student->date_of_birth->format('F d, Y') }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>Enrollment Date:</strong>
                            <p class="text-gray-800">{{ $student->enrollment_date->format('F d, Y') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Status:</strong>
                            <p>
                                <span class="badge badge-{{ $student->status === 'active' ? 'success' : ($student->status === 'suspended' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($student->status) }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>Address:</strong>
                            <p class="text-gray-800">{{ $student->address ?? 'Not provided' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enrolled Courses Card -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Enrolled Courses</h6>
                    <a href="{{ route('students.courses', $student) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus fa-sm"></i> Manage Courses
                    </a>
                </div>
                <div class="card-body">
                    @if($student->courses->count() > 0)
                        <div class="list-group">
                            @foreach($student->courses as $course)
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $course->name }}</h6>
                                        <small>{{ $course->pivot->enrollment_date->format('M d, Y') }}</small>
                                    </div>
                                    <p class="mb-1">{{ $course->code }}</p>
                                    <small class="text-muted">{{ $course->credits }} credits</small>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-gray-500 my-3">No courses enrolled yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 