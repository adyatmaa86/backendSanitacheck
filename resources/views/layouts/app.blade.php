<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>@yield('title', 'Admin') — SanitaCheck</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts & Material Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet"/>
    <!-- Custom Admin CSS & JS via Vite -->
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
    @vite(['resources/css/utilities.css'])
    <!-- Favicon / Tab Logo -->
    <link rel="icon" type="image/png" href="{{ asset('images/tabBG.png') }}"/>
    <script>
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);
    </script>
</head>
<body>

    <!-- ===================== SIDEBAR ===================== -->
    <aside class="admin-sidebar" id="adminSidebar">

        <!-- Brand -->
        <a class="sidebar-brand" href="{{ route('dashboard') }}">
            <img src="{{ asset('images/logo.png') }}" alt="Logo SanitaCheck" class="sidebar-brand-logo me-2">
            <div>
                <div class="sidebar-brand-name">SanitaCheck</div>
                <div class="sidebar-brand-sub">Portal Admin</div>
            </div>
        </a>

        <!-- New Inspection Button -->
        @if(Auth::user()->role === 'petugas')
            <a href="{{ route('inspections.index') }}" class="sidebar-new-btn">
                <span class="material-symbols-outlined">add</span>
                Inspeksi Baru
            </a>
        @endif

        <!-- Navigation -->
        <nav class="sidebar-nav">
            <div class="sidebar-section-label">Menu Utama</div>

            <a href="{{ route('dashboard') }}" class="sidebar-link {{ Route::is('dashboard') ? 'active' : '' }}">
                <span class="material-symbols-outlined {{ Route::is('dashboard') ? 'filled-icon' : '' }}">dashboard</span>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('facilities.index') }}" class="sidebar-link {{ Route::is('facilities.*') ? 'active' : '' }}">
                <span class="material-symbols-outlined {{ Route::is('facilities.*') ? 'filled-icon' : '' }}">domain</span>
                <span>Kelola Fasilitas</span>
            </a>

            @if(Auth::user()->role === 'admin')
                <a href="{{ route('admin.index') }}" class="sidebar-link {{ Route::is('admin.index') ? 'active' : '' }}">
                    <span class="material-symbols-outlined {{ Route::is('admin.index') ? 'filled-icon' : '' }}">admin_panel_settings</span>
                    <span>List Admin</span>
                </a>
                <a href="{{ route('petugas.index') }}" class="sidebar-link {{ Route::is('petugas.*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined {{ Route::is('petugas.*') ? 'filled-icon' : '' }}">badge</span>
                    <span>List Petugas</span>
                </a>
                <a href="{{ route('admin.pantau-petugas') }}" class="sidebar-link {{ Route::is('admin.pantau-petugas') ? 'active' : '' }}">
                    <span class="material-symbols-outlined {{ Route::is('admin.pantau-petugas') ? 'filled-icon' : '' }}">monitoring</span>
                    <span>Pantau Petugas</span>
                </a>
            @endif

            @if(Auth::user()->role === 'petugas')
                <a href="{{ route('inspections.index') }}" class="sidebar-link {{ Route::is('inspections.index') ? 'active' : '' }}">
                    <span class="material-symbols-outlined {{ Route::is('inspections.index') ? 'filled-icon' : '' }}">assignment</span>
                    <span>Form Inspeksi</span>
                </a>
                <a href="{{ route('petugas.tugas-saya') }}" class="sidebar-link {{ Route::is('petugas.tugas-saya') ? 'active' : '' }}">
                    <span class="material-symbols-outlined {{ Route::is('petugas.tugas-saya') ? 'filled-icon' : '' }}">task</span>
                    <span>Tugas Saya</span>
                </a>
            @endif

            <a href="{{ route('inspections.history') }}" class="sidebar-link {{ Route::is('inspections.history') ? 'active' : '' }}">
                <span class="material-symbols-outlined {{ Route::is('inspections.history') ? 'filled-icon' : '' }}">history</span>
                <span>Riwayat Laporan</span>
            </a>
        </nav>

        <!-- Sidebar Footer -->
        <div class="sidebar-footer">
            <div class="dropup sidebar-dropup">
                <button type="button" class="sidebar-link w-100 text-start" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                    <span class="material-symbols-outlined">settings</span>
                    <span>Pengaturan</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-up shadow-sm border rounded-3 min-w-200">
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2 py-2 fs-9" href="{{ route('settings.profile') }}">
                            <span class="material-symbols-outlined icon-sm text-slate-500">person</span> Edit Profil
                        </a>
                    </li>
                    @if(Auth::user()->role === 'admin')
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2 fs-9" href="{{ route('settings.add-admin') }}">
                                <span class="material-symbols-outlined icon-sm text-slate-500">admin_panel_settings</span> Tambah Admin
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2 fs-9" href="{{ route('settings.add-petugas') }}">
                                <span class="material-symbols-outlined icon-sm text-slate-500">person_add</span> Tambah Petugas
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="sidebar-link text-danger w-100 text-start">
                    <span class="material-symbols-outlined">logout</span>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Sidebar Overlay for mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- ===================== MAIN CONTENT ===================== -->
    <div class="main-content">

        <!-- Top Bar -->
        <header class="admin-topbar">
            <!-- Sidebar mobile toggle -->
            <button class="sidebar-toggle" id="sidebarToggle" type="button">
                <span class="material-symbols-outlined">menu</span>
            </button>
            <img src="{{ asset('images/logo.png') }}" alt="SanitaCheck" class="d-md-none topbar-logo-mobile">
            <div class="topbar-title d-none d-md-block">
                <h2>@yield('page-title', 'Dashboard')</h2>
                <p>@yield('page-subtitle', '')</p>
            </div>
             <div class="topbar-actions">
                <!-- Dark Mode Toggle -->
                <button type="button" id="themeToggle" class="topbar-icon-btn border-0 topbar-icon-btn-custom" title="Ubah Tema">
                    <span class="material-symbols-outlined">dark_mode</span>
                </button>

                <!-- Notifications -->
                @php
                    $allNotifications = auth()->user()->notifications()->take(10)->get();
                    $unreadCount = auth()->user()->unreadNotifications()->count();
                @endphp
                <div class="dropdown">
                    <div class="topbar-icon-btn cursor-pointer" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="material-symbols-outlined">notifications</span>
                        @if($unreadCount > 0)
                            <span class="notif-dot"></span>
                        @endif
                    </div>
                    <div class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2 py-2 notif-dropdown">
                        <div class="d-flex justify-content-between align-items-center px-3 py-1">
                            <h6 class="dropdown-header p-0 m-0 notif-title">Notifikasi</h6>
                            <div class="d-flex gap-2">
                                @if($unreadCount > 0)
                                    <form action="{{ route('notifications.read-all') }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="btn p-0 text-primary small notif-btn">
                                            Tandai dibaca
                                        </button>
                                    </form>
                                @endif
                                @if($allNotifications->count() > 0)
                                    <button type="button" id="deleteNotifBtn" class="btn p-0 text-danger small d-inline-flex align-items-center gap-1 notif-btn">
                                        <span class="material-symbols-outlined icon-sm" id="deleteNotifIcon">delete_sweep</span>
                                        <span id="deleteNotifText">Hapus Semua</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                        <hr class="dropdown-divider my-2">
                        
                        @if($allNotifications->count() > 0)
                            <form id="notifForm" action="{{ route('notifications.bulk-delete') }}" method="POST" class="m-0" onclick="event.stopPropagation();">
                                @csrf
                                @method('DELETE')
                                <ul class="list-unstyled m-0 px-2 d-flex flex-column gap-1">
                                    @foreach($allNotifications as $notification)
                                        <li class="p-2 rounded d-flex align-items-start gap-2 {{ $notification->read_at ? '' : 'bg-light border-start border-3 border-warning' }} transition-bg">
                                            <input type="checkbox" name="notification_ids[]" value="{{ $notification->id }}" class="form-check-input mt-1 notif-checkbox scale-95">
                                            
                                            <div class="flex-grow-1">
                                                @if($notification->read_at)
                                                    <div class="text-start text-wrap text-muted">
                                                        {{-- TODO: move style --}}<div class="d-flex justify-content-between align-items-start w-100 gap-2">
                                                            <span class="fw-bold text-secondary d-flex align-items-center gap-1 fs-10">
                                                                <span class="material-symbols-outlined text-secondary icon-sm">info</span>
                                                                {{ $notification->data['facility_name'] ?? 'Fasilitas' }}
                                                            </span>
                                                            <span class="text-muted notif-time">{{ $notification->created_at->diffForHumans() }}</span>
                                                        </div>
                                                        <p class="text-muted m-0 mt-1 notif-msg">
                                                            {{ $notification->data['message'] ?? '' }}
                                                        </p>
                                                    </div>
                                                @else
                                                    <button type="button" onclick="event.stopPropagation(); document.getElementById('readForm-{{ $notification->id }}').submit();" class="p-0 border-0 bg-transparent text-start text-wrap w-100">
                                                        {{-- TODO: move style --}}<div class="d-flex justify-content-between align-items-start w-100 gap-2">
                                                            <span class="fw-bold text-dark d-flex align-items-center gap-1 fs-10">
                                                                <span class="material-symbols-outlined text-warning icon-sm">warning</span>
                                                                {{ $notification->data['facility_name'] ?? 'Fasilitas' }}
                                                            </span>
                                                            <span class="text-muted notif-time">{{ $notification->created_at->diffForHumans() }}</span>
                                                        </div>
                                                        <p class="text-dark m-0 mt-1 notif-msg-unread">
                                                            {{ $notification->data['message'] ?? '' }}
                                                        </p>
                                                    </button>
                                                @endif
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </form>

                            <!-- Hidden forms for single read actions -->
                            @foreach($allNotifications as $notification)
                                @if(!$notification->read_at)
                                    <form id="readForm-{{ $notification->id }}" action="{{ route('notifications.read', $notification->id) }}" method="POST">
                                        {{-- TODO: move style --}}
                                        @csrf
                                    </form>
                                @endif
                            @endforeach
                        @else
                            <div class="text-center text-muted py-3 fs-9">Tidak ada notifikasi baru</div>
                        @endif
                    </div>
                </div>

                <!-- User chip -->
                <div class="topbar-user d-none d-md-flex">
                    <div class="topbar-avatar">{{ substr(Auth::user()->name, 0, 1) }}</div>
                    <span class="topbar-username d-none d-sm-inline">{{ Auth::user()->name }}</span>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <div class="page-content">

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <span class="material-symbols-outlined align-middle me-1 icon-md">check_circle</span>
                    <strong>Sukses!</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- disini bearer --}}

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <span class="material-symbols-outlined align-middle me-1 icon-md">error</span>
                    <strong>Error!</strong>
                    <ul class="mb-0 mt-1 small ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
