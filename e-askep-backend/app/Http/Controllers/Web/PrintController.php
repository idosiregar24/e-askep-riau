<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CareSession;
use Illuminate\Support\Facades\Auth;

class PrintController extends Controller
{
    public function show(string $uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $session = CareSession::with([
            'student',
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

        // Check view permission
        if (! $user->isAdmin() && $session->student_id !== $user->id && $session->mentor_dosen_id !== $user->id) {
            abort(403, 'Akses dokumen cetak ditolak.');
        }

        return view('print.case-session', compact('session'));
    }
}
