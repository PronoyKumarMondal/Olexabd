<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>OlexaBD Admin</title>
    <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/3081/3081559.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-hover: #4338ca;
            --sidebar-width: 260px;
            --bg-color: #f8fafc;
            --text-color: #334155;
            --card-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        }
        
        html, body {
            height: 100%;
            overflow-x: hidden; /* Global kill for horizontal scroll */
        }

        body { 
            font-family: 'Outfit', sans-serif; 
            background-color: var(--bg-color); 
            color: var(--text-color);
        }
        
        /* ... existing navbar styles ... */
        .top-navbar {
            /* ... keep existing ... */
            height: 70px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #e2e8f0;
            padding: 0 1.5rem !important;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 900;
            transition: all 0.3s ease;
        }

        /* ... existing main-content ... */
        .main-content { 
            padding: 1.5rem !important;
            transition: padding 0.3s ease; 
        }

        @media (min-width: 992px) {
            .top-navbar { padding: 0 3.5rem !important; height: 90px; }
            .main-content { padding: 3.5rem !important; }
        }

        /* Sidebar Styles */
        .sidebar { 
            width: var(--sidebar-width); 
            height: 100vh;
            background: #ffffff; 
            border-right: 1px solid #e2e8f0;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease;
            box-shadow: 2px 0 10px rgba(0,0,0,0.02);
        }
        
        /* Sidebar Navigation Scroller */
        .sidebar-nav-scroll {
            flex-grow: 1;
            overflow-y: auto; /* Vertical Scrollbar */
            overflow-x: hidden; /* No Horizontal */
            margin-top: 1rem;
            flex-wrap: nowrap !important; /* Prevent column wrapping */
        }

        /* Custom Scrollbar Styles */
        .sidebar-nav-scroll::-webkit-scrollbar { width: 6px; }
        .sidebar-nav-scroll::-webkit-scrollbar-track { background: transparent; }
        .sidebar-nav-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .sidebar-nav-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        .sidebar-brand {
            height: 90px;
            display: flex;
            align-items: center;
            padding-left: 2.5rem !important;
            padding-right: 1.5rem !important;
            border-bottom: 1px solid #f1f5f9;
            flex-shrink: 0; /* Don't shrink logo */
        }
        
        .nav-link { 
            color: #64748b; 
            padding: 0.8rem 1.5rem 0.8rem 2.5rem !important;
            display: flex; 
            align-items: center; 
            gap: 0.8rem; 
            font-weight: 500;
            transition: all 0.2s ease;
            margin: 0.2rem 0;
            border-radius: 0;
            border-right: 4px solid transparent;
            white-space: nowrap; /* Prevent text wrapping */
        }
        
        /* ... rest of nav-link styles ... */
        
        .nav-link:hover { 
            background-color: #f8fafc; 
            color: var(--primary-color); 
        }
        
        .nav-link.active { 
            background-color: #f1f5f9; 
            color: var(--primary-color); 
            border-right-color: var(--primary-color);
        }

        .sidebar-heading {
            padding: 1.5rem 1.5rem 0.5rem 2.5rem !important; /* aligned with logo */
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #94a3b8;
            font-weight: 600;
        }/* ... existing sidebar styles ... */

        /* Main Content Area */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.3s ease;
            overflow-x: hidden; /* Prevent horizontal scroll */
        }

        /* Sidebar Scrollbar (The "Slider") */
        .sidebar nav::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar nav::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar nav::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .sidebar nav::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* ... existing styles ... */

        /* Responsive Breakpoint: Large (992px) */
        @media (max-width: 991.98px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); box-shadow: 5px 0 15px rgba(0,0,0,0.1); }
            .main-wrapper { margin-left: 0; }
            .sidebar-overlay {
                position: fixed; top: 0; left: 0; width: 100%; height: 100%;
                background: rgba(0,0,0,0.5); z-index: 999;
                display: none;
            }
            .sidebar-overlay.show { display: block; }
        }
    </style>
