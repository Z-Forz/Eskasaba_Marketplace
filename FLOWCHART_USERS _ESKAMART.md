# 🔀 Flowchart & User Journey - Eskasaba Marketplace

Dokumentasi lengkap mengenai alur pengguna (*User Flowchart*), *State Diagram*, arsitektur role, dan alur transaksi pada platform **Eskasaba Marketplace (SMKN 1 Bangsri)**.

---

## 🏗️ 1. Struktur Role & Komponen Utama

```text
eskasaba-marketplace/
├── app/
│   ├── Http/Controllers/
│   │   ├── Admin/                      # Pengelolaan Admin Panel (User, Seller, Request, WA Bot)
│   │   │   ├── AnnouncementController.php
│   │   │   ├── CategoryController.php
│   │   │   ├── OrderController.php
│   │   │   ├── RequestController.php
│   │   │   ├── SellerController.php
│   │   │   ├── UserController.php
│   │   │   ├── WebsiteSettingController.php
│   │   │   └── WhatsAppController.php
│   │   ├── Buyer/                      # Fitur Pembeli (Cart, Checkout, Order, Review)
│   │   │   ├── CartController.php
│   │   │   ├── CheckoutController.php
│   │   │   ├── OrderController.php
│   │   │   └── ReviewController.php
│   │   └── Seller/                     # Dashboard & Manajemen Toko Penjual
│   │       ├── DashboardController.php
│   │       ├── OrderController.php
│   │       ├── PickupScheduleController.php
│   │       ├── ProductController.php
│   │       └── SellerRequestController.php
├── resources/views/
│   ├── admin/                          # View Antarmuka Admin
│   ├── auth/                           # View Login Student/Teacher & Admin
│   ├── buyer/                          # View Keranjang, Checkout, Order History
│   ├── components/                     # Component Blade (Navbar, Footer, Modal, Alert)
│   ├── products/                       # Katalog Produk & Detail Produk
│   ├── profile/                        # Profil User, Activity Logs, Pengajuan Seller
│   └── seller/                         # Dashboard Seller, Tambah Produk, Olah Pesanan
└── routes/
    ├── admin.php                       # Middleware: ['auth', 'role:admin']
    ├── buyer.php                       # Middleware: ['auth']
    ├── seller.php                      # Middleware: ['auth', 'seller.approved']
    └── web.php                         # Route Publik (Beranda, Katalog, Detail Produk, Guide)
```

---

## 🔄 2. Alur Pengguna (User Flowcharts)

### 1️⃣ Alur Autentikasi & Akun Sekolah (Sijuna / SiPintu)

```mermaid
flowchart TD
    Start([Buka Eskasaba Marketplace]) --> CheckAuth{Apakah Sudah Login?}
    
    CheckAuth -- Belum Login --> ChoiceLogin[Halaman Login / Masuk]
    ChoiceLogin --> InputCreds[Masukkan NIS/NIP & Password Sijuna]
    InputCreds --> SubmitLogin[Verifikasi Database Sekolah]
    
    SubmitLogin -- Valid --> AuthSuccess[Autentikasi Berhasil - Session Active]
    SubmitLogin -- Tidak Valid --> LoginError[Tampilkan Pesan Error / Kredensial Salah] --> ChoiceLogin
    
    CheckAuth -- Sudah Login --> UserType{Pilih Akses Menu}
    AuthSuccess --> UserType
    
    UserType --> RoleBuyer[Akses Pembeli: Belanja & Keranjang]
    UserType --> RoleSellerCheck{Status Toko Seller?}
    UserType --> RoleAdminCheck{Role User?}
    
    RoleAdminCheck -- Role: Admin --> AdminDash[Akses Panel Admin]
    RoleSellerCheck -- Approved --> SellerDash[Akses Dashboard Seller]
    RoleSellerCheck -- Belum Daftar / Pending --> ApplyForm[Form Pengajuan Toko Seller]
```

