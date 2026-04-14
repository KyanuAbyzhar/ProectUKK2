<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pengaduan;
use App\Models\Feedback;
use App\Models\Kategori;
use Illuminate\Http\Request;

class SiswaPengaduanController extends Controller
{
    // 📋 LIST
    public function index()
    {
        $data = Pengaduan::with(['kategori', 'feedback'])
            ->where('id_user', auth()->id())
            ->latest()
            ->get();

        return response()->json($data);
    }

    // 🔍 DETAIL
    public function show($id)
    {
        $data = Pengaduan::with(['kategori', 'feedback'])
            ->findOrFail($id);

        return response()->json($data);
    }

    // 📝 CREATE
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required',
            'deskripsi' => 'required',
            'id_kategori' => 'required|exists:kategori,id_kategori',
            'foto' => 'nullable|image|max:2048'
        ]);

        $foto = null;

        if ($request->hasFile('foto')) {
            $foto = $request->file('foto')->store('pengaduan', 'public');
        }

        $data = Pengaduan::create([
            'id_user' => auth()->id(),
            'id_kategori' => $request->id_kategori,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'tanggal_pengaduan' => now()->toDateString(),
            'status' => 'menunggu',
            'foto' => $foto
        ]);

        // Create histori entry
        \App\Models\HistoriPengaduan::create([
            'id_pengaduan' => $data->id_pengaduan,
            'status' => 'menunggu',
            'keterangan' => 'Pengaduan dibuat',
            'tanggal_update' => now()
        ]);

        return response()->json([
            'message' => 'Berhasil membuat pengaduan',
            'data' => $data
        ]);
    }

    // 📊 GET STATUS
    public function getStatus($id)
    {
        $pengaduan = Pengaduan::where('id_pengaduan', $id)
            ->where('id_user', auth()->id())
            ->first(['id_pengaduan', 'status', 'judul']);

        if (!$pengaduan) {
            return response()->json(['message' => 'Pengaduan tidak ditemukan'], 404);
        }

        return response()->json($pengaduan);
    }

    // 💬 GET FEEDBACK
    public function getFeedback($id)
    {
        $pengaduan = Pengaduan::where('id_pengaduan', $id)
            ->where('id_user', auth()->id())
            ->first();

        if (!$pengaduan) {
            return response()->json(['message' => 'Pengaduan tidak ditemukan'], 404);
        }

        $feedback = Feedback::with(['pengaduan', 'admin'])
            ->where('id_pengaduan', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($feedback);
    }

    // 📝 GET RIWAYAT
    public function getRiwayat($id)
    {
        $pengaduan = Pengaduan::where('id_pengaduan', $id)
            ->where('id_user', auth()->id())
            ->first();

        if (!$pengaduan) {
            return response()->json(['message' => 'Pengaduan tidak ditemukan'], 404);
        }

        $riwayat = \App\Models\HistoriPengaduan::where('id_pengaduan', $id)
            ->orderBy('tanggal_update', 'desc')
            ->get();

        return response()->json($riwayat);
    }

    // 📂 GET KATEGORI
    public function getKategori()
    {
        $kategori = Kategori::all();
        return response()->json($kategori);
    }
}