<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed akun-akun default (Admin, Dosen, Mahasiswa).
     */
    public function run(): void
    {
        // 1. SEED PENGGUNA DEFAULT (AKUN)
        // Catatan: menggunakan updateOrCreate (bukan firstOrCreate) secara sengaja agar
        // kredensial default ini selalu "self-healing" ke nilai yang benar setiap kali
        // seeder dijalankan ulang, meskipun password di database sempat tertimpa/rusak
        // secara manual.
        User::updateOrCreate(
            ['email' => 'admin@poltekkes-riau.ac.id'],
            [
                'name'         => 'Administrator Prodi Keperawatan',
                'nim_nip'      => '198501012010121001',
                'password'     => Hash::make('admin123'),
                'role'         => 'admin',
                'phone_number' => '081234567890',
                'is_active'    => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'dosen@poltekkes-riau.ac.id'],
            [
                'name'         => 'Ns. Hj. Suryani, M.Kep., Sp.Kep.MB',
                'nim_nip'      => '197903152005012002',
                'password'     => Hash::make('dosen123'),
                'role'         => 'dosen',
                'phone_number' => '081298765432',
                'is_active'    => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'mahasiswa@poltekkes-riau.ac.id'],
            [
                'name'         => 'Ahmad Fadhil Pratama',
                'nim_nip'      => 'P032414401001',
                'password'     => Hash::make('mhs123'),
                'role'         => 'mahasiswa',
                'phone_number' => '081377889900',
                'is_active'    => true,
            ]
        );
    }
}