---

### 2️⃣ Alur Pembeli (Buyer Journey - Belanja & Checkout)

```mermaid
flowchart TD
    StartBuyer([Pembeli Membuka Katalog Produk]) --> BrowseProducts[Lihat Produk & Filter Kategori]
    BrowseProducts --> SelectProduct[Pilih Detail Produk]
    
    SelectProduct --> CheckOptions{Memiliki Varian / Ukuran?}
    CheckOptions -- Ya --> SelectVariant[Pilih Varian Rasa/Ukuran]
    CheckOptions -- Tidak --> DirectQty[Atur Jumlah Barang]
    SelectVariant --> DirectQty
    
    DirectQty --> AddToCart[Klik 'Tambah ke Keranjang']
    AddToCart --> ViewCart[Halaman Keranjang Belanja]
    
    ViewCart --> ClickCheckout[Klik 'Lanjut ke Checkout']
    ClickCheckout --> FillCheckoutForm[Pilih Titik Pengambilan & Metode Pembayaran]
    
    FillCheckoutForm --> SubmitOrder[Klik 'Buat Pesanan Sekarang']
    SubmitOrder --> SaveOrder[(Simpan Pesanan ke Database)]
    
    SaveOrder --> TriggerWA[Kirim Notifikasi WhatsApp Otomatis]
    TriggerWA --> WABuyer[WhatsApp Pembeli: Bukti Pesanan & Invoice]
    TriggerWA --> WASeller[WhatsApp Penjual: Alert Pesanan Masuk]
    
    SubmitOrder --> OrderDetail[Halaman Detail Pesanan / Invoice]
    OrderDetail --> WaitPickup[Menunggu Konfirmasi & Barang Siap Diambil]
```

---

### 3️⃣ Alur Penjual (Seller Journey - Olah Produk & Pesanan)

```mermaid
flowchart TD
    StartSeller([User Mengajukan Toko Seller]) --> FillApplyForm[Isi Nama Toko, Alasan & Upload QRIS]
    FillApplyForm --> WaitAdminVerif[Status: Pending Verifikasi Admin]
    
    WaitAdminVerif --> AdminDecision{Keputusan Admin}
    AdminDecision -- Disetujui --> SellerApproved[Status: Approved - Akses Dashboard Seller]
    AdminDecision -- Minta Revisi --> RevisionNotice[Notifikasi Revisi WA] --> FillApplyForm
    AdminDecision -- Ditolak --> RejectNotice[Notifikasi Ditolak WA]
    
    SellerApproved --> SellerAction{Pilih Aksi Seller}
    
    SellerAction --> AddProduct[Tambah Produk Baru]
    AddProduct --> ConfigVariants[Atur Harga Tunggal / Harga per Varian Size]
    ConfigVariants --> UploadImages[Upload Foto Produk (Maks 5 Foto)]
    UploadImages --> SaveProduct[(Simpan Produk ke Katalog)]
    
    SellerAction --> ProcessOrders[Kelola Pesanan Masuk]
    ProcessOrders --> UpdateStatus[Ubah Status Pesanan]
    UpdateStatus --> StatusChoice{Status Baru}
    
    StatusChoice --> ConfirmOrd[Dikonfirmasi]
    StatusChoice --> ProcOrd[Sedang Diproses]
    StatusChoice --> ReadyOrd[Siap Diambil]
    StatusChoice --> CompleteOrd[Selesai & Diserahterimakan]
    
    UpdateStatus --> AutoWANotify[Bot WA Otomatis Notifikasi Pembeli]
```

---

### 4️⃣ Alur Pengelolaan Admin (Admin Flowchart)

