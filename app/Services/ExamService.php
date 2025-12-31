<?php

namespace App\Services;

use App\Models\ExamSchedule;
use App\Models\HasilUjian;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Service untuk mengelola logika bisnis ujian siswa
 * Menangani pengambilan data ujian, validasi waktu, dan transformasi data
 */
class ExamService
{
    /**
     * Mengambil daftar ujian untuk siswa berdasarkan kelas (jika diset)
     * 
     * @param string|null $studentClass Nama kelas siswa (contoh: "X IPA 1")
     * @param int|null $siswaId ID siswa untuk cek hasil ujian
     * @return Collection Koleksi data ujian yang sudah ditransformasi
     */
    public function getExamsForStudent(?string $studentClass = null, ?int $siswaId = null): Collection
    {
        // Ambil jadwal ujian dari database dengan relasi questionSet dan teacher
        // Filter berdasarkan kelas jika ada, urutkan berdasarkan tanggal dan waktu mulai
        $query = ExamSchedule::with(['questionSet.teacher'])
            ->orderBy('date_start')
            ->orderBy('time_start');

        if (! empty($studentClass)) {
            $query->where('class', $studentClass);
        }

        return $query->get()->map(fn($schedule) => $this->mapToExamData($schedule, $siswaId));
    }

    /**
     * Transformasi data jadwal ujian menjadi format yang siap ditampilkan di view
     * 
     * @param ExamSchedule $schedule Model jadwal ujian dari database
     * @param int|null $siswaId ID siswa untuk cek hasil ujian
     * @return array Data ujian dalam format array untuk view
     */
    private function mapToExamData(ExamSchedule $schedule, ?int $siswaId = null): array
    {
        // Parse tanggal dan waktu dari database
        $dateStart = Carbon::parse($schedule->date_start);
        $timeStart = Carbon::parse($schedule->time_start);
        $timeEnd = Carbon::parse($schedule->time_end);

        // Kembalikan data dalam format yang sesuai untuk tampilan
        return [
            'id' => $schedule->id,
            'subject' => $schedule->questionSet->subject ?? 'N/A', // Nama mata pelajaran
            'day' => $this->getDayInIndonesian($dateStart->dayOfWeek), // Hari dalam bahasa Indonesia
            'date' => $dateStart->format('d F Y'), // Format tanggal: 26 November 2025
            'time' => $timeStart->format('H:i') . ' - ' . $timeEnd->format('H:i'), // Format jam: 08:00 - 09:30
            'teacher' => $schedule->questionSet->teacher->nama_guru ?? 'N/A', // Nama guru pengajar
            'canStart' => $this->canStartExam($schedule), // Status apakah ujian bisa dimulai
            'isExpired' => $this->isExamExpired($schedule), // Status apakah ujian sudah selesai/lewat
            'isCompleted' => $this->isExamCompleted($schedule->id, $siswaId), // Status apakah siswa sudah mengerjakan
        ];
    }

    /**
     * Validasi apakah ujian dapat dimulai berdasarkan waktu saat ini
     * Ujian hanya bisa dimulai jika waktu sekarang berada dalam rentang waktu ujian
     * 
     * @param ExamSchedule $schedule Model jadwal ujian
     * @return bool true jika ujian bisa dimulai, false jika belum atau sudah lewat
     */
    private function canStartExam(ExamSchedule $schedule): bool
    {
        $now = now(); // Waktu sekarang (timezone Asia/Jakarta)
        
        // Parse waktu mulai dan selesai dari format HH:mm:ss
        $timeStart = Carbon::parse($schedule->time_start);
        $timeEnd = Carbon::parse($schedule->time_end);
        
        // Gabungkan tanggal mulai dengan waktu mulai untuk mendapatkan datetime lengkap
        // Gunakan copy() agar tidak mengubah objek original
        $examStart = $schedule->date_start->copy()
            ->setHour($timeStart->hour)
            ->setMinute($timeStart->minute)
            ->setSecond($timeStart->second);
        
        // Gabungkan tanggal selesai dengan waktu selesai
        // Jika date_end null, gunakan date_start (ujian di hari yang sama)
        $examEndDate = $schedule->date_end ?? $schedule->date_start;
        $examEnd = $examEndDate->copy()
            ->setHour($timeEnd->hour)
            ->setMinute($timeEnd->minute)
            ->setSecond($timeEnd->second);

        // Ujian bisa dimulai jika:
        // 1. Waktu sekarang >= waktu mulai ujian (gte = greater than or equal)
        // 2. Waktu sekarang <= waktu selesai ujian (lte = less than or equal)
        return $now->gte($examStart) && $now->lte($examEnd);
    }

    /**
     * Mengecek apakah ujian sudah lewat/selesai
     * 
     * @param ExamSchedule $schedule Model jadwal ujian
     * @return bool true jika ujian sudah selesai, false jika belum
     */
    private function isExamExpired(ExamSchedule $schedule): bool
    {
        $now = now();
        
        $timeEnd = Carbon::parse($schedule->time_end);
        $examEndDate = $schedule->date_end ?? $schedule->date_start;
        $examEnd = $examEndDate->copy()
            ->setHour($timeEnd->hour)
            ->setMinute($timeEnd->minute)
            ->setSecond($timeEnd->second);
        
        // Ujian expired jika waktu sekarang sudah melewati waktu selesai
        return $now->gt($examEnd);
    }

    /**
     * Mengecek apakah siswa sudah menyelesaikan ujian
     * 
     * @param int $examScheduleId ID jadwal ujian
     * @param int|null $siswaId ID siswa
     * @return bool true jika siswa sudah mengerjakan, false jika belum
     */
    private function isExamCompleted(int $examScheduleId, ?int $siswaId = null): bool
    {
        // Jika tidak ada siswaId, return false
        if (!$siswaId) {
            return false;
        }

        // Cek apakah ada hasil ujian untuk siswa ini di ujian ini
        return HasilUjian::where('siswa_id', $siswaId)
            ->where('ujian_id', $examScheduleId)
            ->exists();
    }

    /**
     * Konversi nomor hari menjadi nama hari dalam bahasa Indonesia
     * 
     * @param int $dayOfWeek Nomor hari (0=Minggu, 1=Senin, dst)
     * @return string Nama hari dalam bahasa Indonesia
     */
    private function getDayInIndonesian(int $dayOfWeek): string
    {
        // Array mapping nomor hari ke nama hari dalam bahasa Indonesia
        $days = [
            0 => 'Minggu',
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
        ];

        // Return nama hari, jika tidak ada kembalikan string kosong
        return $days[$dayOfWeek] ?? '';
    }
}
