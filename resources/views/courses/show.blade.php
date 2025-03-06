{{ $student->pivot->enrollment_date ? $student->pivot->enrollment_date->format('Y-m-d') : 'Not enrolled' }}
{{ $student->pivot->enrollment_date?->format('Y-m-d') ?? 'Not enrolled' }} 