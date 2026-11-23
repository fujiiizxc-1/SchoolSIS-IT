<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_persists_linked_student_and_login_uses_hashed_password(): void
    {
        $this->post('/register', $this->registration() + ['role' => 'admin'])
            ->assertRedirect('/login')->assertSessionHas('status');

        $user = User::where('email', 'student@example.test')->sole();
        $this->assertSame('student', $user->role);
        $this->assertTrue(Hash::check('Demo-student-123', $user->password));
        $this->assertDatabaseHas('students', ['user_id' => $user->id, 'student_number' => 'S-001', 'course' => 'Information Systems']);

        $this->post('/login', ['email' => $user->email, 'password' => 'incorrect'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->post('/login', ['email' => $user->email, 'password' => 'Demo-student-123'])->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
        $this->post('/logout')->assertRedirect('/login');
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_invalid_input_and_duplicate_identifiers_are_rejected(): void
    {
        $this->post('/register', ['email' => 'not-an-email', 'password' => 'short'])
            ->assertSessionHasErrors(['name', 'student_number', 'course', 'email', 'password']);
        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('students', 0);

        $this->post('/register', $this->registration())->assertRedirect('/login');
        $this->post('/register', array_replace($this->registration(), ['student_number' => 's-001', 'email' => 'STUDENT@example.test']))
            ->assertSessionHasErrors(['student_number', 'email']);

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('students', 1);
    }

    public function test_a_database_conflict_rolls_back_both_registration_records(): void
    {
        $this->post('/register', $this->registration())->assertRedirect('/login');

        Student::creating(function (Student $student) {
            $student->student_number = 'S-001';
        });

        $this->post('/register', array_replace($this->registration(), [
            'student_number' => 'S-002',
            'email' => 'another@example.test',
        ]))->assertSessionHasErrors('student_number')->assertSessionMissing('status');

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('students', 1);
    }

    private function registration(): array
    {
        return [
            'name' => 'Avery Rivera',
            'student_number' => 's-001',
            'course' => 'Information Systems',
            'email' => 'student@example.test',
            'password' => 'Demo-student-123',
            'password_confirmation' => 'Demo-student-123',
        ];
    }
}
