# 📚 Dokumentasi Fitur Siswa - Sistem Pengaduan Sarana

## 🎯 Overview
Sistem pengaduan sarana sekolah yang lengkap untuk siswa dengan fitur CRUD, tracking, dan feedback management.

## ✨ Fitur Utama

### 1. 📝 Form Pengaduan Baru
- **URL:** `/siswa/pengaduan/buat`
- **Fitur:**
  - Form lengkap dengan validasi
  - Pilihan kategori pengaduan
  - Upload foto dengan drag & drop
  - Preview foto sebelum upload
  - Error handling dan success notifications
  - Responsive design

### 2. 📊 Status Pengaduan
- **URL:** `/siswa/pengaduan/status`
- **Fitur:**
  - Daftar semua pengaduan siswa
  - Search/filter functionality
  - Status badges (menunggu, diproses, selesai)
  - Detail view untuk setiap pengaduan
  - Real-time status tracking

### 3. 📜 Riwayat Pengaduan
- **URL:** `/siswa/pengaduan/{id}/riwayat`
- **Fitur:**
  - Timeline visual perubahan status
  - Informasi lengkap pengaduan
  - Tracking history dengan timestamp
  - Status progression visualization
  - Navigation ke fitur terkait

### 4. 💬 Feedback Admin
- **URL:** `/siswa/pengaduan/{id}/feedback`
- **Fitur:**
  - Tampilan feedback dari admin
  - Avatar dan informasi admin
  - Status terkini pengaduan
  - Clean dan modern UI
  - Response history

### 5. 🏠 Dashboard
- **URL:** `/dashboard`
- **Fitur:**
  - Statistik pengaduan (total, menunggu, diproses, selesai)
  - Recent pengaduan list
  - Quick action buttons
  - Interactive cards dengan icons
  - Loading states

## 🛠️ Teknologi yang Digunakan

### Backend (Laravel)
- **Framework:** Laravel 11
- **Database:** MySQL/SQLite
- **API:** RESTful API endpoints
- **Authentication:** Laravel Auth
- **File Storage:** Local Storage

### Frontend (React + TypeScript)
- **Framework:** React 19 dengan TypeScript
- **UI Library:** shadcn/ui + Tailwind CSS
- **State Management:** React Hooks
- **Routing:** Inertia.js
- **Icons:** Lucide React
- **HTTP Client:** Axios

## 📁 Struktur File

### Backend
```
app/
├── Http/Controllers/
│   ├── Siswa/PengaduanController.php
│   └── Api/SiswaPengaduanController.php
├── Models/
│   ├── Pengaduan.php
│   ├── Feedback.php
│   ├── HistoriPengaduan.php
│   └── Kategori.php
└── ...

database/migrations/
├── 2026_04_03_084905_create_pengaduan_table.php
├── 2026_04_03_084915_create_feedback_table.php
├── 2026_04_03_084922_create_histori_pengaduan_table.php
└── 2026_04_03_084750_create_kategori_table.php

routes/
├── web.php
└── api.php
```

### Frontend
```
resources/js/
├── pages/Siswa/
│   ├── buat-pengaduan.tsx
│   ├── status-pengaduan.tsx
│   ├── riwayat-pengaduan.tsx
│   └── feedback-pengaduan.tsx
├── components/
│   ├── ui/ (shadcn/ui components)
│   └── app-sidebar.tsx
├── layouts/
│   └── app-layout.tsx
└── types/index.ts
```

## 🗄️ Database Schema

### Tabel Pengaduan
- `id_pengaduan` (Primary Key)
- `id_user` (Foreign Key ke users)
- `id_kategori` (Foreign Key ke kategori)
- `judul` (string)
- `deskripsi` (text)
- `tanggal_pengaduan` (date)
- `status` (enum: menunggu, diproses, selesai)
- `foto` (string, nullable)
- `timestamps`

### Tabel Feedback
- `id_feedback` (Primary Key)
- `id_pengaduan` (Foreign Key ke pengaduan)
- `dibuat_oleh` (Foreign Key ke users)
- `pesan_feedback` (text)
- `tanggal_feedback` (date)
- `timestamps`

