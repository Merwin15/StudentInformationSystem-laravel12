@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Admin Dashboard</h2>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    Courses
                </div>
                <div class="card-body">
                    <h5>Total Courses: {{ $courses->count() }}</h5>
                    <a href="{{ route('courses.index') }}" class="btn btn-primary">Manage Courses</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    Students
                </div>
                <div class="card-body">
                    <h5>Total Students: {{ $students->count() }}</h5>
                    <a href="{{ route('students.index') }}" class="btn btn-primary">Manage Students</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    Teachers
                </div>
                <div class="card-body">
                    <h5>Total Teachers: {{ $teachers->count() }}</h5>
                    <a href="{{ route('teachers.index') }}" class="btn btn-primary">Manage Teachers</a>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <h3>Recent Courses</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Course Name</th>
                    <th>Teacher</th>
                    <th>Students</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($courses as $course)
                <tr>
                    <td>{{ $course->name }}</td>
                    <td>{{ $course->teacher->name ?? 'No Teacher' }}</td>
                    <td>{{ $course->students->count() }}</td>
                    <td>
                        <a href="{{ route('courses.show', $course) }}" class="btn btn-sm btn-info">View</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection 