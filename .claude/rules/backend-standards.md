# Backend Standards — Laravel 11 & REST API Engine
## e-Askep Poltekkes Kemenkes Riau

---

## 1. Arsitektur Controller & Route

### 1.1 Web Controller (Admin & Dosen/CI)
* Web Controller melayani antarmuka browser untuk Admin Prodi dan Meja Telaah Dosen/CI.
* Menggunakan Blade + Livewire v3 / Alpine.js dengan return type eksplisit `View` atau `RedirectResponse`.

```php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(Request $request): View
    {
        $courses = Course::withCount(['studentGroups', 'careSessions'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', "%{$request->search}%")
                      ->orWhere('code', 'like', "%{$request->search}%");
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.courses.index', compact('courses'));
    }
}
```

### 1.2 REST API Controller (Mobile Flutter Client)
* Seluruh endpoint API mobile berada di bawah namespace `App\Http\Controllers\Api\V1\` dengan prefix URL `/api/v1/`.
* Seluruh response wajib berformat JSON standar yang seragam via `ApiResponse` trait atau helper:

```json
{
  "success": true,
  "message": "Data berhasil dimuat.",
  "data": { ... },
  "meta": { "page": 1, "total": 45 }
}
```

Format Error:
```json
{
  "success": false,
  "message": "Validasi gagal.",
  "errors": {
    "triage_category": ["Kategori triase wajib dipilih."]
  }
}
```

Contoh Implementasi API Controller:
```php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateDraftRequest;
use App\Http\Resources\CareSessionResource;
use App\Models\CareSession;
use Illuminate\Http\JsonResponse;

class CareSessionController extends Controller
{
    public function updateDraft(UpdateDraftRequest $request, CareSession $session): JsonResponse
    {
        $this->authorize('update', $session);

        $session->updateDraftAssessment(
            $request->validated('assessment_payload'),
            $request->validated('procedures', [])
        );

        return response()->json([
            'success' => true,
            'message' => 'Draf pengkajian klinis berhasil disimpan.',
            'data'    => new CareSessionResource($session->fresh(['assessment', 'procedureLogs'])),
        ]);
    }
}
```

---

## 2. FormRequest & Validasi Data Klinis

* Setiap form input wajib memiliki FormRequest tersendiri di `App\Http\Requests\`.
* Validasi mencakup format data klinis, skala skor rubrik 0–100, dan kategori triase resmi:

```php
namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCareSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('mahasiswa');
    }

    public function rules(): array
    {
        return [
            'course_id'          => ['required', 'exists:courses,id'],
            'mentor_dosen_id'    => ['required', 'exists:users,id'],
            'patient_name'       => ['required', 'string', 'max:255'],
            'medical_record_no'  => ['nullable', 'string', 'max:100'],
            'age'                => ['required', 'string', 'max:50'],
            'gender'             => ['required', Rule::in(['L', 'P'])],
            'triage_category'    => ['nullable', Rule::in(['merah', 'kuning', 'hijau', 'hitam'])],
        ];
    }

    public function messages(): array
    {
        return [
            'patient_name.required'    => 'Nama pasien wajib diisi.',
            'mentor_dosen_id.required' => 'Dosen pembimbing wajib dipilih.',
            'triage_category.in'       => 'Kategori triase harus merah, kuning, hijau, atau hitam.',
        ];
    }
}
```

---

## 3. Otorisasi & Peran Pengguna (`spatie/laravel-permission`)

* Sistem memiliki 3 peran utama: `admin`, `dosen`, `mahasiswa`.
* Seluruh operasi pengkajian dilindungi oleh **Laravel Policy**:
  * `Mahasiswa`: Hanya dapat menyunting berkas miliknya yang berstatus `draft` atau `need_revision`.
  * `Dosen / CI`: Hanya dapat menyetujui, meminta revisi, atau menilai berkas mahasiswa dalam kelompok bimbingannya.
  * `Admin`: Memiliki akses penuh ke master kurikulum, 3S, SPO, dan rekap nilai.

```php
namespace App\Policies;

use App\Models\CareSession;
use App\Models\User;

class CareSessionPolicy
{
    public function update(User $user, CareSession $session): bool
    {
        return $user->id === $session->student_id 
            && in_array($session->status, ['draft', 'need_revision']);
    }

    public function grade(User $user, CareSession $session): bool
    {
        return $user->hasRole('dosen') 
            && $user->id === $session->mentor_dosen_id 
            && $session->status === 'submitted';
    }
}
```

---

## 4. Penanganan Payload JSON Klinis pada Model

Gunakan `$casts` native Laravel untuk kolom bertipe JSON:

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CareSessionAssessment extends Model
{
    protected $fillable = [
        'care_session_id',
        'stage_type',
        'assessment_payload',
    ];

    protected $casts = [
        'assessment_payload' => 'array',
    ];
}
```

---

## 5. Generator Dokumen PDF Resmi (`barryvdh/laravel-snappy`)

* Menggunakan wrapper Snappy untuk binary `wkhtmltopdf`.
* Output PDF wajib mencerminkan tata letak fisik 1:1 format baku Poltekkes Kemenkes Riau.
* Konfigurasi Snappy di `config/snappy.php`:

```php
'pdf' => [
    'enabled' => true,
    'binary'  => env('WKHTMLTOPDF_BINARY', 'C:/laragon/bin/wkhtmltopdf/bin/wkhtmltopdf.exe'),
    'timeout' => 60,
    'options' => [
        'page-size'     => 'A4',
        'margin-top'    => '15mm',
        'margin-right'  => '15mm',
        'margin-bottom' => '15mm',
        'margin-left'   => '15mm',
        'enable-local-file-access' => true,
    ],
],
```

---

## 6. Checklist Kepatuhan Kode Backend

```
[ ] Seluruh endpoint API baru didokumentasikan dan menggunakan ApiResource
[ ] Tidak ada data pasien sensitif yang diekspos tanpa penyamaran jika tidak diperlukan
[ ] Query database terbebas dari masalah N+1 (selalu gunakan with() atau load())
[ ] Seluruh perubahan status berkas divalidasi transisinya (state machine check)
[ ] Transaksi e-paraf batch dan penilaian rubrik dibungkus dalam DB::transaction()
```
