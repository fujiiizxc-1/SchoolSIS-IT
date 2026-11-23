@php
    $links = match (auth()->user()->role) {
        'student' => ['profile' => 'My profile', 'subjects' => 'Available subjects', 'enrollments' => 'My subjects', 'grades' => 'My grades'],
        'teacher' => ['teacher.subjects' => 'Assigned subjects'],
        'admin' => ['admin.students' => 'Students', 'admin.subjects' => 'Subjects', 'admin.enrollments' => 'Enrollments', 'admin.teachers' => 'Teachers'],
        default => [],
    };
@endphp
<aside class="record-nav">
    <p class="nav-heading">School</p>
    <nav aria-label="School records" class="nav">
        @foreach (['dashboard' => 'Dashboard'] + $links as $routeName => $label)
            <a href="{{ route($routeName) }}" @if (request()->routeIs($routeName, $routeName.'.*')) aria-current="page" @endif>{{ $label }}</a>
        @endforeach
    </nav>
    <p class="term-note">Current term<br><strong>{{ \App\Models\Enrollment::SCHOOL_YEAR }}</strong><br>Semester {{ \App\Models\Enrollment::SEMESTER }}</p>
</aside>
