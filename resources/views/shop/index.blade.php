@extends('layouts.app')

@section('title', 'OlexaBD | Best Home Appliances in Bangladesh')
@section('meta_description', 'Discover premium home appliances, electronics, and gadgets at OlexaBD. Official warranty, fast delivery, and best prices in Bangladesh.')
@section('meta_keywords', 'olexabd, olexa, olexa bd, home appliances, bangladesh electronics, fridge price bd, tv price bd, ac price bd')

@section('schema')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "OlexaBD",
  "url": "{{ url('/') }}",
  "logo": "https://cdn-icons-png.flaticon.com/512/3081/3081559.png",
  "sameAs": [
    "https://www.facebook.com/olexabd",
    "https://www.instagram.com/olexabd"
  ],
  "contactPoint": {
    "@type": "ContactPoint",
    "telephone": "+8801700000000",
    "contactType": "customer service",
    "areaServed": "BD"
  }
}
</script>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebSite",
  "name": "OlexaBD",
  "url": "{{ url('/') }}",
  "potentialAction": {
    "@type": "SearchAction",
    "target": "{{ route('shop.search') }}?query={search_term_string}",
    "query-input": "required name=search_term_string"
  }
}
</script>
@endsection

@section('content')
<!-- Hero Section -->
<!-- Hero Section (Carousel) -->
@if($banners->count() > 0)
<div id="heroCarousel" class="carousel slide mb-5" data-bs-ride="carousel" data-bs-interval="2000">
    <div class="carousel-indicators mb-3">
        @foreach($banners as $key => $banner)
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $key }}" class="{{ $key == 0 ? 'active' : '' }}" aria-current="{{ $key == 0 ? 'true' : 'false' }}" aria-label="Slide {{ $key + 1 }}" style="width: 10px; height: 10px; border-radius: 50%; margin: 0 5px;"></button>
        @endforeach
    </div>
    <div class="carousel-inner rounded-4 overflow-hidden shadow-lg">
        @foreach($banners as $key => $banner)
        <div class="carousel-item {{ $key == 0 ? 'active' : '' }} hero-banner-item">
            <a href="{{ $banner->link ?? '#' }}" class="d-block w-100 h-100 text-decoration-none">
                <!-- Background Image (Responsive) -->
                <picture>
                    {{-- Mobile Image (Max Width 768px) --}}
                    @if($banner->mobile_image)
                        <source media="(max-width: 768px)" srcset="{{ $banner->mobile_image }}">
                    @endif
                    {{-- Desktop Image (Default) --}}
                    <img src="{{ $banner->image }}" class="d-block w-100 h-100 hero-banner-img" alt="{{ $banner->title ?? 'Banner' }}">
                </picture>
                
                <!-- Premium Gradient Overlay (Left to Right) -->
                <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(90deg, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.5) 40%, rgba(0,0,0,0) 100%); pointer-events: none;"></div>
                
                @if($banner->title)
                <div class="carousel-caption d-flex flex-column justify-content-center h-100 top-0 text-start" style="left: 10%; right: auto; max-width: 800px; padding-top: 15rem;">
                    <div style="animation: slideInLeft 1s ease-out;">
                        @if($banner->badge_text)
                            <span class="badge bg-primary bg-opacity-75 text-white mb-3 px-3 py-2 rounded-1 text-uppercase ls-wide fw-bold fade-in-delay">{{ $banner->badge_text }}</span>
                        @endif
                        <h2 class="hero-title fw-bold text-white mb-4 lh-sm text-shadow-lg">{{ $banner->title }}</h2>
                        @if($banner->link)
                            <div class="mt-4">
                                <span class="btn btn-primary btn-lg rounded-pill px-5 py-3 fw-bold shadow-lg hover-lift ls-wide text-uppercase border-2 border-white">
                                    Shop Now <i class="bi bi-arrow-right-short fs-4 align-middle ms-1"></i>
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
                @endif
            </a>
        </div>
        @endforeach
    </div>
    
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev" style="width: 5%; opacity: 0; transition: opacity 0.3s;">
        <span class="bg-black bg-opacity-25 rounded-circle p-2 p-md-3 d-flex align-items-center justify-content-center backdrop-blur shadow-sm hero-nav-btn border border-white border-opacity-25">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        </span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next" style="width: 5%; opacity: 0; transition: opacity 0.3s;">
        <span class="bg-black bg-opacity-25 rounded-circle p-2 p-md-3 d-flex align-items-center justify-content-center backdrop-blur shadow-sm hero-nav-btn border border-white border-opacity-25">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
        </span>
        <span class="visually-hidden">Next</span>
    </button>


