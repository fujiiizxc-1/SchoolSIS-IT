@extends('layouts.sis')
@section('title', 'Dashboard')

@section('content')
    @php($user = auth()->user())
    @if ($user->role === 'student')
        <h1>Student dashboard</h1>
        <p class="intro">Welcome, {{ $user->name }}.</p>
        <section class="panel">
            <h2>Your profile</h2>
            <dl class="details">
                <div><dt>Student ID</dt><dd>{{ $user->student->student_number }}</dd></div>
                <div><dt>Course</dt><dd>{{ $user->student->course }}</dd></div>
                <div><dt>Email address</dt><dd>{{ $user->email }}</dd></div>
            </dl>
        </section>
    @elseif ($user->role === 'teacher')
        <h1>Teacher dashboard</h1>
        <p class="intro">Welcome, {{ $user->name }}.</p>
        <a class="button" href="{{ route('teacher.subjects') }}">View assigned subjects</a>
    @elseif ($user->role === 'admin')
        <section class="admin-overview">
            <header class="admin-welcome">
                <div>
                    <p class="dashboard-kicker">School overview</p>
                    <h1>Welcome back, {{ $user->name }}</h1>
                    <p class="dashboard-term">School year {{ \App\Models\Enrollment::SCHOOL_YEAR }} · Semester {{ \App\Models\Enrollment::SEMESTER }}</p>
                </div>
                <a class="button" href="{{ route('admin.students', ['new' => 1]) }}">Register student</a>
            </header>

            <nav class="dashboard-stats" aria-label="School totals">
                <a class="dashboard-stat" href="{{ route('admin.students') }}">
                    <span>Students</span>
                    <strong>{{ number_format($schoolStats['students']) }}</strong>
                    <small>Manage student records</small>
                </a>
                <a class="dashboard-stat" href="{{ route('admin.teachers') }}">
                    <span>Teachers</span>
                    <strong>{{ number_format($schoolStats['teachers']) }}</strong>
                    <small>Manage teacher accounts</small>
                </a>
                <a class="dashboard-stat" href="{{ route('admin.subjects') }}">
                    <span>Subjects</span>
                    <strong>{{ number_format($schoolStats['subjects']) }}</strong>
                    <small>Manage subjects and assignments</small>
                </a>
                <a class="dashboard-stat" href="{{ route('admin.enrollments') }}">
                    <span>Current enrollments</span>
                    <strong>{{ number_format($schoolStats['enrollments']) }}</strong>
                    <small>View this term's enrollments</small>
                </a>
            </nav>

            <div class="dashboard-columns">
                <section class="dashboard-panel">
                    <header class="dashboard-panel-header">
                        <div>
                            <h2>Enrollment by subject</h2>
                            <p>Most enrolled subjects this term</p>
                        </div>
                        <a href="{{ route('admin.subjects') }}">Subjects</a>
                    </header>
                    @if ($schoolStats['enrollments'] > 0 && $topSubjects->isNotEmpty())
                        @php($largestSubjectCount = max(1, (int) $topSubjects->max('current_enrollment_count')))
                        <ul class="subject-bars">
                            @foreach ($topSubjects as $subject)
                                @php($barWidth = (int) round($subject->current_enrollment_count / $largestSubjectCount * 100))
                                <li>
                                    <div class="subject-bar-label">
                                        <span><strong>{{ $subject->code }}</strong> {{ $subject->name }}</span>
                                        <span>{{ number_format($subject->current_enrollment_count) }}</span>
                                    </div>
                                    <div class="bar-track" role="progressbar" aria-label="{{ $subject->code }} enrollments" aria-valuemin="0" aria-valuemax="{{ $largestSubjectCount }}" aria-valuenow="{{ $subject->current_enrollment_count }}">
                                        <span style="width: {{ $barWidth }}%"></span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="dashboard-empty">No students have enrolled in a subject this term yet.</p>
                    @endif
                </section>

                <section class="dashboard-panel">
                    <header class="dashboard-panel-header">
                        <div>
                            <h2>Grade progress</h2>
                            <p>Final grades for current enrollments</p>
                        </div>
                        <a href="{{ route('admin.enrollments') }}">Review</a>
                    </header>
                    <div class="grade-summary">
                        <div><strong>{{ number_format($schoolStats['grades']) }}</strong><span>Grades saved</span></div>
                        <div><strong>{{ number_format($schoolStats['pendingGrades']) }}</strong><span>Awaiting a grade</span></div>
                    </div>
                    @if ($schoolStats['enrollments'] > 0)
                        <div class="grade-progress" role="progressbar" aria-label="Current term grades saved" aria-valuemin="0" aria-valuemax="{{ $schoolStats['enrollments'] }}" aria-valuenow="{{ $schoolStats['grades'] }}">
                            <span style="width: {{ $schoolStats['gradeProgress'] }}%"></span>
                        </div>
                        <p class="progress-caption">{{ $schoolStats['gradeProgress'] }}% of current term enrollments have a final grade.</p>
                    @else
                        <p class="dashboard-empty">No current term enrollments to grade yet.</p>
                    @endif
                </section>
            </div>

            <section class="dashboard-panel recent-enrollments">
                <header class="dashboard-panel-header">
                    <div>
                        <h2>Recent enrollments</h2>
                        <p>Latest student activity for this term</p>
                    </div>
                    <a href="{{ route('admin.enrollments') }}">View all</a>
                </header>
                @if ($recentEnrollments->isNotEmpty())
                    <table class="record-table">
                        <caption class="visually-hidden">Recent current term enrollments</caption>
                        <thead><tr><th scope="col">Student</th><th scope="col">Subject</th><th scope="col">Term</th><th scope="col">Final grade</th></tr></thead>
                        <tbody>
                            @foreach ($recentEnrollments as $enrollment)
                                <tr>
                                    <th scope="row">
                                        {{ $enrollment->student->user->name }}
                                        <p class="secondary-text">{{ $enrollment->student->student_number }}</p>
                                    </th>
                                    <td data-label="Subject">{{ $enrollment->subject->code }} · {{ $enrollment->subject->name }}</td>
                                    <td data-label="Term">{{ $enrollment->school_year }} · Semester {{ $enrollment->semester }}</td>
                                    <td data-label="Final grade">{{ $enrollment->grade?->grade ?? 'Awaiting grade' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="dashboard-empty">There are no enrollments to show yet. Students will appear here after enrolling in a subject.</p>
                @endif
            </section>
        </section>
    @endif
@endsection
