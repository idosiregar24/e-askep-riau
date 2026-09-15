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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

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

        return Inertia::render('Admin/Dashboard', [
            'totalMahasiswa'    => $totalMahasiswa,
            'totalDosen'        => $totalDosen,
            'totalCourses'      => $totalCourses,
            'totalCareSessions' => $totalCareSessions,
            'totalSdki'         => $totalSdki,
            'totalSpo'          => $totalSpo,
            'recentSessions'    => $recentSessions,
            'courses'           => $courses,
        ]);
    }

    // ==========================================
    // 1. USERS CRUD (Mahasiswa, Dosen, Admin)
    // ==========================================
    public function users(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nim_nip', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        return Inertia::render('Admin/Users', [
            'users'   => $users,
            'filters' => $request->only(['search', 'role', 'is_active']),
        ]);
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'nim_nip'      => ['required', 'string', 'max:50', 'unique:users,nim_nip'],
            'email'        => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role'         => ['required', 'in:admin,dosen,mahasiswa'],
            'password'     => ['required', 'string', 'min:6'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'is_active'    => ['nullable', 'boolean'],
        ]);

        User::create([
            'name'         => $validated['name'],
            'nim_nip'      => $validated['nim_nip'],
            'email'        => $validated['email'],
            'role'         => $validated['role'],
            'password'     => Hash::make($validated['password']),
            'phone_number' => $validated['phone_number'] ?? null,
            'is_active'    => $validated['is_active'] ?? true,
        ]);

        return back()->with('success', "Pengguna {$validated['name']} ({$validated['role']}) berhasil ditambahkan.");
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'nim_nip'      => ['required', 'string', 'max:50', Rule::unique('users', 'nim_nip')->ignore($user->id)],
            'email'        => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role'         => ['required', 'in:admin,dosen,mahasiswa'],
            'password'     => ['nullable', 'string', 'min:6'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'is_active'    => ['required', 'boolean'],
        ]);

        $userData = [
            'name'         => $validated['name'],
            'nim_nip'      => $validated['nim_nip'],
            'email'        => $validated['email'],
            'role'         => $validated['role'],
            'phone_number' => $validated['phone_number'] ?? null,
            'is_active'    => $validated['is_active'],
        ];

        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $user->update($userData);

        return back()->with('success', "Data pengguna {$user->name} berhasil diperbarui.");
    }

    public function destroyUser($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $name = $user->name;
        $user->delete();

        return back()->with('success', "Pengguna {$name} telah dihapus dari sistem.");
    }

    // ==========================================
    // 2. COURSES / STASE CRUD
    // ==========================================
    public function courses()
    {
        $courses = Course::withCount(['studentGroups', 'careSessions', 'spoProcedures'])->get();
        return Inertia::render('Admin/Courses', [
            'courses' => $courses,
        ]);
    }

    public function storeCourse(Request $request)
    {
        $validated = $request->validate([
            'code'          => ['required', 'string', 'max:50', 'unique:courses,code'],
            'name'          => ['required', 'string', 'max:255'],
            'program_study' => ['required', 'string', 'max:100'],
            'academic_year' => ['required', 'string', 'max:50'],
        ]);

        Course::create($validated);

        return back()->with('success', "Mata kuliah / stase {$validated['code']} berhasil ditambahkan.");
    }

    public function updateCourse(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $validated = $request->validate([
            'code'          => ['required', 'string', 'max:50', Rule::unique('courses', 'code')->ignore($course->id)],
            'name'          => ['required', 'string', 'max:255'],
            'program_study' => ['required', 'string', 'max:100'],
            'academic_year' => ['required', 'string', 'max:50'],
        ]);

        $course->update($validated);

        return back()->with('success', "Mata kuliah / stase {$course->code} berhasil diperbarui.");
    }

    public function destroyCourse($id)
    {
        $course = Course::findOrFail($id);
        $code = $course->code;
        $course->delete();

        return back()->with('success', "Mata kuliah / stase {$code} telah dihapus.");
    }

    // ==========================================
    // 3. STUDENT GROUPS CRUD
    // ==========================================
    public function groups()
    {
        $groups = StudentGroup::with(['course', 'mentor', 'student'])->paginate(15);
        $courses = Course::all();
        $dosens = User::where('role', 'dosen')->where('is_active', true)->get();
        $students = User::where('role', 'mahasiswa')->where('is_active', true)->get();

        return Inertia::render('Admin/Groups', [
            'groups'   => $groups,
            'courses'  => $courses,
            'dosens'   => $dosens,
            'students' => $students,
        ]);
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

    public function updateGroup(Request $request, $id)
    {
        $group = StudentGroup::findOrFail($id);

        $validated = $request->validate([
            'course_id'       => ['required', 'exists:courses,id'],
            'mentor_dosen_id' => ['required', 'exists:users,id'],
            'student_id'      => ['required', 'exists:users,id'],
            'group_name'      => ['required', 'string', 'max:100'],
        ]);

        $group->update($validated);

        return back()->with('success', 'Data kelompok praktik berhasil diperbarui.');
    }

    public function destroyGroup($id)
    {
        $group = StudentGroup::findOrFail($id);
        $group->delete();

        return back()->with('success', 'Kelompok bimbingan berhasil dihapus.');
    }

    // ==========================================
    // 4. MASTER 3S PPNI CRUD (SDKI, SLKI, SIKI)
    // ==========================================
    public function master3s(Request $request)
    {
        $search = $request->input('search');
        $tab = $request->input('tab', 'sdki');

        $sdkiQuery = MasterSdki::query();
        if ($search && $tab === 'sdki') {
            $sdkiQuery->where('code', 'like', "%{$search}%")
                      ->orWhere('title', 'like', "%{$search}%")
                      ->orWhere('category', 'like', "%{$search}%");
        }
        $sdkiList = $sdkiQuery->paginate(15, ['*'], 'sdki_page')->withQueryString();

        $slkiQuery = MasterSlki::query();
        if ($search && $tab === 'slki') {
            $slkiQuery->where('code', 'like', "%{$search}%")
                      ->orWhere('title', 'like', "%{$search}%");
        }
        $slkiList = $slkiQuery->paginate(15, ['*'], 'slki_page')->withQueryString();

        $sikiQuery = MasterSiki::query();
        if ($search && $tab === 'siki') {
            $sikiQuery->where('code', 'like', "%{$search}%")
                      ->orWhere('title', 'like', "%{$search}%");
        }
        $sikiList = $sikiQuery->paginate(15, ['*'], 'siki_page')->withQueryString();

        return Inertia::render('Admin/Master3s', [
            'sdkiList'  => $sdkiList,
            'slkiList'  => $slkiList,
            'sikiList'  => $sikiList,
            'sdkiCount' => MasterSdki::count(),
            'slkiCount' => MasterSlki::count(),
            'sikiCount' => MasterSiki::count(),
            'search'    => $search,
            'activeTab' => $tab,
        ]);
    }

    public function storeSdki(Request $request)
    {
        $validated = $request->validate([
            'code'         => ['required', 'string', 'max:20', 'unique:master_sdki,code'],
            'title'        => ['required', 'string', 'max:255'],
            'category'     => ['required', 'string', 'max:100'],
            'sub_category' => ['required', 'string', 'max:100'],
        ]);

        MasterSdki::create($validated);

        return back()->with('success', "Diagnosis SDKI {$validated['code']} berhasil ditambahkan.");
    }

    public function updateSdki(Request $request, $id)
    {
        $item = MasterSdki::findOrFail($id);

        $validated = $request->validate([
            'code'         => ['required', 'string', 'max:20', Rule::unique('master_sdki', 'code')->ignore($item->id)],
            'title'        => ['required', 'string', 'max:255'],
            'category'     => ['required', 'string', 'max:100'],
            'sub_category' => ['required', 'string', 'max:100'],
        ]);

        $item->update($validated);

        return back()->with('success', "Diagnosis SDKI {$item->code} berhasil diperbarui.");
    }

    public function destroySdki($id)
    {
        $item = MasterSdki::findOrFail($id);
        $code = $item->code;
        $item->delete();

        return back()->with('success', "Diagnosis SDKI {$code} telah dihapus.");
    }

    public function storeSlki(Request $request)
    {
        $validated = $request->validate([
            'code'  => ['required', 'string', 'max:20', 'unique:master_slki,code'],
            'title' => ['required', 'string', 'max:255'],
        ]);

        MasterSlki::create($validated);

        return back()->with('success', "Luaran SLKI {$validated['code']} berhasil ditambahkan.");
    }

    public function updateSlki(Request $request, $id)
    {
        $item = MasterSlki::findOrFail($id);

        $validated = $request->validate([
            'code'  => ['required', 'string', 'max:20', Rule::unique('master_slki', 'code')->ignore($item->id)],
            'title' => ['required', 'string', 'max:255'],
        ]);

        $item->update($validated);

        return back()->with('success', "Luaran SLKI {$item->code} berhasil diperbarui.");
    }

    public function destroySlki($id)
    {
        $item = MasterSlki::findOrFail($id);
        $code = $item->code;
        $item->delete();

        return back()->with('success', "Luaran SLKI {$code} telah dihapus.");
    }

    public function storeSiki(Request $request)
    {
        $validated = $request->validate([
            'code'  => ['required', 'string', 'max:20', 'unique:master_siki,code'],
            'title' => ['required', 'string', 'max:255'],
        ]);

        MasterSiki::create($validated);

        return back()->with('success', "Intervensi SIKI {$validated['code']} berhasil ditambahkan.");
    }

    public function updateSiki(Request $request, $id)
    {
        $item = MasterSiki::findOrFail($id);

        $validated = $request->validate([
            'code'  => ['required', 'string', 'max:20', Rule::unique('master_siki', 'code')->ignore($item->id)],
            'title' => ['required', 'string', 'max:255'],
        ]);

        $item->update($validated);

        return back()->with('success', "Intervensi SIKI {$item->code} berhasil diperbarui.");
    }

    public function destroySiki($id)
    {
        $item = MasterSiki::findOrFail($id);
        $code = $item->code;
        $item->delete();

        return back()->with('success', "Intervensi SIKI {$code} telah dihapus.");
    }

    // ==========================================
    // 5. MASTER SPO PROCEDURES CRUD
    // ==========================================
    public function spo(Request $request)
    {
        $query = MasterSpoProcedure::with('course');

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('procedure_name', 'like', "%{$search}%")
                  ->orWhere('domain_category', 'like', "%{$search}%")
                  ->orWhere('sub_cpmk_reference', 'like', "%{$search}%");
            });
        }

        $procedures = $query->paginate(15)->withQueryString();
        $courses = Course::all();

        return Inertia::render('Admin/Spo', [
            'procedures' => $procedures,
            'courses'    => $courses,
            'filters'    => $request->only(['search', 'course_id']),
        ]);
    }

    public function storeSpo(Request $request)
    {
        $validated = $request->validate([
            'course_id'           => ['required', 'exists:courses,id'],
            'sub_cpmk_reference'  => ['required', 'string', 'max:50'],
            'domain_category'     => ['required', 'string', 'max:100'],
            'procedure_name'      => ['required', 'string', 'max:255'],
        ]);

        MasterSpoProcedure::create($validated);

        return back()->with('success', "Prosedur SPO {$validated['procedure_name']} berhasil ditambahkan.");
    }

    public function updateSpo(Request $request, $id)
    {
        $item = MasterSpoProcedure::findOrFail($id);

        $validated = $request->validate([
            'course_id'           => ['required', 'exists:courses,id'],
            'sub_cpmk_reference'  => ['required', 'string', 'max:50'],
            'domain_category'     => ['required', 'string', 'max:100'],
            'procedure_name'      => ['required', 'string', 'max:255'],
        ]);

        $item->update($validated);

        return back()->with('success', "Prosedur SPO {$item->procedure_name} berhasil diperbarui.");
    }

    public function destroySpo($id)
    {
        $item = MasterSpoProcedure::findOrFail($id);
        $name = $item->procedure_name;
        $item->delete();

        return back()->with('success', "Prosedur SPO {$name} telah dihapus.");
    }
}
