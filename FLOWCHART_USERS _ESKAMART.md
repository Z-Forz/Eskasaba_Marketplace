# 🔀 Flowchart & User Journey - Eskasaba Marketplace

Dokumentasi lengkap mengenai alur pengguna (*User Flowchart*), *State Diagram*, arsitektur role, dan alur transaksi pada platform **Eskasaba Marketplace (SMKN 1 Bangsri)**.

---

## 🏗️ 1. Struktur Role & Komponen Utama

```text
eskasaba-marketplace/
├── app/
│   ├── Http/Controllers/
│   │   ├── Admin/                      # Panel Administrasi (User, Seller, Request, Pembayaran, WA Bot)
│   │   │   ├── CategoryController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── OrderController.php
│   │   │   ├── PaymentController.php
│   │   │   ├── ReportController.php
│   │   │   ├── SellerController.php
│   │   │   ├── SellerRequestController.php
│   │   │   ├── UserController.php
│   │   │   ├── WebsiteSettingController.php
│   │   │   └── WhatsAppController.php
│   │   ├── Api/                        # Callback Webhook API (Payment Gateway & SiPintu)
│   │   │   ├── PaymentCallbackController.php
│   │   │   └── SiPintuWebhookController.php
│   │   ├── Auth/                       # Autentikasi Pengguna & SSO Sekolah
│   │   │   ├── AdminLoginController.php
│   │   │   ├── DashboardRedirectController.php
│   │   │   ├── SchoolCallbackController.php
│   │   │   └── SchoolLoginController.php
│   │   ├── Buyer/                      # Fitur Pembeli (Cart, Checkout, Order, Review)
│   │   │   ├── CartController.php
│   │   │   ├── CheckoutController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── OrderController.php
│   │   │   └── ReviewController.php
│   │   ├── Seller/                     # Dashboard & Manajemen Toko Penjual
│   │   │   ├── DashboardController.php
│   │   │   ├── OrderController.php
│   │   │   ├── PaymentController.php
│   │   │   ├── PickupScheduleController.php
│   │   │   ├── ProductController.php
│   │   │   ├── ProfileController.php
│   │   │   └── SellerRequestController.php
│   │   ├── HomeController.php
│   │   ├── NotificationController.php
│   │   ├── OAuthController.php
│   │   ├── ProfileController.php
│   │   └── SellerApplicationController.php
│   ├── Models/                         # Model Data (Order, Seller, Product, User, Payment, dll.)
│   └── Services/                       # Service Layer
│       ├── ImageCompressor.php
│       ├── SchoolApiService.php
│       ├── SiPintuService.php
│       ├── WhatsAppBotService.php       # Pengecekan status proses Node, QR Code, PID control
│       └── WhatsAppService.php          # Formatting nomor HP, template pesan, REST client ke Bot WA
├── resources/views/
│   ├── admin/                          # View Panel Administrasi
│   ├── auth/                           # View Login Siswa/Guru & Admin
│   ├── buyer/                          # View Keranjang, Checkout, Detail Pesanan
│   ├── components/                     # Component Blade (Navbar, Footer, Modal, Badge, OrderCard)
│   ├── products/                       # Katalog Produk & Detail Produk
│   ├── profile/                        # Profil User, Activity Logs, Pengajuan Toko
│   └── seller/                         # Dashboard Seller, Tambah Produk, Kelola Pesanan
├── routes/
│   ├── admin.php                       # Middleware: ['auth', 'role:admin']
│   ├── api.php                         # Route Webhook & Integration API
│   ├── auth.php                        # Route Login, Callback SSO, Logout
│   ├── buyer.php                       # Middleware: ['auth']
│   ├── guest.php                       # Route Akses Publik Tanpa Login
│   ├── seller.php                      # Middleware: ['auth', 'seller.approved']
│   └── web.php                         # Entrypoint Utama Rute Web
└── whatsapp-bot/                       # Microservice WhatsApp Bot (Node.js Express + Baileys WS)
    ├── auth_info_baileys/              # Folder Sesi WhatsApp Web Socket
    ├── logs/                           # Log Aktivitas Microservice
    ├── bot_state.json                  # Catatan Status Sesi Microservice
    └── index.js                        # REST Server API Baileys
```

---

## 🔄 2. Alur Pengguna (User Flowcharts)

