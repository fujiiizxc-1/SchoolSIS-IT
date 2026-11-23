<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        if ($user->role !== 'admin') {
            return view('dashboard');
        }

        $termEnrollments = fn ($query) => $query
            ->where('school_year', Enrollment::SCHOOL_YEAR)
            ->where('semester', Enrollment::SEMESTER);

        $enrollmentCount = Enrollment::query()->where($termEnrollments)->count();
        $gradedCount = Grade::query()->whereHas('enrollment', $termEnrollments)->count();

        return view('dashboard', [
            'schoolStats' => [
                'students' => Student::count(),
                'teachers' => Teacher::count(),
                'subjects' => Subject::count(),
                'enrollments' => $enrollmentCount,
                'grades' => $gradedCount,
                'pendingGrades' => max(0, $enrollmentCount - $gradedCount),
                'gradeProgress' => $enrollmentCount > 0 ? (int) round($gradedCount / $enrollmentCount * 100) : 0,
            ],
            'topSubjects' => Subject::query()
                ->whereHas('enrollments', $termEnrollments)
                ->withCount(['enrollments as current_enrollment_count' => $termEnrollments])
                ->orderByDesc('current_enrollment_count')
                ->orderBy('code')
                ->limit(5)
                ->get(),
            'recentEnrollments' => Enrollment::query()
                ->where($termEnrollments)
                ->with(['student.user', 'subject', 'grade'])
                ->latest('created_at')
                ->limit(6)
                ->get(),
        ]);
    }
}
