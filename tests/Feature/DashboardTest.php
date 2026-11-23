<?php

namespace Tests\Feature;

use App\Models\Enrollment;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_shows_real_current_term_school_activity(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'name' => 'Admin Avery']);
        $teacher = User::factory()->create(['role' => 'teacher']);
        $teacher->teacher()->create();

        $gradedStudent = $this->student('S-401', 'Jordan Lee');
        $pendingStudent = $this->student('S-402', 'Taylor Kim');
        $activeSubject = Subject::create(['code' => 'IS401', 'name' => 'Systems Design']);
        $emptySubject = Subject::create(['code' => 'IS402', 'name' => 'Database Concepts']);

        $gradedEnrollment = $gradedStudent->student->enrollments()->create([
            'subject_id' => $activeSubject->id,
            'school_year' => Enrollment::SCHOOL_YEAR,
            'semester' => Enrollment::SEMESTER,
        ]);
        $gradedEnrollment->grade()->create(['grade' => 92]);

        $pendingEnrollment = $pendingStudent->student->enrollments()->create([
            'subject_id' => $activeSubject->id,
            'school_year' => Enrollment::SCHOOL_YEAR,
            'semester' => Enrollment::SEMESTER,
        ]);

        $historicalEnrollment = $gradedStudent->student->enrollments()->create([
            'subject_id' => $emptySubject->id,
            'school_year' => '2025-2026',
            'semester' => '2',
        ]);
        $historicalEnrollment->grade()->create(['grade' => 84]);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Welcome back, Admin Avery')
            ->assertSee('Jordan Lee')
            ->assertSee('Taylor Kim')
            ->assertSee('IS401')
            ->assertDontSee('IS402')
            ->assertViewHas('schoolStats', fn (array $stats) =>
                $stats['students'] === 2
                && $stats['teachers'] === 1
                && $stats['subjects'] === 2
                && $stats['enrollments'] === 2
                && $stats['grades'] === 1
                && $stats['pendingGrades'] === 1
                && $stats['gradeProgress'] === 50
            )
            ->assertViewHas('topSubjects', fn ($subjects) =>
                $subjects->pluck('code')->all() === ['IS401']
                && $subjects->first()->current_enrollment_count === 2
            )
            ->assertViewHas('recentEnrollments', fn ($enrollments) =>
                $enrollments->count() === 2
                && $enrollments->contains('id', $pendingEnrollment->id)
            );
    }

    private function student(string $studentNumber, string $name): User
    {
        $user = User::factory()->create(['role' => 'student', 'name' => $name]);
        $user->student()->create(['student_number' => $studentNumber, 'course' => 'Information Systems']);

        return $user;
    }
}
