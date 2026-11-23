<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class StudentPortalController extends Controller
{
    public function profile(Request $request): View
    {
        return view('student.profile', ['student' => $request->user()->student()->with('user')->firstOrFail()]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:100']]);
        $request->user()->update($data);

        return to_route('profile')->with('status', 'Your profile was saved.');
    }

    public function subjects(Request $request): View
    {
        $enrolled = $request->user()->student()->firstOrFail()->enrollments()
            ->where('school_year', Enrollment::SCHOOL_YEAR)
            ->where('semester', Enrollment::SEMESTER)
            ->select('subject_id');

        return view('student.subjects', [
            'subjects' => Subject::with('teacher.user')->whereNotIn('id', $enrolled)->orderBy('code')->paginate(15),
            'schoolYear' => Enrollment::SCHOOL_YEAR,
            'semester' => Enrollment::SEMESTER,
        ]);
    }

    public function enroll(Request $request): RedirectResponse
    {
        $data = $request->validate(['subject_id' => ['required', 'integer', 'exists:subjects,id']]);
        $enrollment = $request->user()->student()->firstOrFail()->enrollments()->firstOrCreate([
            'subject_id' => $data['subject_id'],
            'school_year' => Enrollment::SCHOOL_YEAR,
            'semester' => Enrollment::SEMESTER,
        ]);

        if (! $enrollment->wasRecentlyCreated) {
            throw ValidationException::withMessages(['subject_id' => 'You are already enrolled in this subject for this term.']);
        }

        return to_route('enrollments')->with('status', 'You are enrolled in the subject.');
    }

    public function enrollments(Request $request): View
    {
        return view('student.enrollments', [
            'enrollments' => $request->user()->student()->firstOrFail()->enrollments()
                ->with(['subject', 'grade'])->latest('id')->paginate(15),
        ]);
    }

    public function grades(Request $request): View
    {
        return view('student.grades', [
            'enrollments' => $request->user()->student()->firstOrFail()->enrollments()
                ->with(['subject', 'grade'])->latest('id')->paginate(15),
        ]);
    }
}
