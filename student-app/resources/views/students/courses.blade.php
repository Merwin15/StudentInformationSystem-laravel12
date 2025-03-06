@extends('layouts.dashboardlayout')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $student->name }}'s Courses</h1>
        <a href="{{ route('students.show', $student) }}" class="d-none d-sm-inline-block btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Student Details
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Enrolled Courses by Year -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Enrolled Courses by Year</h6>
                </div>
                <div class="card-body">
                    @if(isset($enrolledCoursesByYear) && $enrolledCoursesByYear->count() > 0)
                        <div class="accordion" id="coursesAccordion">
                            @foreach($enrolledCoursesByYear as $year => $courses)
                                <div class="card">
                                    <div class="card-header" id="heading{{ $year }}">
                                        <h2 class="mb-0">
                                            <button class="btn btn-link btn-block text-left" type="button" 
                                                    data-toggle="collapse" data-target="#collapse{{ $year }}" 
                                                    aria-expanded="true" aria-controls="collapse{{ $year }}">
                                                Year {{ $year }} ({{ $courses->count() }} courses)
                                            </button>
                                        </h2>
                                    </div>

                                    <div id="collapse{{ $year }}" class="collapse show" 
                                         aria-labelledby="heading{{ $year }}" data-parent="#coursesAccordion">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Course Code</th>
                                                            <th>Course Name</th>
                                                            <th>Teacher</th>
                                                            <th>Enrollment Date</th>
                                                            <th>Grade</th>
                                                            <th>Status</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($courses as $course)
                                                            <tr>
                                                                <td>{{ $course->code }}</td>
                                                                <td>{{ $course->name }}</td>
                                                                <td>{{ $course->teacher->name }}</td>
                                                                <td>
                                                                    @if($course->pivot->enrollment_date)
                                                                        {{ $course->pivot->enrollment_date instanceof \Carbon\Carbon 
                                                                            ? $course->pivot->enrollment_date->format('M d, Y')
                                                                            : \Carbon\Carbon::parse($course->pivot->enrollment_date)->format('M d, Y') }}
                                                                    @else
                                                                        N/A
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    {{ $course->pivot->grade ?? 'Not graded' }}
                                                                </td>
                                                                <td>
                                                                    @php
                                                                        $hasGrade = !is_null($course->pivot->grade);
                                                                        $enrollmentDate = $course->pivot->enrollment_date instanceof \Carbon\Carbon 
                                                                            ? $course->pivot->enrollment_date 
                                                                            : \Carbon\Carbon::parse($course->pivot->enrollment_date);
                                                                        $isCurrentSemester = $enrollmentDate->isCurrentQuarter();
                                                                    @endphp
                                                                    
                                                                    @if($hasGrade)
                                                                        <span class="badge badge-success">Completed</span>
                                                                    @elseif($isCurrentSemester)
                                                                        <span class="badge badge-primary">Current</span>
                                                                    @else
                                                                        <span class="badge badge-secondary">Past</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @php
                                                                        $hasGrade = !is_null($course->pivot->grade);
                                                                        $enrollmentDate = $course->pivot->enrollment_date instanceof \Carbon\Carbon 
                                                                            ? $course->pivot->enrollment_date 
                                                                            : \Carbon\Carbon::parse($course->pivot->enrollment_date);
                                                                        $isCurrentSemester = $enrollmentDate->isCurrentQuarter();
                                                                    @endphp
                                                                    
                                                                    @if($hasGrade)
                                                                        <button type="button" class="btn btn-danger btn-sm" disabled 
                                                                                data-toggle="tooltip" data-placement="top" 
                                                                                title="Cannot remove: Course has a grade">
                                                                            <i class="fas fa-trash"></i>
                                                                        </button>
                                                                    @elseif(!$isCurrentSemester)
                                                                        <button type="button" class="btn btn-danger btn-sm" disabled 
                                                                                data-toggle="tooltip" data-placement="top" 
                                                                                title="Cannot remove: Past enrollment">
                                                                            <i class="fas fa-trash"></i>
                                                                        </button>
                                                                    @else
                                                                        <form action="{{ route('students.courses.remove', [$student, $course]) }}" 
                                                                              method="POST" class="d-inline">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit" class="btn btn-danger btn-sm" 
                                                                                    onclick="return confirm('Are you sure you want to remove this course? This action cannot be undone.')">
                                                                                <i class="fas fa-trash"></i>
                                                                            </button>
                                                                        </form>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-muted my-3">No courses enrolled yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Available Courses -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Available Courses</h6>
                </div>
                <div class="card-body">
                    @if($availableCourses->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Course Code</th>
                                        <th>Course Name</th>
                                        <th>Teacher</th>
                                        <th>Credits</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($availableCourses as $course)
                                        <tr>
                                            <td>{{ $course->code }}</td>
                                            <td>{{ $course->name }}</td>
                                            <td>{{ $course->teacher->name }}</td>
                                            <td>{{ $course->credits }}</td>
                                            <td>
                                                <form action="{{ route('students.courses.add', [$student, $course]) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm">
                                                        <i class="fas fa-plus"></i> Enroll
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $availableCourses->links() }}
                        </div>
                    @else
                        <p class="text-center text-muted my-3">No available courses to enroll.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Course Enrollment Confirmation Modal -->
