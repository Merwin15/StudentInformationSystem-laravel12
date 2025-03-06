<form action="{{ route('courses.grades.destroy', ['course' => $course->id, 'student' => $student->id]) }}" 
      method="POST" 
      class="d-inline">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger btn-sm" 
            onclick="return confirm('Are you sure you want to remove this grade?')">
        <i class="fas fa-trash"></i>
    </button>
</form> 