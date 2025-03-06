@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Student Enrollment Details</h2>
    
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">{{ $student->name }}</h5>
            <p class="card-text">
                <strong>Course:</strong> {{ $course->name }}<br>
                <strong>Enrollment Status:</strong> 
                @if($isEnrolled)
                    <span class="text-success">Enrolled</span><br>
                    <strong>Enrollment Date:</strong> {{ $enrollmentDate ? $enrollmentDate->format('Y-m-d') : 'N/A' }}<br>
                    <strong>Grade:</strong> {{ $grade ?? 'Not graded yet' }}
                @else
                    <span class="text-danger">Not Enrolled</span>
                @endif
            </p>
        </div>
    </div>
</div>
@endsection 