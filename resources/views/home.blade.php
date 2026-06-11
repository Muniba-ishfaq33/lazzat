@extends('layouts.app')

@section('title', 'Lazzat | لذّت - Pakistani Recipe Planner')

@section('content')
<!-- Navbar injected by JS -->

<section class="hero hero-lazzat" id="home">
  <div class="hero-orbit"></div>
  <div class="hero-spice hero-spice-1">✦</div>
  <div class="hero-spice hero-spice-2">✺</div>
  <div class="hero-spice hero-spice-3">✦</div>

  <div class="hero-content hero-copy-panel">
    <div class="hero-badge">
      <span class="hero-badge-dot"></span>
      <span data-t="hero-badge">Taste. Plan. Enjoy.</span>
    </div>
    <h1 class="hero-title">
      <span data-t="hero-title">Discover Pakistani Recipes with</span><br>
      <span class="hero-title-urdu" data-t="hero-title-urdu">Smart Meal Planning</span>
    </h1>
    <p class="hero-sub" data-t="hero-sub">From traditional favorites to smart meal plans, Lazzat makes cooking simple, organized, and delightful.</p>
    <form class="hero-search" id="hero-search-form">
      <span>⌕</span>
      <input id="hero-search-input" type="search" data-t="hero-search-placeholder" data-t-attr="placeholder" placeholder="Search recipes, ingredients...">
      <button type="submit" data-t="hero-search-btn">Search</button>
    </form>
    <div class="hero-ctas">
      <a href="/recipes" class="btn-primary"><span data-t="hero-cta1">Explore Recipes</span></a>
      <a href="/recipes#ingr" class="btn-secondary"><span data-t="hero-cta2">Smart Suggest</span></a>
    </div>
  </div>

  <div class="hero-showcase" aria-label="Featured Pakistani meals">
    <div class="hero-image-carousel">
      <img class="hero-slide active" src="{{ asset('images/hero.png') }}" alt="Pakistani biryani meal planning hero">
      <img class="hero-slide" src="https://www.themealdb.com/images/media/meals/wyxwsp1486979827.jpg" alt="Chicken karahi">
      <img class="hero-slide" src="https://www.themealdb.com/images/media/meals/xrttsx1487339558.jpg" alt="Chicken biryani">
    </div>
    <div class="hero-float-card card-one">
      <img src="https://www.themealdb.com/images/media/meals/wyxwsp1486979827.jpg" alt="Chicken Karahi">
      <strong>Chicken Karahi</strong>
      <span>★ 4.8 · 30 mins</span>
    </div>
    <div class="hero-float-card card-two">
      <img src="https://www.themealdb.com/images/media/meals/xrttsx1487339558.jpg" alt="Chicken Biryani">
      <strong>Chicken Biryani</strong>
      <span>★ 4.9 · 45 mins</span>
    </div>
    <div class="hero-float-card card-three">
      <img src="https://www.themealdb.com/images/media/meals/1c5oso1614347493.jpg" alt="Aloo Paratha">
      <strong>Aloo Paratha</strong>
      <span>★ 4.7 · 20 mins</span>
    </div>
  </div>
</section>

<section class="section reveal-section" id="features">
  <div class="section-header">
    <span class="section-tag" data-t="feat-tag">Why Lazzat</span>
    <h2 class="section-title" data-t="feat-title">Everything for Your Kitchen</h2>
    <p class="section-sub" data-t="feat-sub">From finding the perfect recipe to generating your grocery list, all in one place.</p>
  </div>
  <div class="features-grid">
    <div class="feature-card tilt-card">
      <div class="feature-icon">⌕</div>
      <h3 class="feature-title" data-t="feat1-title">Smart Recipe Search</h3>
      <p class="feature-desc" data-t="feat1-desc">Search by name or type ingredients you already have.</p>
    </div>
    <div class="feature-card tilt-card">
      <div class="feature-icon">□</div>
      <h3 class="feature-title" data-t="feat2-title">Weekly Meal Planner</h3>
      <p class="feature-desc" data-t="feat2-desc">Plan breakfast, lunch and dinner for every day of the week.</p>
    </div>
    <div class="feature-card tilt-card">
      <div class="feature-icon">▱</div>
      <h3 class="feature-title" data-t="feat3-title">Auto Grocery List</h3>
      <p class="feature-desc" data-t="feat3-desc">Your grocery list builds itself from your meal plan.</p>
    </div>
    <div class="feature-card tilt-card">
      <div class="feature-icon">★</div>
      <h3 class="feature-title" data-t="feat4-title">Pakistani Cuisine</h3>
      <p class="feature-desc" data-t="feat4-desc">Authentic desi recipes, from Biryani to Nihari and Karahi.</p>
    </div>
  </div>
</section>

