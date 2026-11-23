<?php

namespace Tests\Feature;

use App\Models\Enrollment;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicRecordsTest extends TestCase
{
    use RefreshDatabase;

    public function test_students_can_only_update_their_own_profile_and_enroll_once_per_term(): void
    {
        $student = $this->student('S-101');
        $other = $this->student('S-102');
        $subject = Subject::create(['code' => 'IS101', 'name' => 'Information Systems']);
        $otherEnrollment = $other->student->enrollments()->create([
            'subject_id' => $subject->id,
            'school_year' => Enrollment::SCHOOL_YEAR,
            'semester' => Enrollment::SEMESTER,
        ]);
        $otherEnrollment->grade()->create(['grade' => 75]);

        $this->actingAs($student);
        $this->get('/profile')->assertOk()->assertSee('S-101')->assertDontSee('S-102');
        $this->get('/profile/'.$other->student->id)->assertNotFound();
        $this->get('/admin/students')->assertForbidden();
        $this->put('/profile', ['name' => '', 'role' => 'admin'])->assertSessionHasErrors('name');
        $this->put('/profile', ['name' => 'Avery Rivera', 'role' => 'admin', 'user_id' => $other->id])
            ->assertRedirect('/profile');
        $this->assertDatabaseHas('users', ['id' => $student->id, 'name' => 'Avery Rivera', 'role' => 'student']);
        $this->assertDatabaseHas('users', ['id' => $other->id, 'name' => $other->name]);

        $this->get('/subjects')->assertOk()->assertSee('IS101');
        $this->post('/enrollments', ['subject_id' => 500])->assertSessionHasErrors('subject_id');
        $this->post('/enrollments', ['subject_id' => $subject->id, 'student_id' => $other->student->id])
            ->assertRedirect('/enrollments')->assertSessionHas('status');
        $this->get('/enrollments')->assertOk()->assertSee('IS101');
        $this->post('/enrollments', ['subject_id' => $subject->id])->assertSessionHasErrors('subject_id');
        $this->assertDatabaseCount('enrollments', 2);
        $this->assertDatabaseHas('enrollments', ['student_id' => $student->student->id, 'subject_id' => $subject->id]);
        $this->get('/grades')->assertOk()->assertSee('Pending')->assertViewHas('enrollments', function ($enrollments) use ($student) {
            return $enrollments->count() === 1 && $enrollments->first()->student_id === $student->student->id;
        });
        $this->put('/teacher/grades/'.$otherEnrollment->id, ['grade' => 99])->assertForbidden();
        $this->assertDatabaseHas('grades', ['enrollment_id' => $otherEnrollment->id, 'grade' => 75]);
    }

    public function test_teachers_can_only_grade_students_enrolled_in_their_assigned_subjects(): void
    {
        $student = $this->student('S-201');
        $assigned = $this->staff('teacher');
        $unassigned = $this->staff('teacher');
        $subject = $assigned->teacher->subjects()->create(['code' => 'IS201', 'name' => 'Systems Integration']);
        $enrollment = $student->student->enrollments()->create([
            'subject_id' => $subject->id,
            'school_year' => Enrollment::SCHOOL_YEAR,
            'semester' => Enrollment::SEMESTER,
        ]);

        $this->actingAs($unassigned)->get('/teacher/subjects/'.$subject->id)->assertForbidden();
        $this->put('/teacher/grades/'.$enrollment->id, ['grade' => 96])->assertForbidden();
        $this->assertDatabaseCount('grades', 0);

        $this->actingAs($assigned)->get('/teacher/subjects')->assertOk()->assertSee('IS201');
        $this->get('/teacher/subjects/'.$subject->id)->assertOk()->assertSee('S-201');
        $this->put('/teacher/grades/'.$enrollment->id, ['grade' => 101])->assertSessionHasErrors('grade');
        $this->put('/teacher/grades/'.$enrollment->id, ['grade' => 92])->assertSessionHas('status');
        $this->put('/teacher/grades/'.$enrollment->id, ['grade' => 95])->assertSessionHas('status');
        $this->assertDatabaseCount('grades', 1);
        $this->assertDatabaseHas('grades', ['enrollment_id' => $enrollment->id, 'grade' => 95]);
        $this->actingAs($student)->get('/grades')->assertOk()->assertSee('95');
    }

    public function test_admin_can_manage_records_and_graded_enrollments_are_protected(): void
    {
        $admin = $this->staff('admin');
        $student = $this->student('S-301');
        $this->actingAs($student)->post('/admin/subjects', ['code' => 'IS301', 'name' => 'Lab'])->assertForbidden();

        $this->actingAs($admin)->post('/admin/teachers', [
            'name' => 'Casey Reyes', 'email' => 'CASEY@example.test', 'role' => 'admin',
            'password' => 'Teacher-123', 'password_confirmation' => 'Teacher-123',
        ])->assertSessionHas('status');
        $teacher = User::where('email', 'casey@example.test')->sole();
        $this->assertSame('teacher', $teacher->role);

        $this->post('/admin/subjects', ['code' => 'is301', 'name' => 'Lab', 'teacher_id' => 500])
            ->assertSessionHasErrors('teacher_id');
        $this->post('/admin/subjects', ['code' => 'is301', 'name' => 'Lab', 'teacher_id' => $teacher->teacher->id])
            ->assertSessionHas('status');
        $subject = Subject::where('code', 'IS301')->sole();
        $this->put('/admin/subjects/'.$subject->id, ['code' => 'IS301', 'name' => 'Integration Lab', 'teacher_id' => $teacher->teacher->id])
            ->assertSessionHas('status');
        $this->get('/admin/subjects')->assertOk()->assertSee('Integration Lab');
        $this->get('/admin/teachers')->assertOk()->assertSee('Casey Reyes');

        $this->put('/admin/students/'.$student->student->id, [
            'name' => 'Avery Rivera', 'email' => $student->email, 'student_number' => 'S-301', 'course' => 'Computer Science',
        ])->assertSessionHas('status');
        $this->get('/admin/students')->assertOk()->assertSee('Computer Science');

        $enrollment = $student->student->enrollments()->create([
            'subject_id' => $subject->id,
            'school_year' => Enrollment::SCHOOL_YEAR,
            'semester' => Enrollment::SEMESTER,
        ]);
        $this->get('/admin/enrollments')->assertOk()->assertSee('Integration Lab');
        $this->put('/admin/grades/'.$enrollment->id, ['grade' => 88])->assertSessionHas('status');
        $this->delete('/admin/enrollments/'.$enrollment->id)->assertSessionHasErrors('enrollment');
        $this->assertDatabaseHas('enrollments', ['id' => $enrollment->id]);
    }

    private function student(string $studentNumber): User
    {
        $user = User::factory()->create(['role' => 'student']);
        $user->student()->create(['student_number' => $studentNumber, 'course' => 'Information Systems']);

        return $user;
    }

    private function staff(string $role): User
    {
        $user = User::factory()->create(['role' => $role]);
        if ($role === 'teacher') {
            $user->teacher()->create();
        }

        return $user;
    }
}
