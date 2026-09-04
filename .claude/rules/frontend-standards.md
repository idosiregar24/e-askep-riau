# Frontend Standards — Clinical Clean Minimalism (Shadcn UI Style)
## e-Askep Poltekkes Kemenkes Riau

> **Filosofi Visual**: Antarmuka klinis modern mengadopsi tema **Clinical Clean Minimalism** berbasis filosofi **Shadcn UI**. Mengutamakan keterbacaan data medis yang jernih dengan kanvas putih bersih (*pure white*), struktur kartu mengambang (*floating card frame*), dan aksen warna resmi **Poltekkes Kemenkes Riau**.

---

## 1. Palet Warna Resmi Poltekkes Riau & Semantik Medis

Seluruh komponen pada Web (Blade + Livewire/Alpine + Tailwind) dan Mobile (Flutter) wajib menggunakan token warna berikut:

### 1.1 Identitas Utama Institusi
* **Primary / Brand (Hijau Toska Kemenkes)**:
  * Default: `#008D88`
  * Hover: `#00736F`
  * Light Tint / Active Pill: `#E6F5F4`
  * Digunakan untuk: tombol aksi utama (*Save*, *Submit*, *E-Paraf*), menu navigasi aktif, badge stase.
* **Secondary / Accent (Kuning Emas Poltekkes)**:
  * Default: `#EAB308`
  * Deep Gold: `#CA8A04`
  * Soft Yellow Pill: `#FEF9C3`
  * Digunakan untuk: skor rubrik Sub-CPMK, badge status *Need Revision*, catatan instruktur.

### 1.2 Netral & Kanvas Kerja
* **Base Canvas**: `#F1F5F9` (Slate-100 lembut — memberikan kontras kartu mengambang).
* **Main Card / Surface**: `#FFFFFF` (Pure White, tanpa gradasi gelap).
* **Sidebar Surface**: `#FAFAFA` / `#F8FAFC` (Slate-50 dengan batas tipis `border-r border-slate-200`).
* **Tipografi**:
  * Heading & Nilai Utama: `#0F172A` (Slate-900).
  * Subjudul & Label Deskriptif: `#64748B` (Slate-500).
  * Teks Menu Sidebar: `#475569` (Slate-600).
  * Teks Menu Aktif: `#008D88` di atas background `#E6F5F4`.

### 1.3 Semantik Triase Kegawatdaruratan (KGD)
Wajib mengikuti kaidah triase standar medis:
* **Merah (Emergent)**: Teks `#DC2626`, Latar `#FEF2F2`, Border `#FECACA`
* **Kuning (Urgent)**: Teks `#D97706`, Latar `#FFFBEB`, Border `#FDE68A`
* **Hijau (Non-Urgent)**: Teks `#16A34A`, Latar `#F0FDF4`, Border `#BBF7D0`
* **Hitam (Meninggal)**: Teks `#1E293B`, Latar `#F1F5F9`, Border `#CBD5E1`

---

## 2. Standar Komponen Web (Blade + Tailwind CSS)

### 2.1 Floating Card Frame (Komponen Kartu Standar)
```html
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden transition-all duration-200">
    <!-- Header Card -->
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-2.5 h-2.5 rounded-full bg-[#008D88]"></div>
            <h3 class="text-base font-semibold text-slate-900">Survei Primer (ABCDE)</h3>
        </div>
        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-[#E6F5F4] text-[#008D88]">
            Stase KGD
        </span>
    </div>
    <!-- Konten Card -->
    <div class="p-6">
        <!-- Form Field / Data -->
    </div>
</div>
```

### 2.2 Left Sidebar Modular (Shadcn Style)
* Sidebar menggunakan tata letak tetap dengan item menu berkapsul halus (*rounded-full pill*).
* Item aktif: `bg-[#E6F5F4] text-[#008D88] font-medium`.
* Item non-aktif: `text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 transition-colors`.

### 2.3 Status Chips & Action Buttons
* **Tombol Utama (Brand Button)**:
  ```html
  <button class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-[#008D88] hover:bg-[#00736F] rounded-lg shadow-xs transition-colors focus:ring-2 focus:ring-[#008D88]/30">
      Simpan Pengkajian
  </button>
  ```
* **Status Dokumen Chips**:
  * Draft: `bg-slate-100 text-slate-700 border border-slate-200`
  * Submitted: `bg-sky-50 text-sky-700 border border-sky-200`
  * Need Revision: `bg-[#FEF9C3] text-[#CA8A04] border border-[#FDE68A]`
  * Approved & Graded: `bg-emerald-50 text-emerald-700 border border-emerald-200`
  * Archived: `bg-slate-200 text-slate-800 border border-slate-300`

---

## 3. Standar Multi-Step Clinical Form Wizard

Pengkajian klinis (KGD, KDM, KMB) terdiri dari formulir panjang yang wajib dibagi menjadi langkah bertahap (*multi-step wizard*):
1. **Step Indicator**: Menampilkan nomor tahapan, nama modul pengkajian, dan status validitas (*valid/invalid/draft*).
2. **Auto-Save Indicator**: Memberikan umpan balik visual (*"Draf tersimpan secara lokal pukul 10:45"*).
3. **Form Sectioning**: Setiap kelompok data (misal: Airway, Breathing, Circulation) dikelompokkan ke dalam kartu individual.

---

## 4. Standar UI Mobile Client (Flutter)

1. **Theme Data**:
   * Warna `colorScheme.primary`: `Color(0xFF008D88)`
   * Warna `colorScheme.secondary`: `Color(0xFFEAB308)`
   * Scaffold background: `Color(0xFFF1F5F9)`
   * Card background: `Colors.white`
2. **Card Elevation & Border**:
   * Sudut melengkung `BorderRadius.circular(16)`
   * Garis tepi halus `Border.all(color: Color(0xFFE2E8F0))`
   * Elevation rendah `elevation: 0` atau `elevation: 1`
3. **Ergonomi Bangsal Klinis**:
   * Ukuran touch-target tombol tindakan checklist SPO minimal `48x48 dp`.
   * Komponen tanda tangan digital menggunakan kanvas transparan bergaris putus-putus panduan.
   * Banner status koneksi (*Offline Mode: Tersimpan di Memori Lokal*).
