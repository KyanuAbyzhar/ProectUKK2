<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaduan;
use App\Models\Feedback;
use App\Models\User;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Ambil statistik pengaduan
        $stats = [
            'totalPengaduan' => Pengaduan::count(),
            'pengaduanHariIni' => Pengaduan::whereDate('created_at', today())->count(),
            'menunggu' => Pengaduan::where('status', 'menunggu')->count(),
            'diproses' => Pengaduan::where('status', 'diproses')->count(),
            'selesai' => Pengaduan::where('status', 'selesai')->count(),
            'ditolak' => 0, 
        ];

        // Hitung pertumbuhan dari bulan lalu
        $bulanLalu = Pengaduan::whereMonth('created_at', now()->subMonth()->month)
                             ->whereYear('created_at', now()->subMonth()->year)
                             ->count();
        $bulanIni = Pengaduan::whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year)
                            ->count();
        
        if ($bulanLalu > 0) {
            $stats['pertumbuhan'] = round((($bulanIni - $bulanLalu) / $bulanLalu) * 100, 1);
        } else {
            $stats['pertumbuhan'] = 0;
        }

        // Hitung rata-rata waktu penyelesaian (dalam hari)
        $pengaduanSelesai = Pengaduan::where('status', 'selesai')->get();
        if ($pengaduanSelesai->isNotEmpty()) {
            $totalHari = $pengaduanSelesai->sum(function($pengaduan) {
                return $pengaduan->updated_at->diffInDays($pengaduan->created_at);
            });
            $stats['rataWaktuPenyelesaian'] = round($totalHari / $pengaduanSelesai->count(), 1);
        } else {
            $stats['rataWaktuPenyelesaian'] = 0;
        }

        // Ambil pengaduan terbaru
        $recentPengaduan = Pengaduan::with(['user', 'kategori'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($pengaduan) {
                return [
                    'id' => $pengaduan->id_pengaduan,
                    'judul' => $pengaduan->judul,
                    'kategori' => $pengaduan->kategori->nama_kategori,
                    'status' => $pengaduan->status,
                    'prioritas' => 'sedang', 
                    'pelapor' => $pengaduan->user->nama ?? 'Unknown',
                    'tanggal' => $pengaduan->created_at->toISOString(),
                ];
            });

        // Ambil kategori teratas
        $topKategori = DB::table('pengaduan')
            ->join('kategori', 'pengaduan.id_kategori', '=', 'kategori.id_kategori')
            ->select('kategori.nama_kategori', DB::raw('count(*) as jumlah'))
            ->groupBy('kategori.nama_kategori')
            ->orderBy('jumlah', 'desc')
            ->take(5)
            ->get()
            ->map(function ($kategori, $index) use ($stats) {
                $persentase = $stats['totalPengaduan'] > 0 
                    ? round(($kategori->jumlah / $stats['totalPengaduan']) * 100, 1)
                    : 0;
                
                return [
                    'nama' => $kategori->nama_kategori,
                    'jumlah' => $kategori->jumlah,
                    'persentase' => $persentase,
                ];
            });

        return inertia('Admin/dashboard', [
            'stats' => $stats,
            'recentPengaduan' => $recentPengaduan,
            'topKategori' => $topKategori,
        ]);
    }

    public function storeFeedback(Request $request, $id)
    {
        $request->validate([
            'pesan' => 'required|string|max:1000',
            'status' => 'required|in:menunggu,diproses,selesai'
        ]);
    
        $pengaduan = Pengaduan::findOrFail($id);
    
        $oldStatus = $pengaduan->status;
        $pengaduan->status = $request->status;
        $pengaduan->save();
    
        Feedback::create([
            'id_pengaduan' => $id,
            'dibuat_oleh' => auth()->id(), // ✅ pakai id_user
            'pesan_feedback' => $request->pesan,
            'tanggal_feedback' => now(),
        ]);
    
        \App\Models\HistoriPengaduan::create([
            'id_pengaduan' => $id,
            'status' => $request->status,
            'keterangan' => 'Admin memberikan feedback',
            'tanggal_update' => now()
        ]);
    
        return back()->with('success', 'Feedback berhasil dikirim!');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:menunggu,diproses,selesai'
        ]);

        $pengaduan = \App\Models\Pengaduan::findOrFail($id);
        $oldStatus = $pengaduan->status;
        $pengaduan->status = $request->status;
        $pengaduan->save();

        // Create histori entry
        \App\Models\HistoriPengaduan::create([
            'id_pengaduan' => $id,
            'status' => $request->status,
            'keterangan' => 'Status diubah dari ' . $oldStatus . ' menjadi ' . $request->status,
            'tanggal_update' => now()
        ]);

        return redirect()->back()->with('success', 'Status berhasil diperbarui!');
    }
}
