<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SchoolAdminController extends Controller
{
    public function students(): View
    {
        return view('admin.students', ['students' => Student::with('user')->latest('id')->paginate(15)]);
    }

    public function updateStudent(Request $request, Student $student): RedirectResponse
    {
        if (is_string($request->input('student_number'))) {
            $request->merge(['student_number' => $request->string('student_number')->upper()->toString()]);
        }

        $profile = $request->validate([
            'student_number' => ['required', 'string', 'max:30', Rule::unique('students')->ignore($student)],
            'course' => ['required', 'string', 'max:120'],
        ], [], ['student_number' => 'student ID']);
        $account = $this->accountData($request, $student->user_id);

        DB::transaction(function () use ($student, $profile, $account) {
            $student->update($profile);
            $student->user()->update($account);
        });

        return back()->with('status', 'Student record saved.');
    }

    public function subjects(): View
    {
        return view('admin.subjects', [
            'subjects' => Subject::with('teacher.user')->orderBy('code')->paginate(15),
            'teachers' => Teacher::with('user')->get(),
        ]);
    }

    public function storeSubject(Request $request): RedirectResponse
    {
        Subject::create($this->subjectData($request, new Subject));

        return back()->with('status', 'Subject created.');
    }

    public function updateSubject(Request $request, Subject $subject): RedirectResponse
    {
        $subject->update($this->subjectData($request, $subject));

        return back()->with('status', 'Subject saved.');
    }

    public function enrollments(): View
    {
        return view('admin.enrollments', [
            'enrollments' => Enrollment::with(['student.user', 'subject', 'grade'])->latest('id')->paginate(15),
        ]);
    }

    public function destroyEnrollment(Enrollment $enrollment): RedirectResponse
    {
        DB::transaction(function () use ($enrollment) {
            $record = Enrollment::lockForUpdate()->findOrFail($enrollment->id);
            if ($record->grade()->exists()) {
                throw ValidationException::withMessages(['enrollment' => 'This enrollment has a grade and cannot be removed.']);
            }
            $record->delete();
        });

        return back()->with('status', 'Enrollment removed.');
    }

    public function teachers(): View
    {
        return view('admin.teachers', ['teachers' => Teacher::with('user')->latest('id')->paginate(15)]);
    }

    public function storeTeacher(Request $request): RedirectResponse
    {
        $account = $this->accountData($request);
        $password = $request->validate([
            'password' => ['bail', 'required', 'string', 'min:8', 'confirmed', AccountController::validatePassword(...)],
        ]);

        DB::transaction(function () use ($account, $password) {
            $user = new User($account + $password);
            $user->role = 'teacher';
            $user->save();
            $user->teacher()->create();
        });

        return back()->with('status', 'Teacher account created.');
    }

    public function updateTeacher(Request $request, Teacher $teacher): RedirectResponse
    {
        $teacher->user()->firstOrFail()->update($this->accountData($request, $teacher->user_id));

        return back()->with('status', 'Teacher account saved.');
    }

    private function subjectData(Request $request, Subject $subject): array
    {
        if (is_string($request->input('code'))) {
            $request->merge(['code' => $request->string('code')->upper()->toString()]);
        }

        return $request->validate([
            'code' => ['required', 'string', 'max:24', Rule::unique('subjects')->ignore($subject)],
            'name' => ['required', 'string', 'max:120'],
            'teacher_id' => ['nullable', 'integer', 'exists:teachers,id'],
        ]);
    }

    private function accountData(Request $request, ?int $userId = null): array
    {
        if (is_string($request->input('email'))) {
            $request->merge(['email' => $request->string('email')->lower()->toString()]);
        }

        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:254', Rule::unique('users')->ignore($userId)],
        ]);
    }
}