### 1️⃣ Alur Autentikasi & Akun Sekolah (Sijuna / SiPintu / SSO)

```mermaid
flowchart TD
    Start([Buka Eskasaba Marketplace]) --> CheckAuth{Apakah Sudah Login?}
    
    CheckAuth -- Belum Login --> ChoiceLogin[Halaman Login / Masuk]
    ChoiceLogin --> MethodChoice{Pilih Metode Login}
    
    MethodChoice -- Akun Sekolah (SiPintu/Sijuna) --> InputCreds[Masukkan NIS/NIP & Password Sekolah]
    MethodChoice -- SSO OAuth --> OAuthRedirect[Redirect ke Gate OAuth Sekolah]
    MethodChoice -- Admin Panel --> AdminForm[Halaman Login Khusus Admin]
    
    InputCreds --> SubmitLogin[Verifikasi Service SiPintu / Database]
    OAuthRedirect --> OAuthCallback[Callback OAuth - Verifikasi Token]
    AdminForm --> AdminVerify[Verifikasi Kredensial Admin]
    
    SubmitLogin -- Valid --> AuthSuccess[Autentikasi Berhasil - Session Active]
    OAuthCallback -- Valid --> AuthSuccess
    AdminVerify -- Valid --> AuthSuccess
    
    SubmitLogin -- Tidak Valid --> LoginError[Pesan Error Kredensial Salah] --> ChoiceLogin
    OAuthCallback -- Tidak Valid --> LoginError
    AdminVerify -- Tidak Valid --> LoginError
    
    CheckAuth -- Sudah Login --> UserType{Pilih Akses Menu / Dashboard}
    AuthSuccess --> UserType
    
    UserType --> RoleBuyer[Akses Pembeli: Belanja, Keranjang & Checkout]
    UserType --> RoleSellerCheck{Status Toko Seller?}
    UserType --> RoleAdminCheck{Role User?}
    
    RoleAdminCheck -- Role: Admin --> AdminDash[Panel Administrasi /admin]
    RoleSellerCheck -- Approved --> SellerDash[Dashboard Penjual /seller]
    RoleSellerCheck -- Belum Daftar / Pending / Revisi --> ApplyForm[Form / Status Pengajuan Toko]
```

---

### 2️⃣ Alur Pembeli (Buyer Journey - Belanja, Checkout & Notifikasi)

```mermaid
flowchart TD
    StartBuyer([Pembeli Membuka Katalog Produk]) --> BrowseProducts[Lihat Produk & Filter Kategori]
    BrowseProducts --> SelectProduct[Pilih Detail Produk]
    
    SelectProduct --> CheckOptions{Memiliki Varian / Size?}
    CheckOptions -- Ya --> SelectVariant[Pilih Varian Rasa / Ukuran]
    CheckOptions -- Tidak --> DirectQty[Atur Jumlah Barang]
    SelectVariant --> DirectQty
    
    DirectQty --> AddToCart[Klik 'Tambah ke Keranjang']
    AddToCart --> ViewCart[Halaman Keranjang Belanja]
    
    ViewCart --> ClickCheckout[Klik 'Lanjut ke Checkout']
    ClickCheckout --> FillCheckoutForm[Pilih Jadwal Pickup & Metode Pembayaran]
    
    FillCheckoutForm --> SubmitOrder[Klik 'Buat Pesanan Sekarang']
    SubmitOrder --> SaveOrder[(Simpan Pesanan & Generate Invoice)]
    
    SaveOrder --> TriggerWA[WhatsAppService::sendNotifPesananBaru]
    TriggerWA --> WABuyer[WhatsApp Pembeli: Bukti Pesanan & Invoice]
    TriggerWA --> WASeller[WhatsApp Penjual: Alert Pesanan Masuk]
    
    SubmitOrder --> OrderDetail[Halaman Detail Pesanan / Invoice]
    OrderDetail --> WaitPickup[Menunggu Konfirmasi & Barang Siap Diambil]
```

---

### 3️⃣ Alur Penjual (Seller Journey - Pendaftaran, Produk & Pesanan)

