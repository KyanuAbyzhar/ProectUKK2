<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaduan;
use App\Models\Kategori;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $totalPengaduan = Pengaduan::count();
        $selesai        = Pengaduan::where('status', 'selesai')->count();
        $diproses       = Pengaduan::where('status', 'diproses')->count();
        $menunggu       = Pengaduan::where('status', 'menunggu')->count();
        $pengaduanHariIni = Pengaduan::whereDate('created_at', today())->count();

        // Pertumbuhan bulanan
        $bulanLalu = Pengaduan::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();
        $bulanIni = Pengaduan::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        if ($bulanLalu > 0) {
            $pertumbuhanBulanan = round((($bulanIni - $bulanLalu) / $bulanLalu) * 100, 1);
        } else {
            $pertumbuhanBulanan = $bulanIni > 0 ? 100 : 0;
        }

        // Rata-rata waktu penyelesaian
        $rataWaktuSelesai = 0;
        if ($selesai > 0) {
            $pengaduanSelesai = Pengaduan::where('status', 'selesai')->get();
            $totalHari = $pengaduanSelesai->sum(fn($p) => $p->updated_at->diffInDays($p->created_at));
            $rataWaktuSelesai = round($totalHari / $selesai, 1);
        }

        // Kategori terpopuler
        $kategoriTeratas = Kategori::withCount('pengaduan')
            ->whereHas('pengaduan')
            ->orderBy('pengaduan_count', 'desc')
            ->take(5)
            ->get()
            ->map(fn($k) => [
                'nama'       => $k->nama_kategori,
                'jumlah'     => $k->pengaduan_count,
                'persentase' => $totalPengaduan > 0
                    ? round(($k->pengaduan_count / $totalPengaduan) * 100, 1)
                    : 0,
            ]);

        // Trend bulanan 6 bulan terakhir
        $trendBulanan = [];
        for ($i = 5; $i >= 0; $i--) {
            $bulan = now()->subMonths($i);
            $trendBulanan[] = [
                'bulan'     => $bulan->translatedFormat('F'),
                'pengaduan' => Pengaduan::whereMonth('created_at', $bulan->month)
                    ->whereYear('created_at', $bulan->year)
                    ->count(),
                'selesai'   => Pengaduan::whereMonth('created_at', $bulan->month)
                    ->whereYear('created_at', $bulan->year)
                    ->where('status', 'selesai')
                    ->count(),
            ];
        }

        // ✅ Semua kategori dari DB untuk dropdown filter
        $kategoriList = Kategori::orderBy('nama_kategori')
            ->get(['id_kategori', 'nama_kategori']);

        return inertia('Admin/laporan-page', [
            'laporanData' => [
                'total_pengaduan'    => $totalPengaduan,
                'selesai'            => $selesai,
                'diproses'           => $diproses,
                'menunggu'           => $menunggu,
                'pengaduan_hari_ini' => $pengaduanHariIni,
                'pertumbuhan_bulanan'=> $pertumbuhanBulanan,
                'rata_waktu_selesai' => $rataWaktuSelesai,
                'kategori_teratas'   => $kategoriTeratas,
                'trend_bulanan'      => $trendBulanan,
            ],
            'kategoriList' => $kategoriList, // ✅ kirim ke frontend
        ]);
    }
}