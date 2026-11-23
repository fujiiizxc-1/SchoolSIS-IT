<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class GradingController extends Controller
{
    public function subjects(Request $request): View
    {
        return view('teacher.subjects', [
            'subjects' => $request->user()->teacher()->firstOrFail()->subjects()->orderBy('code')->paginate(15),
        ]);
    }

    public function subject(Request $request, Subject $subject): View
    {
        abort_unless($subject->teacher_id === $request->user()->teacher()->firstOrFail()->id, 403);

        return view('teacher.subject', [
            'subject' => $subject,
            'enrollments' => $subject->enrollments()->with(['student.user', 'grade'])->latest('id')->paginate(15),
        ]);
    }

    public function update(Request $request, Enrollment $enrollment): RedirectResponse
    {
        $teacherId = $request->user()->role === 'teacher' ? $request->user()->teacher()->firstOrFail()->id : null;

        DB::transaction(function () use ($request, $enrollment, $teacherId) {
            $record = Enrollment::lockForUpdate()->findOrFail($enrollment->id);
            if ($teacherId !== null) {
                $subject = $record->subject()->lockForUpdate()->firstOrFail();
                abort_unless($subject->teacher_id === $teacherId, 403);
            }

            $data = $request->validate(['grade' => ['required', 'integer', 'between:0,100']]);
            $record->grade()->updateOrCreate([], $data);
        });

        return back()->with('status', 'Grade saved.');
    }
}
