@extends('layouts.dashboardlayout')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Enroll Student</h1>
        <a href="{{ route('students.index') }}" class="d-none d-sm-inline-block btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Students
        </a>
    </div>

    <!-- Create Student Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Student Information</h6>
        </div>
        <div class="card-body">
            <form id="createStudentForm" action="{{ route('students.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="student_id">Student ID</label>
                            <input type="text" class="form-control @error('student_id') is-invalid @enderror" 
                                id="student_id" name="student_id" value="{{ old('student_id') }}" required>
                            @error('student_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="course">Course</label>
                            <select class="form-control @error('course') is-invalid @enderror" 
                                id="course" name="course" required>
                                <option value="">Select Course</option>
                                <option value="BSIT" {{ old('course') == 'BSIT' ? 'selected' : '' }}>BSIT</option>
                                <option value="BSFT" {{ old('course') == 'BSFT' ? 'selected' : '' }}>BSFT</option>
                                <option value="BSEMC" {{ old('course') == 'BSEMC' ? 'selected' : '' }}>BSEMC</option>
                            </select>
                            @error('course')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                id="phone" name="phone" value="{{ old('phone') }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="date_of_birth">Date of Birth</label>
                            <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" 
                                id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" required>
                            @error('date_of_birth')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="enrollment_date">Enrollment Date</label>
                            <input type="date" class="form-control @error('enrollment_date') is-invalid @enderror" 
                                id="enrollment_date" name="enrollment_date" value="{{ old('enrollment_date') }}" required>
                            @error('enrollment_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control @error('status') is-invalid @enderror" 
                                id="status" name="status" required>
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="graduated" {{ old('status') == 'graduated' ? 'selected' : '' }}>Graduated</option>
                                <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                id="address" name="address" rows="3">{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="text-right">
                    <button type="button" class="btn btn-primary" onclick="confirmEnrollment()">
                        <i class="fas fa-save"></i> Enroll Student
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Enrollment Confirmation Modal -->
<div class="modal fade" id="enrollmentModal" tabindex="-1" role="dialog" aria-labelledby="enrollmentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="enrollmentModalLabel">Confirm Student Enrollment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Please review the following information:</p>
                <div class="card">
                    <div class="card-body">
                        <p><strong>Name:</strong> <span id="confirmName"></span></p>
                        <p><strong>Student ID:</strong> <span id="confirmStudentId"></span></p>
                        <p><strong>Course:</strong> <span id="confirmCourse"></span></p>
                        <p><strong>Email:</strong> <span id="confirmEmail"></span></p>
                    </div>
                </div>
                <p class="mt-3">Are you sure you want to enroll this student?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitForm()">
                    Confirm Enrollment
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Make sure jQuery and Bootstrap JS are included in your layout -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Verify jQuery and Bootstrap are loaded
    if (typeof jQuery === 'undefined') {
        console.error('jQuery is not loaded');
        return;
    }
    if (typeof bootstrap === 'undefined') {
        console.error('Bootstrap JS is not loaded');
        return;
    }
});

function confirmEnrollment() {
    // Form validation
    const form = document.getElementById('createStudentForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    // Get form values
    const name = document.getElementById('name').value;
    const studentId = document.getElementById('student_id').value;
    const course = document.getElementById('course').value;
    const email = document.getElementById('email').value;

    // Validate required fields
    if (!name || !studentId || !course || !email) {
        alert('Please fill in all required fields');
        return;
    }

    // Update modal with form values
    document.getElementById('confirmName').textContent = name;
    document.getElementById('confirmStudentId').textContent = studentId;
    document.getElementById('confirmCourse').textContent = course;
    document.getElementById('confirmEmail').textContent = email;

    // Show modal using Bootstrap 5 syntax
    const modal = new bootstrap.Modal(document.getElementById('enrollmentModal'));
    modal.show();
}

function submitForm() {
    // Disable the confirm button to prevent double submission
    const confirmButton = document.querySelector('#enrollmentModal .btn-primary');
    confirmButton.disabled = true;
    confirmButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enrolling...';

    // Submit the form
    document.getElementById('createStudentForm').submit();
}
</script>
@endpush 