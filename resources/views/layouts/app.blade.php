<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="google-site-verification" content="IzFKzcNIBbyI0XS9bZUm-JVP018Ad6uC87QhFf8yhRU" />

    <title>@yield('title', 'OlexaBD | Premium Appliances')</title>
    <meta name="description" content="@yield('meta_description', 'Shop premium home appliances and electronics at OlexaBD. Best prices, warranty, and fast delivery across Bangladesh.')">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph -->
    <meta property="og:title" content="@yield('title', 'OlexaBD')" />
    <meta property="og:description" content="@yield('meta_description', 'Premium Home Appliances')" />
    <meta property="og:image" content="@yield('og_image', 'https://cdn-icons-png.flaticon.com/512/3081/3081559.png')" />
    <meta property="og:url" content="{{ url()->current() }}" />

    <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/3081/3081559.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- Schema -->
    @yield('schema')

    <!-- Meta Pixel Code -->
    @if(env('META_PIXEL_ID'))
    <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '{{ env('META_PIXEL_ID') }}');
    fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id={{ env('META_PIXEL_ID') }}&ev=PageView&noscript=1"
    /></noscript>
    @endif
    <!-- End Meta Pixel Code -->

    <style>
        :root {
            --primary-color: #4f46e5;
            --secondary-color: #1e1b4b;
            --accent-color: #f59e0b;
        }
        body { font-family: 'Outfit', sans-serif; background-color: #f8fafc; color: #334155; }
        
        /* Navbar */
        .navbar { background: white; padding: 0.75rem 0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
        .navbar-brand { font-weight: 800; font-size: 1.5rem; letter-spacing: -0.5px; color: var(--secondary-color) !important; }

        /* Global Layout */
        html, body {
            overflow-x: hidden;
            width: 100%;
            padding-top: 40px; /* Adjusted for no gap */
        }
        
        @media (max-width: 991.98px) {
            body { padding-bottom: 80px; padding-top: 40px; }
            .pb-safe { padding-bottom: env(safe-area-inset-bottom); }
        }
        .search-group { position: relative; max-width: 500px; width: 100%; }
        .search-input { padding-right: 50px; border-radius: 50px; border: 1px solid #e2e8f0; background: #f8fafc; padding-left: 1.5rem; }
        .search-input:focus { box-shadow: none; border-color: var(--primary-color); background: white; }
        .search-btn { position: absolute; right: 5px; top: 5px; border-radius: 50%; width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; background: var(--primary-color); color: white; border: none; }
        .nav-icon-btn { position: relative; width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; border-radius: 50%; color: var(--secondary-color); background: #f1f5f9; transition: all 0.2s; }
        .nav-icon-btn:hover { background: #e2e8f0; color: var(--primary-color); }
        .cart-badge { position: absolute; top: -2px; right: -2px; background: var(--accent-color); color: white; font-size: 0.7rem; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; border-radius: 50%; border: 2px solid white; }

        /* Footer */
        footer { background: #0f172a; color: #94a3b8; padding-top: 4rem; padding-bottom: 2rem; }
        .footer-heading { color: white; font-weight: 700; margin-bottom: 1.5rem; }
        .footer-link { color: #94a3b8; text-decoration: none; display: block; margin-bottom: 0.75rem; transition: color 0.2s; }
        .footer-link:hover { color: white; }
        .social-btn { width: 40px; height: 40px; border-radius: 50%; background: rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; color: white; transition: all 0.2s; }
        .social-btn:hover { background: var(--primary-color); transform: translateY(-3px); }
        
        /* Utils */
        .btn-primary { background-color: var(--primary-color); border: none; padding: 0.6rem 1.5rem; font-weight: 600; }
        .btn-primary:hover { background-color: #4338ca; }
        
        /* Global Product Card Button Standardization */
        .btn-equal {
            height: 36px !important;
            padding: 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 100% !important;
            border-width: 1px !important;
            border-style: solid !important;
            font-size: 0.85rem !important;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg fixed-top shadow-sm">
        <div class="container gap-3">
            <!-- 1. Logo -->
            <a class="navbar-brand d-flex align-items-center me-auto me-lg-0" href="{{ route('shop.index') }}">
                <div class="bg-primary text-white rounded-3 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                    <i class="bi bi-box-seam-fill fs-5"></i>
                </div>
                Olexa<span class="text-primary">BD</span>
            </a>

            <!-- Desktop Category Toggle -->
            <button class="btn btn-light rounded-pill border d-none d-lg-flex align-items-center gap-2 px-3 ms-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
                <i class="bi bi-grid-fill text-primary"></i>
                <span class="fw-bold small">Categories</span>
            </button>

            <!-- Mobile Menu Toggle (Hamburger) -->
            <div class="d-lg-none">
                 <button class="btn btn-light rounded-circle text-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu" aria-controls="mobileMenu">
                    <i class="bi bi-list fs-4"></i>
                </button>
            </div>

            <!-- Desktop View: Search and Menu -->
            <div class="collapse navbar-collapse" id="navbarContent">
                <!-- 2. Search Bar (Center) -->
                <form action="{{ route('shop.search') }}" method="GET" class="mx-lg-auto my-3 my-lg-0 search-group">
                    <input type="text" name="query" class="form-control form-control-lg search-input" placeholder="Search for appliances..." value="{{ request('query') }}">
                    <button type="submit" class="search-btn"><i class="bi bi-search"></i></button>
                </form>

                <!-- 3. Right Menu (Cart & User) -->
                <div class="d-flex align-items-center gap-3 mt-3 mt-lg-0">
                    <!-- Cart -->
                    <a href="{{ route('cart.index') }}" class="nav-icon-btn text-decoration-none">
                        <i class="bi bi-cart3 fs-5"></i>
                        <span class="cart-badge">{{ count(session('cart') ?? []) }}</span>
                    </a>

                    <!-- User Account -->
                    @auth
                        <div class="dropdown">
                            <a href="#" class="nav-icon-btn text-decoration-none" data-bs-toggle="dropdown">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-lg-end border-0 shadow-lg p-2 mt-2" style="width: 200px;">
                                <li class="px-3 py-2 border-bottom mb-2">
                                    <div class="fw-bold">{{ Auth::user()->name }}</div>
                                    <small class="text-muted" style="font-size: 0.75rem;">{{ Auth::user()->email }}</small>
                                </li>
                                @if(Auth::user()->is_admin)
                                    <li><a class="dropdown-item rounded-2 text-primary fw-bold" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Admin Panel</a></li>
                                @endif
                                <li><a class="dropdown-item rounded-2" href="{{ route('orders.index') }}"><i class="bi bi-box-seam me-2"></i>My Orders</a></li>
                                <li><a class="dropdown-item rounded-2" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i>Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item rounded-2 text-danger"><i class="bi bi-box-arrow-right me-2"></i>Log Out</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @endauth
                    @guest
                        <a href="{{ route('login') }}" class="btn btn-outline-primary rounded-pill px-4">Log In</a>
                        <a href="{{ route('register') }}" class="btn btn-primary rounded-pill px-4">Sign Up</a>
                    @endguest
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Mobile Search Bar (Collapsible) -->
    <!-- Mobile Offcanvas Menu (Left Side) -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileMenu" aria-labelledby="mobileMenuLabel">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title fw-bold text-primary" id="mobileMenuLabel">
                <a href="{{ route('shop.index') }}" class="text-decoration-none text-primary d-flex align-items-center">
                    <i class="bi bi-box-seam-fill me-2"></i>OlexaBD
                </a>
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-0">
            <!-- Mobile Search (Hidden on Desktop) -->
             <div class="p-3 bg-light border-bottom d-lg-none">
                <form action="{{ route('shop.search') }}" method="GET" class="search-group w-100">
                    <input type="text" name="query" class="form-control rounded-pill border-0 shadow-sm" placeholder="Search products..." value="{{ request('query') }}">
                </form>
            </div>

            <!-- Category List -->
            <div class="p-3">
                <h6 class="text-uppercase text-muted small fw-bold mb-3 ls-1">Categories</h6>
                <div class="list-group list-group-flush">
                    @if(isset($globalCategories))
                        @foreach($globalCategories as $cat)
                        <a href="{{ route('shop.category', $cat->slug) }}" class="list-group-item list-group-item-action py-2 px-0 border-0 d-flex align-items-center justify-content-between">
                            <span class="d-flex align-items-center gap-3">
                                @if($cat->image)
                                    <img src="{{ $cat->image }}" class="rounded-circle bg-light border" width="35" height="35" style="object-fit:cover;">
                                @else
                                    <div class="rounded-circle bg-light border d-flex align-items-center justify-content-center text-primary" style="width: 35px; height: 35px;">
                                        <i class="bi bi-grid small"></i>
                                    </div>
                                @endif
                                <span class="fw-medium">{{ $cat->name }}</span>
                            </span>
                            <i class="bi bi-chevron-right text-muted small"></i>
                        </a>
                        @endforeach
                    @endif
                </div>
            </div>
            
            <!-- Bottom Actions (Hidden on Desktop) -->
            <div class="p-3 border-top mt-auto d-lg-none">
                <a href="{{ route('orders.track') }}" class="btn btn-light w-100 fw-bold mb-2"><i class="bi bi-truck me-2"></i>Track Order</a>
                @auth
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger w-100 fw-bold"><i class="bi bi-box-arrow-right me-2"></i>Log Out</button>
                    </form>
                @else
                    <div class="d-flex gap-2">
                        <a href="{{ route('login') }}" class="btn btn-outline-primary w-50 fw-bold">Log In</a>
                        <a href="{{ route('register') }}" class="btn btn-primary w-50 fw-bold">Sign Up</a>
                    </div>
                @endauth
            </div>
        </div>
    </div>

    <!-- Content -->
    <main class="flex-grow-1">
        @yield('content')
    </main>

    <!-- Toast Notification -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1050;">
        @if(session('success'))
            <div id="liveToast" class="toast align-items-center text-white bg-success border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body fw-bold fs-6">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div id="errorToast" class="toast align-items-center text-white bg-danger border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body fw-bold fs-6">
                        <i class="bi bi-x-circle-fill me-2"></i> {{ session('error') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var toastElList = [].slice.call(document.querySelectorAll('.toast'))
            var toastList = toastElList.map(function (toastEl) {
                return new bootstrap.Toast(toastEl, { delay: 3000 })
            })
            toastList.forEach(toast => toast.show());
        });
    </script>
    </main>

    <!-- Mobile Bottom Nav -->
    <nav class="navbar navbar-dark fixed-bottom d-lg-none pb-safe border-top border-white border-opacity-10" style="background-color: rgba(30, 27, 75, 0.85); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);">
        <div class="container-fluid d-flex justify-content-around">
            <a href="{{ route('shop.index') }}" class="nav-link text-center {{ request()->routeIs('shop.index') ? 'text-white' : 'text-white-50' }}">
                <i class="bi bi-house-door fs-4 d-block mb-1"></i>
                <small style="font-size: 10px;">Home</small>
            </a>
            <a href="{{ route('shop.products') }}" class="nav-link text-center {{ request()->routeIs('shop.products') ? 'text-white' : 'text-white-50' }}">
                <i class="bi bi-grid fs-4 d-block mb-1"></i>
                <small style="font-size: 10px;">Shop</small>
            </a>
            <a href="{{ route('cart.index') }}" class="nav-link text-center position-relative {{ request()->routeIs('cart.index') ? 'text-white' : 'text-white-50' }}">
                <i class="bi bi-cart3 fs-4 d-block mb-1"></i>
                @if(count(session('cart') ?? []) > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger shadow-sm" style="font-size: 8px; margin-left: -15px; margin-top: 5px;">
                    {{ count(session('cart') ?? []) }}
                </span>
                @endif
                <small style="font-size: 10px;">Cart</small>
            </a>
            <a href="{{ route('profile.edit') }}" class="nav-link text-center {{ request()->routeIs('profile.edit') ? 'text-white' : 'text-white-50' }}">
                <i class="bi bi-person fs-4 d-block mb-1"></i>
                <small style="font-size: 10px;">Account</small>
            </a>
        </div>
    </nav>
    


    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="bg-primary text-white rounded-3 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                            <i class="bi bi-box-seam-fill small"></i>
                        </div>
                        <span class="h4 fw-bold text-white mb-0">OlexaBD</span>
                    </div>
                    <p class="small opacity-75 mb-4">Your one-stop destination for premium home appliances. We bring modern technology to your doorstep with trusted service and warranty.</p>
                    <div class="d-flex gap-2">
                        <a href="#" class="social-btn"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="social-btn"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="social-btn"><i class="bi bi-whatsapp"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6">
                    <h6 class="footer-heading">Quick Links</h6>
                    <a href="{{ route('shop.index') }}" class="footer-link">Home</a>
                    <a href="{{ route('shop.products') }}" class="footer-link">Shop All</a>
                    <a href="{{ route('shop.featured') }}" class="footer-link">Featured</a>
                    <a href="{{ route('orders.index') }}" class="footer-link">My Orders</a>
                </div>

                <div class="col-lg-2 col-md-6">
                    <h6 class="footer-heading">Support</h6>
                    <a href="{{ route('orders.track') }}" class="footer-link">Track Order</a>
                    <a href="#" class="footer-link">Return Policy</a>
                    <a href="#" class="footer-link">Warranty Claim</a>
                    <a href="#" class="footer-link">Contact Us</a>
                </div>

                <div class="col-lg-4 col-md-6">
                    <h6 class="footer-heading">Newsletter</h6>
                    <p class="small opacity-75">Subscribe to get special offers and updates.</p>
                    <form class="position-relative">
                        <input type="email" class="form-control rounded-pill bg-white border-0 py-3 ps-4" placeholder="Your email address">
                        <button class="btn btn-primary rounded-pill position-absolute top-0 end-0 m-1 px-4 py-2">Join</button>
                    </form>
                </div>
            </div>
            <div class="border-top border-secondary border-opacity-25 mt-5 pt-4 text-center small opacity-50">
                &copy; {{ date('Y') }} OlexaBD. All rights reserved.
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