<div class="modal fade" id="enrollCourseModal" tabindex="-1" role="dialog" aria-labelledby="enrollCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="enrollCourseModalLabel">Confirm Course Enrollment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <strong>Student:</strong> {{ $student->name }} ({{ $student->student_id }})
                </div>
                <p>Are you sure you want to enroll in this course?</p>
                <div class="card">
                    <div class="card-body">
                        <p><strong>Course Code:</strong> <span id="confirmCourseCode"></span></p>
                        <p><strong>Course Name:</strong> <span id="confirmCourseName"></span></p>
                        <p><strong>Teacher:</strong> <span id="confirmTeacher"></span></p>
                        <p><strong>Credits:</strong> <span id="confirmCredits"></span></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmEnrollBtn">
                    Confirm Enrollment
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add this modal for course removal confirmation -->
<div class="modal fade" id="removeCourseModal" tabindex="-1" role="dialog" aria-labelledby="removeCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="removeCourseModalLabel">Confirm Course Removal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Warning!</strong> This action cannot be undone.
                </div>
                <p>Are you sure you want to remove this course?</p>
                <div class="card">
                    <div class="card-body">
                        <p><strong>Course:</strong> <span id="removeCourseName"></span></p>
                        <p><strong>Enrollment Date:</strong> <span id="removeEnrollmentDate"></span></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmRemoveBtn">
                    Remove Course
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Show only the latest year's courses by default
        $('.collapse').not(':first').removeClass('show');

        // Store the form being submitted
        let formToSubmit = null;

        // Handle enrollment button clicks
        $('form[action*="courses/add"]').on('submit', function(e) {
            e.preventDefault();
            formToSubmit = this;
            
            // Get course details from the row
            const row = $(this).closest('tr');
            $('#confirmCourseCode').text(row.find('td:eq(0)').text());
            $('#confirmCourseName').text(row.find('td:eq(1)').text());
            $('#confirmTeacher').text(row.find('td:eq(2)').text());
            $('#confirmCredits').text(row.find('td:eq(3)').text());
            
            // Show the modal
            $('#enrollCourseModal').modal('show');
        });

        // Handle confirmation button click
        $('#confirmEnrollBtn').click(function() {
            if (formToSubmit) {
                // Disable the button and show loading state
                $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Enrolling...');
                
                // Submit the form
                formToSubmit.submit();
            }
        });

        // Reset form reference when modal is hidden
        $('#enrollCourseModal').on('hidden.bs.modal', function() {
            formToSubmit = null;
            $('#confirmEnrollBtn').prop('disabled', false).text('Confirm Enrollment');
        });

        let removeFormToSubmit = null;

        // Handle remove button clicks
        $('form[action*="courses/remove"]').on('submit', function(e) {
            e.preventDefault();
            removeFormToSubmit = this;
            
            // Get course details from the row
            const row = $(this).closest('tr');
            $('#removeCourseName').text(row.find('td:eq(1)').text());
            $('#removeEnrollmentDate').text(row.find('td:eq(3)').text());
            
            // Show the modal
            $('#removeCourseModal').modal('show');
        });

        // Handle remove confirmation button click
        $('#confirmRemoveBtn').click(function() {
            if (removeFormToSubmit) {
                // Disable the button and show loading state
                $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Removing...');
                
                // Submit the form
                removeFormToSubmit.submit();
            }
        });

        // Reset form reference when modal is hidden
        $('#removeCourseModal').on('hidden.bs.modal', function() {
            removeFormToSubmit = null;
            $('#confirmRemoveBtn').prop('disabled', false).text('Remove Course');
        });
    });
</script>
@endpush 