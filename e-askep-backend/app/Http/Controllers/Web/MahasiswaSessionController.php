<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CareProcedureLog;
use App\Models\CareSession;
use App\Models\CareSessionAssessment;
use App\Models\Course;
use App\Models\EvaluationAndHandover;
use App\Models\MasterSdki;
use App\Models\MasterSiki;
use App\Models\MasterSlki;
use App\Models\MasterSpoProcedure;
use App\Models\NursingCarePlan;
use App\Models\StudentGroup;
use App\Models\User;
use App\Models\VitalSignMonitoring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MahasiswaSessionController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $query = CareSession::with(['course', 'mentor', 'review'])
            ->where('student_id', $user->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        $sessions = $query->latest()->paginate(10)->withQueryString();

        $totalDraft = CareSession::where('student_id', $user->id)->where('status', 'draft')->count();
        $totalSubmitted = CareSession::where('student_id', $user->id)->where('status', 'submitted')->count();
        $totalNeedRevision = CareSession::where('student_id', $user->id)->where('status', 'need_revision')->count();
        $totalApproved = CareSession::where('student_id', $user->id)->where('status', 'approved_graded')->count();

        $courses = Course::all();

        return view('mahasiswa.dashboard', compact(
            'sessions',
            'totalDraft',
            'totalSubmitted',
            'totalNeedRevision',
            'totalApproved',
            'courses'
        ));
    }

    public function create()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Get courses and find assigned mentor if any
        $courses = Course::all();
        $dosens = User::where('role', 'dosen')->where('is_active', true)->get();

        // Check if student already has a mentor assigned in student_groups
        $assignedGroup = StudentGroup::where('student_id', $user->id)->first();
        $defaultDosenId = $assignedGroup?->mentor_dosen_id ?? ($dosens->first()?->id ?? null);
        $defaultCourseId = $assignedGroup?->course_id ?? ($courses->first()?->id ?? null);

        return view('mahasiswa.create', compact('courses', 'dosens', 'defaultDosenId', 'defaultCourseId'));
    }

    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'course_id'         => ['required', 'exists:courses,id'],
            'mentor_dosen_id'   => ['required', 'exists:users,id'],
            'patient_name'      => ['required', 'string', 'max:255'],
            'medical_record_no' => ['required', 'string', 'max:50'],
            'age'               => ['required', 'integer', 'min:0', 'max:150'],
            'gender'            => ['required', 'in:L,P'],
            'triage_category'   => ['nullable', 'in:merah,kuning,hijau,hitam'],
        ]);

        $session = CareSession::create([
            'uuid'              => (string) Str::uuid(),
            'student_id'        => $user->id,
            'course_id'         => $validated['course_id'],
            'mentor_dosen_id'   => $validated['mentor_dosen_id'],
            'patient_name'      => $validated['patient_name'],
            'medical_record_no' => $validated['medical_record_no'],
            'age'               => $validated['age'],
            'gender'            => $validated['gender'],
            'triage_category'   => $validated['triage_category'] ?? 'hijau',
            'status'            => 'draft',
        ]);

        // Automatically initialize empty clinical assessment payload based on course code
        $course = Course::find($validated['course_id']);
        $assessmentType = 'kdm';
        if ($course && str_contains(strtoupper($course->code), 'WAT5.31')) {
            $assessmentType = 'kgd';
        } elseif ($course && str_contains(strtoupper($course->code), 'WAT5.24')) {
            $assessmentType = 'kmb';
        }

        CareSessionAssessment::create([
            'care_session_id'    => $session->id,
            'stage_type'         => $assessmentType,
            'assessment_payload' => [
                'keluhan_utama'   => 'Pasien mengeluhkan keluhan saat masuk ke ruangan.',
                'riwayat_penyakit' => 'Riwayat penyakit saat ini dan terdahulu.',
                'tanda_vital_awal' => ['td' => '120/80', 'nadi' => '80', 'rr' => '20', 'suhu' => '36.8', 'spo2' => '98'],
            ],
        ]);

        // Pre-populate SPO procedure logs from master
        $spos = MasterSpoProcedure::where('course_id', $validated['course_id'])->get();
        foreach ($spos as $spo) {
            CareProcedureLog::create([
                'care_session_id' => $session->id,
                'procedure_id'    => $spo->id,
                'is_performed'    => false,
            ]);
        }

        return redirect()->route('mahasiswa.show', $session->uuid)
            ->with('success', 'Kasus pasien berhasil dibuat. Silakan lengkapi pengkajian klinis dan rencana 3S.');
    }

    public function show(string $uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $session = CareSession::with([
            'course',
            'mentor',
            'assessment',
            'carePlans.sdki',
            'carePlans.slki',
            'carePlans.siki',
            'procedureLogs.procedure',
            'vitalSigns',
            'evaluationsAndHandovers',
            'review.dosen',
        ])->where('uuid', $uuid)->firstOrFail();

        // Check ownership
        if (! $user->isAdmin() && $session->student_id !== $user->id) {
            abort(403, 'Akses ditolak.');
        }

        $masterSdki = MasterSdki::all();
        $masterSlki = MasterSlki::all();
        $masterSiki = MasterSiki::all();

        return view('mahasiswa.show', compact('session', 'masterSdki', 'masterSlki', 'masterSiki'));
    }

    public function updateAssessment(Request $request, string $uuid)
    {
        $session = CareSession::where('uuid', $uuid)->firstOrFail();
        $this->ensureEditable($session);

        $validated = $request->validate([
            'keluhan_utama'   => ['required', 'string'],
            'riwayat_penyakit' => ['nullable', 'string'],
            'airway'          => ['nullable', 'string'],
            'breathing'       => ['nullable', 'string'],
            'circulation'     => ['nullable', 'string'],
            'disability'      => ['nullable', 'string'],
            'exposure'        => ['nullable', 'string'],
            'catatan_tambahan'=> ['nullable', 'string'],
        ]);

        $assessment = CareSessionAssessment::firstOrNew(['care_session_id' => $session->id]);
        $existing = $assessment->assessment_payload ?? [];
        $assessment->assessment_payload = array_merge($existing, $validated);
        $assessment->save();

        return back()->with('success', 'Pengkajian klinis berhasil diperbarui.');
    }

    public function storeCarePlan(Request $request, string $uuid)
    {
        $session = CareSession::where('uuid', $uuid)->firstOrFail();
        $this->ensureEditable($session);

        $validated = $request->validate([
            'master_sdki_id' => ['required', 'exists:master_sdki,id'],
            'master_slki_id' => ['required', 'exists:master_slki,id'],
            'master_siki_id' => ['required', 'exists:master_siki,id'],
            'subjective_data'=> ['nullable', 'string'],
            'objective_data' => ['nullable', 'string'],
            'priority_order' => ['required', 'integer', 'min:1'],
        ]);

        NursingCarePlan::create([
            'care_session_id' => $session->id,
            'sdki_id'         => $validated['master_sdki_id'],
            'slki_id'         => $validated['master_slki_id'],
            'siki_id'         => $validated['master_siki_id'],
            'subjective_data' => $validated['subjective_data'],
            'objective_data'  => $validated['objective_data'],
            'etiology'        => 'Faktor risiko klinis / proses patofisiologis penyakit.',
            'priority_order'  => $validated['priority_order'],
        ]);

        return back()->with('success', 'Rencana Asuhan 3S PPNI berhasil ditambahkan.');
    }

    public function destroyCarePlan(string $uuid, int $planId)
    {
        $session = CareSession::where('uuid', $uuid)->firstOrFail();
        $this->ensureEditable($session);

        NursingCarePlan::where('care_session_id', $session->id)->where('id', $planId)->delete();

        return back()->with('success', 'Rencana asuhan berhasil dihapus.');
    }

    public function toggleProcedure(Request $request, string $uuid, int $logId)
    {
        $session = CareSession::where('uuid', $uuid)->firstOrFail();
        $this->ensureEditable($session);

        $log = CareProcedureLog::where('care_session_id', $session->id)->where('id', $logId)->firstOrFail();

        $newState = ! $log->is_performed;
        $log->update([
            'is_performed' => $newState,
            'performed_at' => $newState ? now() : null,
        ]);

        return back()->with('success', 'Status checklist tindakan SPO berhasil diperbarui.');
    }

    public function storeVitalSign(Request $request, string $uuid)
    {
        $session = CareSession::where('uuid', $uuid)->firstOrFail();
        $this->ensureEditable($session);

        $validated = $request->validate([
            'blood_pressure'    => ['required', 'string'],
            'heart_rate'        => ['required', 'integer'],
            'respiratory_rate'  => ['required', 'integer'],
            'temperature'       => ['required', 'numeric'],
            'oxygen_saturation' => ['nullable', 'integer'],
            'gcs_score'         => ['nullable', 'string'],
        ]);

        VitalSignMonitoring::create([
            'care_session_id'   => $session->id,
            'recorded_at'       => now(),
            'blood_pressure'    => $validated['blood_pressure'],
            'heart_rate'        => $validated['heart_rate'],
            'respiratory_rate'  => $validated['respiratory_rate'],
            'temperature'       => $validated['temperature'],
            'oxygen_saturation' => $validated['oxygen_saturation'],
            'gcs_score'         => $validated['gcs_score'],
        ]);

        return back()->with('success', 'Pemantauan tanda-tanda vital berhasil disimpan.');
    }

    public function storeHandover(Request $request, string $uuid)
    {
        $session = CareSession::where('uuid', $uuid)->firstOrFail();
        $this->ensureEditable($session);

        $validated = $request->validate([
            'format_type' => ['required', 'in:SBAR,SOAP'],
            'situation'   => ['nullable', 'string'],
            'background'  => ['nullable', 'string'],
            'assessment'  => ['nullable', 'string'],
            'recommendation' => ['nullable', 'string'],
        ]);

        $payload = [
            'situation'      => $validated['situation'] ?? '',
            'background'     => $validated['background'] ?? '',
            'assessment'     => $validated['assessment'] ?? '',
            'recommendation' => $validated['recommendation'] ?? '',
        ];

        EvaluationAndHandover::create([
            'care_session_id' => $session->id,
            'format_type'     => $validated['format_type'],
            'payload'         => $payload,
        ]);

        return back()->with('success', "Catatan {$validated['format_type']} berhasil disimpan.");
    }

    public function submit(string $uuid)
    {
        $session = CareSession::where('uuid', $uuid)->firstOrFail();
        $this->ensureEditable($session);

        $session->update([
            'status'       => 'submitted',
            'submitted_at' => now(),
        ]);

        return redirect()->route('mahasiswa.dashboard')
            ->with('success', "Berkas kasus pasien {$session->patient_name} telah diajukan ke Dosen Pembimbing untuk ditelaah.");
    }

    private function ensureEditable(CareSession $session)
    {
        if ($session->status === 'approved_graded') {
            abort(403, 'Berkas telah disetujui dan dinilai permanen oleh Dosen Pembimbing (Immutable).');
        }
        if ($session->status === 'submitted') {
            abort(403, 'Berkas sedang dalam antrean telaah Dosen Pembimbing.');
        }
    }
}