<section class="section reveal-section featured-band">
  <div class="section-header">
    <span class="section-tag">Featured Today</span>
    <h2 class="section-title">Popular Right Now</h2>
  </div>
  <div class="recipes-grid" id="featured-grid">
    <div class="skeleton-card"><div class="skeleton-img"></div><div class="skeleton-body"><div class="skeleton-line"></div><div class="skeleton-line short"></div></div></div>
    <div class="skeleton-card"><div class="skeleton-img"></div><div class="skeleton-body"><div class="skeleton-line"></div><div class="skeleton-line short"></div></div></div>
    <div class="skeleton-card"><div class="skeleton-img"></div><div class="skeleton-body"><div class="skeleton-line"></div><div class="skeleton-line short"></div></div></div>
    <div class="skeleton-card"><div class="skeleton-img"></div><div class="skeleton-body"><div class="skeleton-line"></div><div class="skeleton-line short"></div></div></div>
  </div>
  <div style="text-align:center; margin-top:36px;">
    <a href="/recipes" class="btn-primary">See All Recipes →</a>
  </div>
</section>

<section class="section reveal-section">
  <div class="section-header">
    <span class="section-tag">How It Works</span>
    <h2 class="section-title">Three Simple Steps</h2>
  </div>
  <div class="how-grid">
    <div class="tilt-card">
      <div>⌕</div>
      <h3>Find a Recipe</h3>
      <p>Search by name or by ingredients you already have at home.</p>
    </div>
    <div class="tilt-card">
      <div>□</div>
      <h3>Plan Your Week</h3>
      <p>Add recipes to your weekly planner for breakfast, lunch and dinner.</p>
    </div>
    <div class="tilt-card">
      <div>▱</div>
      <h3>Shop Smarter</h3>
      <p>Your grocery list is auto-generated. Tick off items as you shop.</p>
    </div>
  </div>
</section>

<footer class="footer">
  <div class="footer-grid">
    <div>
      <div class="footer-brand-name">Lazzat <span style="font-family:'Noto Nastaliq Urdu',serif;font-size:1.1rem;">لذّت</span></div>
      <p class="footer-brand-sub" data-t="footer-desc">Discover authentic Pakistani recipes, plan your meals, and shop smarter.</p>
    </div>
    <div>
      <div class="footer-col-title" data-t="footer-links-title">Quick Links</div>
      <ul class="footer-links">
        <li><a href="/" data-t="nav-home">Home</a></li>
        <li><a href="/recipes" data-t="nav-recipes">Recipes</a></li>
        <li><a href="/planner" data-t="nav-planner">Meal Planner</a></li>
        <li><a href="/grocery" data-t="nav-grocery">Grocery List</a></li>
      </ul>
    </div>
    <div>
      <div class="footer-col-title" data-t="footer-contact">Contact</div>
      <ul class="footer-links">
        <li><a href="#">lazzat@example.com</a></li>
        <li><a href="/login" data-t="nav-login">Login</a></li>
        <li><a href="/register" data-t="btn-register">Sign Up</a></li>
      </ul>
    </div>
  </div>
  <div class="footer-bottom" data-t="footer-copy">© 2026 Lazzat · Made with love for Pakistani food lovers</div>
</footer>

<script src="{{ asset('js/translations.js') }}"></script>
<script src="{{ asset('js/navbar.js') }}"></script>
<script>
  injectNavbar('home');

  document.getElementById('hero-search-form').addEventListener('submit', (event) => {
    event.preventDefault();
    const query = document.getElementById('hero-search-input').value.trim();
    window.location.href = query ? `/recipes?q=${encodeURIComponent(query)}` : '/recipes';
  });

  const heroSlides = document.querySelectorAll('.hero-slide');
  let heroSlideIndex = 0;
  setInterval(() => {
    heroSlides[heroSlideIndex].classList.remove('active');
    heroSlideIndex = (heroSlideIndex + 1) % heroSlides.length;
    heroSlides[heroSlideIndex].classList.add('active');
  }, 4200);

  async function loadFeatured() {
    try {
      const meals = await fetchPakistaniRecipes();
      const featured = meals.slice(0, 4);
      const grid = document.getElementById('featured-grid');
      grid.innerHTML = featured.map((m) => `
        <div class="recipe-card fade-in tilt-card">
          <img class="recipe-card-img" src="${m.strMealThumb}/preview" alt="${m.strMeal}" loading="lazy" onerror="this.style.display='none'">
          <div class="recipe-card-body">
            <div class="recipe-card-cuisine">Pakistani</div>
            <h3 class="recipe-card-title">${m.strMeal}</h3>
            <div class="recipe-card-meta">
              <span>30-45 mins</span>
              <span class="recipe-card-calorie">~480 kcal</span>
            </div>
            <button class="recipe-card-btn" onclick="window.location.href='/recipe-detail?id=${m.idMeal}'">View Recipe</button>
          </div>
        </div>`).join('');
    } catch (e) {}
  }
  loadFeatured();
</script>
@endsection
