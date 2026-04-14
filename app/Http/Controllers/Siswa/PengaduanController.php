<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Pengaduan;
use App\Models\Feedback;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PengaduanController extends Controller
{
    public function dashboardPage()
    {
        $pengaduan = Pengaduan::with(['kategori', 'feedback'])
            ->where('id_user', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return inertia('dashboard', [
            'pengaduan' => $pengaduan
        ]);
    }

    public function index()
    {
        $pengaduan = Pengaduan::with(['kategori', 'feedback'])
            ->where('id_user', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($pengaduan);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_kategori' => 'required|exists:kategori,id_kategori',
            'judul'       => 'required|string|max:255',
            'deskripsi'   => 'required|string',
            'foto'        => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $validated;
        $data['id_user']           = Auth::id();
        $data['tanggal_pengaduan'] = now();
        $data['status']            = 'menunggu';

        if ($request->hasFile('foto')) {
            $file     = $request->file('foto');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('pengaduan', $filename, 'public');
            // ✅ Simpan dengan prefix folder agar konsisten
            $data['foto'] = 'pengaduan/' . $filename;
        }

        $pengaduan = Pengaduan::create($data);

        \App\Models\HistoriPengaduan::create([
            'id_pengaduan'  => $pengaduan->id_pengaduan,
            'status'        => 'menunggu',
            'keterangan'    => 'Pengaduan dibuat',
            'tanggal_update'=> now(),
        ]);

        return redirect()->route('siswa.pengaduan.index')
            ->with('success', 'Pengaduan berhasil dibuat!');
    }

    public function editPage($id)
    {
        $pengaduan = Pengaduan::with(['kategori'])
            ->where('id_pengaduan', $id)
            ->where('id_user', Auth::id())
            ->firstOrFail();

        $kategori = \App\Models\Kategori::all();

        return inertia('Siswa/edit-pengaduan', [
            'pengaduan' => $pengaduan,
            'kategori'  => $kategori,
        ]);
    }

    public function editPageSimple($id)
    {
        $pengaduan = Pengaduan::with(['kategori'])
            ->where('id_pengaduan', $id)
            ->where('id_user', Auth::id())
            ->firstOrFail();

        $kategori = \App\Models\Kategori::all();

        return inertia('Siswa/edit-pengaduan-simple', [
            'pengaduan' => $pengaduan,
            'kategori'  => $kategori,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'id_kategori' => 'required|exists:kategori,id_kategori',
            'judul'       => 'required|string|max:255',
            'deskripsi'   => 'required|string',
            'foto'        => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $pengaduan = Pengaduan::where('id_pengaduan', $id)
            ->where('id_user', Auth::id())
            ->firstOrFail();

        $oldStatus = $pengaduan->status;
        $data      = $validated;

        if ($request->hasFile('foto')) {
            // ✅ Hapus foto lama pakai Storage facade
            if ($pengaduan->foto) {
                Storage::disk('public')->delete($pengaduan->foto);
            }

            $file     = $request->file('foto');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('pengaduan', $filename, 'public');
            // ✅ Simpan dengan prefix folder
            $data['foto'] = 'pengaduan/' . $filename;
        } else {
            $data['foto'] = $pengaduan->foto;
        }

        $pengaduan->update($data);

        if (isset($data['status']) && $data['status'] !== $oldStatus) {
            \App\Models\HistoriPengaduan::create([
                'id_pengaduan'   => $id,
                'status'         => $data['status'],
                'keterangan'     => 'Pengaduan diperbarui oleh siswa',
                'tanggal_update' => now(),
            ]);
        }

        return redirect()->route('siswa.pengaduan.index')
            ->with('success', 'Pengaduan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $pengaduan = Pengaduan::where('id_pengaduan', $id)
            ->where('id_user', Auth::id())
            ->firstOrFail();

        // ✅ Hapus foto pakai Storage facade
        if ($pengaduan->foto) {
            Storage::disk('public')->delete($pengaduan->foto);
        }

        $pengaduan->delete();

        return redirect()->route('siswa.pengaduan.index')
            ->with('success', 'Pengaduan berhasil dihapus!');
    }

    public function show($id)
    {
        $pengaduan = Pengaduan::with(['kategori', 'feedback'])
            ->where('id_pengaduan', $id)
            ->where('id_user', Auth::id())
            ->first();

        if (!$pengaduan) {
            return response()->json(['message' => 'Pengaduan tidak ditemukan'], 404);
        }

        return response()->json($pengaduan);
    }

    public function getStatus($id)
    {
        $pengaduan = Pengaduan::where('id_pengaduan', $id)
            ->where('id_user', Auth::id())
            ->first(['id_pengaduan', 'status', 'judul']);

        if (!$pengaduan) {
            return response()->json(['message' => 'Pengaduan tidak ditemukan'], 404);
        }

        return response()->json($pengaduan);
    }

    public function getFeedback($id)
    {
        $pengaduan = Pengaduan::where('id_pengaduan', $id)
            ->where('id_user', Auth::id())
            ->first();

        if (!$pengaduan) {
            return response()->json(['message' => 'Pengaduan tidak ditemukan'], 404);
        }

        $feedback = Feedback::with(['pengaduan', 'admin'])
            ->where('id_pengaduan', $id)
            ->get();

        return response()->json($feedback);
    }

    public function getRiwayat($id)
    {
        $pengaduan = Pengaduan::where('id_pengaduan', $id)
            ->where('id_user', Auth::id())
            ->first();

        if (!$pengaduan) {
            return response()->json(['message' => 'Pengaduan tidak ditemukan'], 404);
        }

        $riwayat = \App\Models\HistoriPengaduan::where('id_pengaduan', $id)
            ->orderBy('tanggal_update', 'desc')
            ->get();

        return response()->json($riwayat);
    }

    public function getKategori()
    {
        $kategori = Kategori::all();
        return response()->json($kategori);
    }

    public function indexPage()
    {
        $pengaduan = Pengaduan::with(['kategori', 'feedback'])
            ->where('id_user', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return inertia('Siswa/status-pengaduan', [
            'pengaduan' => $pengaduan,
        ]);
    }

    public function riwayatPage($id)
    {
        $pengaduan = Pengaduan::with(['kategori', 'feedback'])
            ->where('id_pengaduan', $id)
            ->where('id_user', Auth::id())
            ->firstOrFail();

        $riwayat = \App\Models\HistoriPengaduan::where('id_pengaduan', $id)
            ->orderBy('tanggal_update', 'desc')
            ->get();

        return inertia('Siswa/riwayat-pengaduan', [
            'pengaduan' => $pengaduan,
            'riwayat'   => $riwayat,
        ]);
    }

    public function feedbackPage($id)
{
    $pengaduan = Pengaduan::with(['kategori'])
        ->where('id_pengaduan', $id)
        ->where('id_user', Auth::id())
        ->firstOrFail();

    $feedback = Feedback::with(['admin'])
        ->where('id_pengaduan', $id)
        ->orderBy('tanggal_feedback', 'desc')
        ->get()
        ->map(function ($item) {
            return [
                'id_feedback' => $item->id_feedback,
                'pesan_feedback' => $item->pesan_feedback,
                'tanggal_feedback' => $item->tanggal_feedback,

                // ✅ SESUAIKAN DENGAN FIELD USER KAMU
                'admin' => $item->admin ? [
                    'name' => $item->admin->nama ?? $item->admin->name,
                    'avatar' => null
                ] : null
            ];
        });

    return inertia('Siswa/feedback-pengaduan', [
        'pengaduan' => $pengaduan,
        'feedback'  => $feedback,
    ]);
}
    } 