</div>
@else
<!-- Water Theme Fallback Hero -->
<div class="container mb-5">
    <!-- Reusing hero-banner-item class for identical shape -->
    <div class="hero-banner-item rounded-4 overflow-hidden position-relative shadow-lg" style="background: linear-gradient(120deg, #00c6ff 0%, #0072ff 100%); width: 100%;">
        
        <!-- Water Pattern Overlay -->
        <div class="position-absolute top-0 start-0 w-100 h-100" style="background-image: url('https://www.transparenttextures.com/patterns/cubes.png'); opacity: 0.1;"></div>

        <!-- Content Container (Absolute to center it within the aspect-ratio box) -->
        <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center p-4 p-md-5">
            <div class="row align-items-center w-100 position-relative z-1 m-0">
                <div class="col-lg-6 text-white ps-lg-5">
                    <h1 class="display-3 fw-bold mb-2 ls-tight">OlexaBD</h1>
                    <p class="lead opacity-90 mb-4 h5 fw-normal">Premium Appliances for a Pure & Refreshing Lifestyle.</p>
                    <div class="d-flex gap-3">
                        <a href="#all-products" class="btn btn-light btn-lg rounded-pill px-5 fw-bold text-primary shadow-sm">Shop Now</a>
                    </div>
                </div>
                <!-- Decorative Circle & Image (Desktop) -->
                <div class="col-lg-6 d-none d-lg-block position-relative text-center">
                    <div class="position-absolute start-50 top-50 translate-middle bg-white opacity-20 rounded-circle" style="width: 350px; height: 350px; filter: blur(40px);"></div>
                    <img src="https://cdn-icons-png.flaticon.com/512/3659/3659898.png" alt="Appliances" class="img-fluid position-relative" style="max-height: 350px; transform: rotate(-5deg); filter: drop-shadow(0 20px 40px rgba(0,0,0,0.3));">
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Categories Section -->
<div class="container mb-5">
    <h4 class="fw-bold mb-4">Shop by Category</h4>
    <div class="row g-3">
        @foreach($categories as $category)
        <div class="col-6 col-md-4 col-lg-2">
            <a href="{{ route('shop.category', $category) }}" class="card h-100 border-0 shadow-sm text-decoration-none transition-hover text-center py-4 bg-white align-items-center">
                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mb-3 text-primary" style="width: 60px; height: 60px;">
                    @if($category->image)
                        <img src="{{ $category->image }}" class="rounded-circle" width="60" height="60" style="object-fit:cover;">
                    @else
                        <i class="bi bi-grid fs-3"></i>
                    @endif
                </div>
                <h6 class="text-dark fw-bold mb-1">{{ $category->name }}</h6>
                <small class="text-muted">{{ $category->products_count }} Items</small>
            </a>
        </div>
        @endforeach
    </div>
</div>

<!-- Featured Products -->
<div id="featured" class="container mb-5">
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <span class="text-primary fw-bold text-uppercase small ls-1">Recommended</span>
            <h3 class="fw-bold mb-0">Featured Products</h3>
        </div>
        <a href="{{ route('shop.featured') }}" class="btn btn-outline-dark rounded-pill btn-sm px-3">View All</a>
    </div>

    <div class="row g-4">
        @foreach($featuredProducts as $product)
        <div class="col-md-3 col-6">
            <div class="card h-100 border-0 shadow-sm product-card">
                <div class="position-relative overflow-hidden bg-light rounded-top-3" style="height: 240px;">
                    <span class="badge bg-danger position-absolute top-0 start-0 m-3 shadow-sm rounded-pill px-3">Hot</span>
                    @if($product->image)
                        <img src="{{ $product->image }}" class="w-100 h-100 object-fit-cover transition-transform" alt="{{ $product->name }}">
                    @else
                        <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                            <i class="bi bi-image fs-1 opacity-50"></i>
                        </div>
                    @endif
                </div>
                <div class="card-body d-flex flex-column">
                    <div class="small text-muted mb-1">{{ $product->category->name ?? 'Appliance' }}</div>
                    <h6 class="card-title fw-bold text-truncate mb-2">{{ $product->name }}</h6>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="text-primary fw-bold mb-0">৳{{ $product->price }}</h5>
                        <div class="text-warning small">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2 mt-auto position-relative" style="z-index: 2;">
                        <form action="{{ route('cart.add') }}" method="POST" class="w-100">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <button class="btn btn-outline-primary w-100 rounded-pill btn-sm fw-bold" title="Add to Cart">
                                <i class="bi bi-cart-plus"></i> Add
                            </button>
                        </form>
                        <form action="{{ route('cart.add') }}" method="POST" class="w-100">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="buy_now" value="1">
                            <button class="btn btn-primary w-100 rounded-pill btn-sm fw-bold" title="Buy Now">
                                Buy
                            </button>
                        </form>
                    </div>
                    <a href="{{ route('shop.show', $product->slug) }}" class="stretched-link"></a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- All Products Section -->
