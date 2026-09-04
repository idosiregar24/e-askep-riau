<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CareSession;
use App\Models\Course;
use App\Models\StudentGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DosenDashboardController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Query base: cases supervised by this lecturer (or all if admin viewing)
        $query = CareSession::with(['student', 'course', 'review'])
            ->where('mentor_dosen_id', $user->id);

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter course
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        // Search patient or student name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('patient_name', 'like', "%{$search}%")
                  ->orWhere('medical_record_no', 'like', "%{$search}%")
                  ->orWhereHas('student', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%")
                        ->orWhere('nim_nip', 'like', "%{$search}%");
                  });
            });
        }

        $sessions = $query->latest('updated_at')->paginate(10)->withQueryString();

        // Metrics
        $totalSubmitted = CareSession::where('mentor_dosen_id', $user->id)
            ->where('status', 'submitted')
            ->count();

        $totalNeedRevision = CareSession::where('mentor_dosen_id', $user->id)
            ->where('status', 'need_revision')
            ->count();

        $totalApproved = CareSession::where('mentor_dosen_id', $user->id)
            ->where('status', 'approved_graded')
            ->count();

        $totalStudents = StudentGroup::where('mentor_dosen_id', $user->id)
            ->distinct('student_id')
            ->count('student_id');

        $courses = Course::all();

        return view('dosen.dashboard', compact(
            'sessions',
            'totalSubmitted',
            'totalNeedRevision',
            'totalApproved',
            'totalStudents',
            'courses'
        ));
    }
}