```mermaid
flowchart TD
    StartAdmin([Admin Login ke Panel Admin]) --> AdminDashboard[Dashboard Statistik & Ringkasan]
    
    AdminDashboard --> AdminMenu{Pilih Menu Kelola}
    
    AdminMenu --> VerifSellers[Verifikasi Pengajuan Toko Seller]
    VerifSellers --> ActionVerif{Aksi Admin}
    ActionVerif --> ApproveSeller[Setujui Seller]
    ActionVerif --> ReviseSeller[Minta Revisi Seller]
    ActionVerif --> RejectSeller[Tolak / Cabut Seller]
    
    AdminMenu --> ManageRequests[Kelola Permintaan Kategori / Fitur]
    ManageRequests --> RespondReq[Kirim Catatan Balasan & Update Status]
    
    AdminMenu --> WABotManager[Manajemen WhatsApp Bot Baileys]
    WABotManager --> ScanQR[Scan QR Code WA dengan HP Admin]
    WABotManager --> ResetSession[Reset Sesi / Logout Bot]
    
    AdminMenu --> SyncUser[Sinkronisasi Akun SiPintu Gateway]
    SyncUser --> FetchSiPintu[(Update Database Siswa & Guru)]
```

---

## 📊 3. State Diagram Status Pesanan & Toko (State Machine)

### 🏷️ State Diagram Life-Cycle Status Pesanan (Orders)

```mermaid
stateDiagram-v2
    [*] --> Pending : Pembeli Checkout Pesanan
    Pending --> Confirmed : Penjual Mengonfirmasi Pesanan
    Pending --> Cancelled : Pesanan Dibatalkan Pembeli / Penjual
    
    Confirmed --> Processing : Penjual Memulai Penyiapan Barang
    Processing --> ReadyForPickup : Barang Siap Diambil di Kantin/Toko
    
    ReadyForPickup --> Completed : Barang Diterima Pembeli & Diselesaikan
    ReadyForPickup --> Cancelled : Pembatalan Darurat
    
    Completed --> [*]
    Cancelled --> [*]
```

---

### 🏪 State Diagram Life-Cycle Status Toko (Sellers)

```mermaid
stateDiagram-v2
    [*] --> Pending : User Mengirim Formulir Toko
    Pending --> Approved : Admin Menyetujui Pendaftaran
    Pending --> NeedsRevision : Admin Meminta Perbaikan Form/QRIS
    Pending --> Rejected : Admin Menolak Pendaftaran
    
    NeedsRevision --> Pending : User Mengirim Revisi Formulir
    
    Approved --> Revoked : Admin Mencabut Status Toko
    Revoked --> Pending : User Mengajukan Pengajuan Baru
    
    Approved --> [*]
    Rejected --> [*]
```

---

## ⚡ 4. Matriks Hak Akses & Fitur Pengguna (Access Matrix)

| Fitur / Modul | Tamu (Guest) | Pembeli (Siswa/Guru) | Penjual (Seller) | Admin Sekolah |
| :--- | :---: | :---: | :---: | :---: |
| **Melihat Katalog & Detail Produk** | ✅ | ✅ | ✅ | ✅ |
| **Tambah Produk ke Keranjang** | ❌ (Redir Login) | ✅ | ✅ | ✅ |
| **Checkout Pesanan & Notifikasi WA** | ❌ | ✅ | ✅ | ✅ |
| **Riwayat Pesanan & Detail Invoice** | ❌ | ✅ | ✅ | ✅ |
| **Form Pengajuan Toko Seller** | ❌ | ✅ | ✅ | ✅ |
| **Kelola Produk & Varian Harga** | ❌ | ❌ | ✅ | ✅ |
| **Kelola Status Pesanan Masuk** | ❌ | ❌ | ✅ | ✅ |
| **Kirim Request Kategori ke Admin** | ❌ | ❌ | ✅ | ✅ |
| **Verifikasi Toko & Request Seller** | ❌ | ❌ | ❌ | ✅ |
| **Monitoring & Scan QR WA Bot** | ❌ | ❌ | ❌ | ✅ |
| **Sinkronisasi Akun SiPintu** | ❌ | ❌ | ❌ | ✅ |