<div id="all-products" class="bg-white py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">All Products</h2>
            <p class="text-muted">Browse our complete collection of home essentials.</p>
        </div>

        <div class="row g-4 mb-5">
            @foreach($products as $product)
            <div class="col-md-3 col-6">
                <div class="card h-100 border border-light shadow-hover transition-all">
                    <div class="position-relative bg-light rounded-top" style="height: 200px;">
                        @if($product->image)
                            <img src="{{ $product->image }}" class="w-100 h-100 object-fit-cover" alt="{{ $product->name }}">
                        @else
                            <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                                <i class="bi bi-box fs-1 opacity-25"></i>
                            </div>
                        @endif
                    </div>
                    <div class="card-body d-flex flex-column">
                        <small class="text-muted mb-1">{{ $product->category->name ?? 'General' }}</small>
                        <h6 class="fw-bold mb-3 text-truncate">{{ $product->name }}</h6>
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fs-5 fw-bold text-dark">৳{{ $product->price }}</span>
                        </div>
                        
                        <div class="d-flex gap-2 mt-auto position-relative" style="z-index: 2;">
                            <form action="{{ route('cart.add') }}" method="POST" class="w-100">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <button type="submit" class="btn btn-outline-primary w-100 rounded-pill btn-sm fw-bold" title="Add to Cart">
                                    <i class="bi bi-cart-plus"></i> Add
                                </button>
                            </form>
                            <form action="{{ route('cart.add') }}" method="POST" class="w-100">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="buy_now" value="1">
                                <button type="submit" class="btn btn-primary w-100 rounded-pill btn-sm fw-bold">
                                    Buy
                                </button>
                            </form>
                        </div>
                    </div>
                     <a href="{{ route('shop.show', $product->slug) }}" class="stretched-link"></a>
                </div>
            </div>
            @endforeach

            <!-- View All Card (12th Slot) -->
            <div class="col-md-3 col-6">
                <a href="{{ route('shop.products') }}" class="card h-100 border-0 shadow-sm bg-primary text-white text-decoration-none d-flex align-items-center justify-content-center text-center transition-hover">
                    <div class="p-4">
                        <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
                            <i class="bi bi-arrow-right fs-3 text-white"></i>
                        </div>
                        <h5 class="fw-bold mb-1">View All</h5>
                        <p class="small opacity-75 mb-0">Browse full catalog</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .ls-tight { letter-spacing: -1px; }
    .ls-1 { letter-spacing: 1px; }
    .object-fit-cover { object-fit: cover; }
    .transition-hover:hover { transform: translateY(-5px); }
    .transition-transform { transition: transform 0.3s ease; }
    .product-card:hover .transition-transform { transform: scale(1.05); }
    .shadow-hover:hover { box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important; border-color: transparent !important; }
    
    .hover-visible { visibility: visible; }
    .product-card:hover .hover-visible { opacity: 1; bottom: 0; }

    /* Hero Banner Styles (Global) */
    .hero-banner-item {
        aspect-ratio: 21/5; /* approx 1920x450 */
        width: 100%;
    }
    .hero-banner-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        transition: transform 6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }
    .carousel-item.active .hero-banner-img {
        transform: scale(1.05);
    }
    .hero-title {
        font-size: 4rem;
        font-family: 'Outfit', sans-serif;
        letter-spacing: -0.02em;
    }
    .hero-nav-btn {
        width: 50px; height: 50px;
    }
    .ls-wide { letter-spacing: 2px; }
    .text-shadow-lg { text-shadow: 0 4px 20px rgba(0,0,0,0.6); }
    .hover-lift { transition: all 0.3s ease; }
    .hover-lift:hover { transform: translateY(-3px); box-shadow: 0 10px 25px rgba(0,0,0,0.3) !important; }

    /* Mobile Adjustments */
    @media (max-width: 768px) {
        .hero-banner-item {
            height: auto;
            aspect-ratio: 4/3; /* Mobile Ratio */
            background-color: white;
        }
        .hero-banner-item .hero-banner-img {
            object-fit: contain;
        }
        .hero-title {
            font-size: 2.2rem;
        }
        .carousel-caption {
            max-width: 100% !important;
            padding-right: 2rem !important;
            bottom: auto !important;
            top: 0;
        }
        .hero-nav-btn {
            width: 35px; height: 35px;
            padding: 0.5rem !important;
        }
    }

    #heroCarousel:hover .carousel-control-prev,
    #heroCarousel:hover .carousel-control-next {
        opacity: 1 !important;
    }
    .backdrop-blur { backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px); }
    
    @keyframes slideInLeft {
        from { opacity: 0; transform: translateX(-30px); }
        to { opacity: 1; transform: translateX(0); }
    }
    .fade-in-delay { animation: fadeIn 1s ease-out 0.3s backwards; }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
</style>
@endsection