### Tabel Histori Pengaduan
- `id_histori` (Primary Key)
- `id_pengaduan` (Foreign Key ke pengaduan)
- `status` (enum: menunggu, diproses, selesai)
- `keterangan` (text, nullable)
- `tanggal_update` (timestamp)
- `timestamps`

## 🚀 API Endpoints

### Pengaduan
- `GET /api/siswa/pengaduan` - List semua pengaduan user
- `POST /api/siswa/pengaduan` - Buat pengaduan baru
- `GET /api/siswa/pengaduan/{id}` - Detail pengaduan
- `GET /api/siswa/pengaduan/{id}/status` - Get status pengaduan
- `GET /api/siswa/pengaduan/{id}/feedback` - Get feedback pengaduan
- `GET /api/siswa/pengaduan/{id}/riwayat` - Get riwayat pengaduan

### Kategori
- `GET /api/kategori` - List semua kategori

## 🎨 UI/UX Features

### Design System
- **Components:** shadcn/ui
- **Styling:** Tailwind CSS
- **Icons:** Lucide React
- **Colors:** Consistent color scheme
- **Typography:** Clean hierarchy

### User Experience
- **Responsive:** Mobile-first design
- **Loading States:** Skeleton loaders
- **Error Handling:** User-friendly error messages
- **Success Feedback:** Toast notifications
- **Navigation:** Breadcrumb & sidebar
- **Search:** Real-time filtering
- **File Upload:** Drag & drop support

## 🔧 Installation & Setup

### Prerequisites
- PHP 8.2+
- Node.js 18+
- Composer
- MySQL/SQLite

### Backend Setup
```bash
# Clone repository
git clone <repository-url>
cd pengaduansarana

# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate
php artisan db:seed

# Start development server
php artisan serve
```

### Frontend Setup
```bash
# Install dependencies
npm install

# Build assets
npm run dev

# Production build
npm run build
```

## 📱 Usage Guide

### 1. Membuat Pengaduan
1. Login sebagai siswa
2. Klik "Buat Pengaduan" di sidebar
3. Isi form (kategori, judul, deskripsi)
4. Upload foto (opsional)
5. Klik "Kirim Pengaduan"

### 2. Melihat Status
1. Klik "Status Pengaduan" di sidebar
2. Lihat daftar semua pengaduan
3. Gunakan search untuk filter
4. Klik "Detail" untuk info lengkap

### 3. Tracking Riwayat
1. Dari halaman status, klik detail
2. Pilih "Lihat Riwayat"
3. Lihat timeline perubahan status

### 4. Melihat Feedback
1. Dari detail pengaduan
2. Klik "Lihat Feedback"
3. Baca tanggapan dari admin

## 🔐 Security Features

- **Authentication:** Laravel Auth middleware
- **Authorization:** User-specific data filtering
- **CSRF Protection:** Built-in Laravel CSRF
- **File Upload:** Image validation & size limits
- **Input Validation:** Request validation rules
- **SQL Injection:** Eloquent ORM protection

## 🎯 Best Practices

### Code Quality
- **TypeScript:** Type safety
- **Component Reusability:** Modular components
- **Error Handling:** Comprehensive error states
- **Performance:** Optimized queries & lazy loading

### Database
- **Normalization:** Proper relationships
- **Indexing:** Performance optimization
- **Constraints:** Data integrity
- **Migrations:** Version control

### UI/UX
- **Accessibility:** ARIA labels & semantic HTML
- **Performance:** Image optimization
- **Mobile:** Responsive design
- **Usability:** Intuitive navigation

## 🚀 Future Enhancements

### Planned Features
- [ ] Real-time notifications
- [ ] Email notifications
- [ ] Advanced filtering
- [ ] Export functionality
- [ ] Mobile app
- [ ] Admin dashboard
- [ ] Analytics & reporting

### Improvements
- [ ] Caching optimization
- [ ] API rate limiting
- [ ] Advanced search
- [ ] Bulk operations
- [ ] File compression
- [ ] Dark mode support

## 📞 Support

For issues and questions:
1. Check documentation
2. Review error logs
3. Test with sample data
4. Contact development team

---

**Last Updated:** April 2026
**Version:** 1.0.0
**Developer:** AI Assistant
