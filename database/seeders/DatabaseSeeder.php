<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Fasilitas;
use App\Models\Inspeksi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Users
        $admin = User::create([
            'name' => 'Dr. Sarah Chen (Admin)',
            'email' => 'admin@sanitacheck.com',
            'password' => Hash::make('12345678'),
            'role' => 'admin',
        ]);

        $petugas = User::create([
            'name' => 'John Schmidt (Petugas)',
            'email' => 'petugas@sanitacheck.com',
            'password' => Hash::make('12345678'),
            'role' => 'petugas',
        ]);

        // 1.5 Create Jenis Fasilitas
        $jfToilet = \App\Models\JenisFasilitas::create(['nama_jenis' => 'Toilet', 'slug' => 'toilet']);
        $jfKantin = \App\Models\JenisFasilitas::create(['nama_jenis' => 'Kantin', 'slug' => 'kantin']);
        $jfTunggu = \App\Models\JenisFasilitas::create(['nama_jenis' => 'Ruang Tunggu', 'slug' => 'ruang-tunggu']);
        $jfCuci = \App\Models\JenisFasilitas::create(['nama_jenis' => 'Tempat Cuci Tangan', 'slug' => 'tempat-cuci-tangan']);
        $jfKelas = \App\Models\JenisFasilitas::create(['nama_jenis' => 'Ruang Kelas', 'slug' => 'ruang-kelas']);

        // 2. Create Facilities
        $f1 = Fasilitas::create([
            'nama_fasilitas' => 'Toilet Lobby Utama',
            'jenis_fasilitas' => 'toilet',
            'lokasi' => 'Gedung A, Lantai 1',
            'penanggung_jawab' => $petugas->id,
            'status_aktif' => true,
        ]);

        $f2 = Fasilitas::create([
            'nama_fasilitas' => 'Kantin Sehat Utama',
            'jenis_fasilitas' => 'kantin',
            'lokasi' => 'Gedung B, Lantai Dasar',
            'penanggung_jawab' => $petugas->id,
            'status_aktif' => true,
        ]);

        $f3 = Fasilitas::create([
            'nama_fasilitas' => 'Ruang Tunggu Poli Gigi',
            'jenis_fasilitas' => 'ruang-tunggu',
            'lokasi' => 'Gedung C, Lantai 2',
            'penanggung_jawab' => $petugas->id,
            'status_aktif' => true,
        ]);

        $f4 = Fasilitas::create([
            'nama_fasilitas' => 'Tempat Cuci Tangan Depan',
            'jenis_fasilitas' => 'tempat-cuci-tangan',
            'lokasi' => 'Pintu Masuk Utama',
            'penanggung_jawab' => $petugas->id,
            'status_aktif' => true,
        ]);

        $f5 = Fasilitas::create([
            'nama_fasilitas' => 'Ruang Kelas 401',
            'jenis_fasilitas' => 'ruang-kelas',
            'lokasi' => 'Gedung D, Lantai 4',
            'penanggung_jawab' => $petugas->id,
            'status_aktif' => true,
        ]);

        // 3. Create Inspections (History)
        // Facility 1: Toilet Lobby Utama (Status: Bersih)
        Inspeksi::create([
            'fasilitas_id' => $f1->id,
            'petugas_id' => $petugas->id,
            'tanggal_inspeksi' => Carbon::now()->subDays(2),
            'kondisi_kebersihan' => 'cukup',
            'ketersediaan_air' => 'tersedia',
            'ketersediaan_sabun' => 'tidak',
            'bau_tidak_sedap' => 'ya',
            'catatan' => 'Sabun habis dan agak bau.',
            'status_tindak_lanjut' => 'perlu dibersihkan',
        ]);

        Inspeksi::create([
            'fasilitas_id' => $f1->id,
            'petugas_id' => $petugas->id,
            'tanggal_inspeksi' => Carbon::now(),
            'kondisi_kebersihan' => 'baik',
            'ketersediaan_air' => 'tersedia',
            'ketersediaan_sabun' => 'tersedia',
            'bau_tidak_sedap' => 'tidak',
            'catatan' => 'Sudah dibersihkan, sabun telah diisi ulang.',
            'status_tindak_lanjut' => 'aman',
        ]);

        // Facility 2: Kantin Sehat Utama (Status: Perlu perhatian)
        Inspeksi::create([
            'fasilitas_id' => $f2->id,
            'petugas_id' => $petugas->id,
            'tanggal_inspeksi' => Carbon::now()->subHours(2),
            'kondisi_kebersihan' => 'cukup',
            'ketersediaan_air' => 'tersedia',
            'ketersediaan_sabun' => 'tidak',
            'bau_tidak_sedap' => 'tidak',
            'catatan' => 'Tempat cuci piring kantin kehabisan sabun cair.',
            'status_tindak_lanjut' => 'perlu dibersihkan',
        ]);

        // Facility 3: Ruang Tunggu Poli Gigi (Status: Buruk)
        Inspeksi::create([
            'fasilitas_id' => $f3->id,
            'petugas_id' => $petugas->id,
            'tanggal_inspeksi' => Carbon::now()->subDays(1),
            'kondisi_kebersihan' => 'buruk',
            'ketersediaan_air' => 'tidak',
            'ketersediaan_sabun' => 'tidak',
            'bau_tidak_sedap' => 'ya',
            'catatan' => 'Pipa AC bocor membuat lantai basah, air dispenser mati.',
            'status_tindak_lanjut' => 'perlu perbaikan',
        ]);

        // Facility 4: Tempat Cuci Tangan Depan (Status: Bersih)
        Inspeksi::create([
            'fasilitas_id' => $f4->id,
            'petugas_id' => $petugas->id,
            'tanggal_inspeksi' => Carbon::now()->subHours(5),
            'kondisi_kebersihan' => 'baik',
            'ketersediaan_air' => 'tersedia',
            'ketersediaan_sabun' => 'tersedia',
            'bau_tidak_sedap' => 'tidak',
            'catatan' => 'Semua indikator sanitasi sangat baik.',
            'status_tindak_lanjut' => 'aman',
        ]);
    }
}