</head>
<body>
    
    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column" id="sidebar">
        <!-- ... sidebar content ... -->
        <div class="sidebar-brand">
            <a href="{{ route('shop.index') }}" class="text-decoration-none d-flex align-items-center gap-2">
                <div class="bg-primary text-white p-2 rounded-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                    <i class="bi bi-box-seam-fill"></i>
                </div>
                <span class="fs-5 fw-bold text-dark">Olexa<span class="text-primary">BD</span></span>
            </a>
            <button class="btn btn-sm btn-light ms-auto d-lg-none" onclick="toggleSidebar()"><i class="bi bi-x-lg"></i></button>
        </div>
        
        <nav class="nav flex-column sidebar-nav-scroll">
            <!-- ... nav items ... -->
            <div class="sidebar-heading">Menu</div>
            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>
            <!-- ... other links ... -->
            @if(Auth::guard('admin')->user()->isSuperAdmin() || Auth::guard('admin')->user()->hasPermission('product_create') || Auth::guard('admin')->user()->hasPermission('product_edit') || Auth::guard('admin')->user()->hasPermission('product_delete'))
            <a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">
                <i class="bi bi-gem"></i> Products
            </a>
            @endif

            @if(Auth::guard('admin')->user()->isSuperAdmin() || Auth::guard('admin')->user()->hasPermission('category_create') || Auth::guard('admin')->user()->hasPermission('category_edit') || Auth::guard('admin')->user()->hasPermission('category_delete'))
            <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                <i class="bi bi-collection"></i> Categories
            </a>
            @endif

            @if(Auth::guard('admin')->user()->isSuperAdmin() || Auth::guard('admin')->user()->hasPermission('order_edit'))
            <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">
                <i class="bi bi-bag-check-fill"></i> Orders
            </a>
            @endif
            @if(Auth::guard('admin')->user()->isSuperAdmin() || Auth::guard('admin')->user()->hasPermission('view_customers'))
            <a class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}" href="{{ route('admin.customers.index') }}">
                <i class="bi bi-people-fill"></i> Customers
            </a>
            @endif

            @if(Auth::guard('admin')->user()->isSuperAdmin() || Auth::guard('admin')->user()->hasPermission('manage_banners'))
            <a class="nav-link {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}" href="{{ route('admin.banners.index') }}">
                <i class="bi bi-images"></i> Banners
            </a>
            @endif

            @if(Auth::guard('admin')->user()->isSuperAdmin())
                <div class="sidebar-heading mt-3">Administration</div>
                <a class="nav-link {{ request()->routeIs('admin.super.index') ? 'active' : '' }}" href="{{ route('admin.super.index') }}">
                    <i class="bi bi-shield-lock-fill"></i> Manage Admins
                </a>
                <a class="nav-link {{ request()->routeIs('admin.super.health') ? 'active' : '' }}" href="{{ route('admin.super.health') }}">
                    <i class="bi bi-activity"></i> System Health
                </a>
            @endif
        </nav>

        <div class="p-3 border-top bg-light m-3 rounded-3 mb-4">
             <div class="d-flex align-items-center gap-2 mb-2">
                <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                    {{ substr(Auth::guard('admin')->user()->name, 0, 1) }}
                </div>
                <div class="overflow-hidden">
                    <div class="fw-bold text-truncate small">{{ Auth::guard('admin')->user()->name }}</div>
                    <div class="text-muted small text-truncate" style="font-size: 0.7rem;">{{ Auth::guard('admin')->user()->email }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-danger w-100 btn-sm"><i class="bi bi-box-arrow-right"></i> Log Out</button>
            </form>
        </div>
    </div>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light d-lg-none" onclick="toggleSidebar()">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <h4 class="mb-0 fw-bold text-dark">@yield('header')</h4>
            </div>
        </div>

        <!-- Content -->
        <div class="main-content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 border-start border-success border-4" role="alert">
                    <i class="bi bi-check-circle-fill text-success me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
            
            // Prevent background scrolling when sidebar is open on mobile
            if (sidebar.classList.contains('show')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        }
    </script>
</body>
</html>
