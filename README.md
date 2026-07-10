# Sistem Pengajuan Transaksi Pengeluaran

Aplikasi web untuk mengelola pengajuan transaksi pengeluaran dengan **workflow approval berjenjang** dan **Role Based Access Control (RBAC)**. Dibangun dengan Laravel (MVC), MySQL, dan Laravel Breeze untuk autentikasi.

## Fitur Utama

- Autentikasi 5 role: **Staff, SPV, Manager, Direktur, Finance**
- Pengajuan transaksi + upload dokumen (PDF/JPG/PNG, maks 5 MB) via Laravel Storage
- Workflow approval dinamis sesuai kategori & nominal
- Pengecekan budget kategori & pemrosesan pembayaran oleh Finance
- Riwayat & timeline approval per pengajuan
- Dashboard statistik (jumlah per status, total dibayar, penggunaan budget per kategori)

## Teknologi

- Laravel 12 (PHP 8.2+)
- MySQL
- Laravel Breeze (Blade)
- Bootstrap 5 (via CDN) untuk seluruh halaman aplikasi

## Instalasi

```bash
# 1. Clone repository
git clone https://github.com/suadda/transaction-RBAC.git
cd transaction-RBAC

# 2. Install dependency
composer install
npm install

# 3. Konfigurasi environment
cp .env.example .env
php artisan key:generate
```

Edit `.env`, sesuaikan koneksi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=transaction_rbac
DB_USERNAME=root
DB_PASSWORD=
```

```bash
# 4. Migrasi + seed data awal (role, user, kategori, budget)
php artisan migrate:fresh --seed

# 5. Symlink storage agar file upload bisa diakses
php artisan storage:link
```

## Menjalankan Project

Jalankan dua proses (dua terminal terpisah):

```bash
# Terminal 1 - server Laravel
php artisan serve

# Terminal 2 - build asset (WAJIB tetap menyala selama development)
npm run dev
```

Buka `http://127.0.0.1:8000`.

## Akun Login Testing

Semua akun memakai password: **`password`**

| Role     | Email               |
|----------|---------------------|
| Staff    | staff@test.com      |
| SPV      | spv@test.com        |
| Manager  | manager@test.com    |
| Direktur | direktur@test.com   |
| Finance  | finance@test.com    |

## Struktur Database

| Tabel         | Keterangan                                                        |
|---------------|-------------------------------------------------------------------|
| `users`       | Akun pengguna, punya `role_id`                                    |
| `roles`       | Master role (Staff/SPV/Manager/Direktur/Finance)                 |
| `categories`  | Master kategori pengeluaran                                       |
| `budgets`     | Plafon budget per kategori                                        |
| `submissions` | Pengajuan transaksi (nomor, tanggal, nominal, lampiran, status)  |
| `approvals`   | Jejak setiap tindakan approve/reject                             |
| `payments`    | Catatan pembayaran oleh Finance                                  |

### Relasi

- `roles` **1—N** `users` — satu role dimiliki banyak user.
- `users` **1—N** `submissions` — satu user (Staff) membuat banyak pengajuan (`submissions.user_id`). Ini "Nama Pengaju".
- `categories` **1—N** `submissions` — satu kategori dipakai banyak pengajuan (`submissions.category_id`).
- `categories` **1—1** `budgets` — setiap kategori punya satu plafon budget (`budgets.category_id`).
- `submissions` **1—N** `approvals` — satu pengajuan memiliki banyak riwayat approval; tiap baris mencatat `role`, `status`, dan `comment` approver (`approvals.submission_id`, `approvals.user_id`).
- `submissions` **1—1** `payments` — satu pengajuan yang lolos menghasilkan satu pembayaran (`payments.submission_id`, `payments.paid_by`).

## Workflow Approval

Rantai approver ditentukan otomatis di `app/Services/WorkflowService.php`:

| Kondisi                                   | Alur approver                    |
|-------------------------------------------|----------------------------------|
| Kategori = **PO Produk**                  | Direktur                         |
| Bukan PO, nominal **≤ 5 juta**            | SPV                              |
| Bukan PO, **5 juta < nominal ≤ 10 juta**  | SPV → Manager                    |
| Bukan PO, nominal **> 10 juta**           | SPV → Manager → Direktur         |

Aturan tambahan:
- **Budget kategori tidak cukup** → status `Rejected`.
- **Salah satu approver menolak** → status `Rejected` (alur berhenti).
- **Seluruh approval selesai** → status `Waiting Finance`.
- **Finance** memeriksa saldo, lalu `Paid` (cukup) atau `Rejected` (tidak cukup).

### Status Pengajuan

`Draft` → `Submitted` → `Waiting SPV Approval` / `Waiting Manager Approval` / `Waiting Director Approval` → `Waiting Finance` → `Paid`. Jalur gagal berakhir di `Rejected`.

### Catatan Desain

- **Sisa budget** dihitung sebagai `budget.amount − Σ pengajuan berstatus Paid pada kategori tsb`, bukan dengan memutasi kolom `amount`, agar audit-friendly.
- **"Saldo" pada tahap Finance** diinterpretasikan sebagai sisa budget kategori: pembayaran hanya diproses bila sisa budget masih menutupi nominal, lalu status menjadi `Paid` (yang otomatis mengurangi sisa budget untuk pengajuan berikutnya).
