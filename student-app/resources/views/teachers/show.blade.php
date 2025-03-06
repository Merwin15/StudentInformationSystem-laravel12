@extends('layouts.dashboardlayout')

@section('title', 'Teacher Details')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Teacher Details</h1>
        <div>
            <a href="{{ route('teachers.edit', $teacher) }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-edit fa-sm text-white-50"></i> Edit Teacher
            </a>
            <a href="{{ route('teachers.index') }}" class="btn btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Teachers
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Teacher Information Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-center mb-3">
                        <img class="img-profile rounded-circle" src="https://ui-avatars.com/api/?name={{ urlencode($teacher->name) }}&size=128">
                    </div>
                    <div class="text-center">
                        <h4 class="font-weight-bold text-primary">{{ $teacher->name }}</h4>
                        <p class="mb-2">{{ $teacher->department }}</p>
                        <span class="badge badge-{{ $teacher->status === 'active' ? 'success' : 'warning' }}">
                            {{ ucfirst($teacher->status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Information Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-3">Contact Information</div>
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="mb-2">
                                <i class="fas fa-envelope fa-fw"></i> {{ $teacher->email }}
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-phone fa-fw"></i> {{ $teacher->phone ?? 'Not provided' }}
                            </div>
                            <div>
                                <i class="fas fa-map-marker-alt fa-fw"></i> {{ $teacher->address ?? 'Not provided' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-3">Statistics</div>
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="mb-2">
                                <i class="fas fa-book fa-fw"></i> Courses: {{ $teacher->courses->count() }}
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-users fa-fw"></i> Total Students: {{ $teacher->total_students }}
                            </div>
                            @if($teacher->average_grade)
                            <div>
                                <i class="fas fa-star fa-fw"></i> Average Grade: {{ $teacher->average_grade }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Courses Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Assigned Courses</h6>
            <a href="{{ route('teachers.courses', $teacher) }}" class="btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-book fa-sm text-white-50"></i> View All Courses
            </a>
        </div>
        <div class="card-body">
            @if($teacher->courses->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Course Code</th>
                                <th>Course Name</th>
                                <th>Students</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($teacher->courses as $course)
                                <tr>
                                    <td>{{ $course->code }}</td>
                                    <td>{{ $course->name }}</td>
                                    <td>{{ $course->students_count ?? 0 }}</td>
                                    <td>
                                        <span class="badge badge-{{ $course->status === 'active' ? 'success' : 'warning' }}">
                                            {{ ucfirst($course->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-center text-muted my-3">No courses assigned yet.</p>
            @endif
        </div>
    </div>
</div>
@endsection 