```mermaid
flowchart TD
    StartSeller([User Mengajukan Toko Seller]) --> FillApplyForm[Isi Nama Toko, Deskripsi & Upload QRIS]
    FillApplyForm --> WaitAdminVerif[Status: Pending Verifikasi Admin]
    
    WaitAdminVerif --> AdminDecision{Keputusan Admin}
    AdminDecision -- Disetujui --> SellerApproved[Status: Approved - Akses Dashboard Seller]
    AdminDecision -- Perlu Revisi --> RevisionNotice[Notifikasi Revisi WA] --> FillApplyForm
    AdminDecision -- Ditolak --> RejectNotice[Notifikasi Ditolak WA]
    
    SellerApproved --> SellerAction{Pilih Aksi Seller}
    
    SellerAction --> AddProduct[Tambah Produk Baru]
    AddProduct --> ConfigVariants[Atur Harga Single / Varian Size]
    ConfigVariants --> UploadImages[Upload Foto Produk]
    UploadImages --> SaveProduct[(Simpan Produk ke Katalog)]
    
    SellerAction --> ProcessOrders[Kelola Pesanan Masuk]
    ProcessOrders --> UpdateStatus[Ubah Status Pesanan]
    UpdateStatus --> StatusChoice{Pilih Status Baru}
    
    StatusChoice --> ConfirmOrd[Dikonfirmasi]
    StatusChoice --> ProcOrd[Sedang Diproses]
    StatusChoice --> ReadyOrd[Siap Diambil]
    StatusChoice --> CompleteOrd[Selesai & Diserahterimakan]
    
    UpdateStatus --> AutoWANotify[WhatsAppService: Bot WA Notifikasi Pembeli]
```

---

### 4️⃣ Alur Pengelolaan Admin (Admin Flowchart)

```mermaid
flowchart TD
    StartAdmin([Admin Login ke Panel Admin]) --> AdminDashboard[Dashboard Statistik & Ringkasan Transaksi]
    
    AdminDashboard --> AdminMenu{Pilih Menu Kelola}
    
    AdminMenu --> VerifSellers[Verifikasi Pengajuan Toko Seller]
    VerifSellers --> ActionVerif{Aksi Admin}
    ActionVerif --> ApproveSeller[Setujui Toko Seller]
    ActionVerif --> ReviseSeller[Minta Revisi Form / QRIS]
    ActionVerif --> RejectSeller[Tolak / Cabut Status Seller]
    
    AdminMenu --> ManageRequests[Kelola Permintaan Kategori / Fitur]
    ManageRequests --> RespondReq[Balas Catatan & Update Status Request]
    
    AdminMenu --> WABotManager[Manajemen WhatsApp Bot /admin/whatsapp]
    WABotManager --> CheckBotState{Status Server Node Bot}
    CheckBotState -- Belum Aktif --> StartNodeProc[Jalankan Server Node / Restart PID]
    CheckBotState -- Perlu Scan QR --> ScanQR[Scan QR Code WA dengan HP Admin]
    CheckBotState -- Terhubung --> ActiveBot[Bot Siap Kirim Pesan Realtime]
    WABotManager --> ResetSession[Reset Sesi / Logout Bot]
    
    AdminMenu --> SyncUser[Sinkronisasi Akun SiPintu / Database Sekolah]
    SyncUser --> FetchSiPintu[(Update Data Siswa & Guru)]
```

---

## 📊 3. State Diagram Status Pesanan & Toko (State Machine)

### 🏷️ State Diagram Life-Cycle Status Pesanan (Orders)

```mermaid
stateDiagram-v2
    [*] --> Pending : Pembeli Checkout Pesanan
    Pending --> Confirmed : Penjual Mengonfirmasi Pesanan
    Pending --> Cancelled : Dibatalkan Pembeli / Penjual
    
    Confirmed --> Processing : Penjual Memulai Penyiapan Barang
    Processing --> ReadyForPickup : Barang Siap Diambil di Titik Pickup
    
    ReadyForPickup --> Completed : Barang Diserahkan & Transaksi Selesai
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
    Pending --> NeedsRevision : Admin Meminta Perbaikan Form / QRIS
    Pending --> Rejected : Admin Menolak Pendaftaran
    
    NeedsRevision --> Pending : User Mengirim Ulang Revisi Formulir
    
    Approved --> Revoked : Admin Mencabut Status Toko
    Revoked --> Pending : User Mengajukan Ulang Pengajuan Toko
    
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
| **Monitoring & Control QR WA Bot** | ❌ | ❌ | ❌ | ✅ |
| **Sinkronisasi Akun SiPintu** | ❌ | ❌ | ❌ | ✅ |
