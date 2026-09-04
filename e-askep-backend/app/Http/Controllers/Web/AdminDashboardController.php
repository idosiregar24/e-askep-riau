<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CareSession;
use App\Models\Course;
use App\Models\MasterSdki;
use App\Models\MasterSiki;
use App\Models\MasterSlki;
use App\Models\MasterSpoProcedure;
use App\Models\StudentGroup;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalMahasiswa = User::where('role', 'mahasiswa')->count();
        $totalDosen = User::where('role', 'dosen')->count();
        $totalCourses = Course::count();
        $totalCareSessions = CareSession::count();
        $totalSdki = MasterSdki::count();
        $totalSpo = MasterSpoProcedure::count();

        $recentSessions = CareSession::with(['student', 'course', 'mentor'])
            ->latest()
            ->take(8)
            ->get();

        $courses = Course::withCount('careSessions')->get();

        return view('admin.dashboard', compact(
            'totalMahasiswa',
            'totalDosen',
            'totalCourses',
            'totalCareSessions',
            'totalSdki',
            'totalSpo',
            'recentSessions',
            'courses'
        ));
    }

    public function courses()
    {
        $courses = Course::withCount(['studentGroups', 'careSessions', 'spoProcedures'])->get();
        return view('admin.courses', compact('courses'));
    }

    public function groups()
    {
        $groups = StudentGroup::with(['course', 'mentor', 'student'])->paginate(15);
        $courses = Course::all();
        $dosens = User::where('role', 'dosen')->where('is_active', true)->get();
        $students = User::where('role', 'mahasiswa')->where('is_active', true)->get();

        return view('admin.groups', compact('groups', 'courses', 'dosens', 'students'));
    }

    public function storeGroup(Request $request)
    {
        $validated = $request->validate([
            'course_id'       => ['required', 'exists:courses,id'],
            'mentor_dosen_id' => ['required', 'exists:users,id'],
            'student_id'      => ['required', 'exists:users,id'],
            'group_name'      => ['required', 'string', 'max:100'],
        ]);

        StudentGroup::create($validated);

        return back()->with('success', 'Penugasan kelompok praktik berhasil disimpan.');
    }

    public function master3s(Request $request)
    {
        $search = $request->input('search');
        $query = MasterSdki::query();

        if ($search) {
            $query->where('code', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
        }

        $sdkiList = $query->paginate(15)->withQueryString();
        $slkiCount = MasterSlki::count();
        $sikiCount = MasterSiki::count();

        return view('admin.master-3s', compact('sdkiList', 'slkiCount', 'sikiCount', 'search'));
    }
}
