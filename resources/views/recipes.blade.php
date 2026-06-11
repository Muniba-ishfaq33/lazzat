@extends('layouts.app')

@section('title', 'Recipes | Lazzat لذّت')

@section('content')
<!-- Navbar injected -->

<div class="page-top">
  <div class="section-header" style="padding:40px 5% 0;">
    <span class="section-tag" data-t="recipes-title">Recipes</span>
    <h1 class="section-title" data-t="recipes-title">Recipes</h1>
    <p class="section-sub" data-t="recipes-sub">Discover authentic Pakistani & South Asian recipes</p>
  </div>

  <section class="section" style="padding-top:28px;">
    <!-- Controls -->
    <div class="recipes-controls">
      <!-- Tabs -->
      <div class="tabs">
        <button class="tab-btn active" id="tab-browse" onclick="switchTab('browse')" data-t="tab-browse">🍳 Browse All</button>
        <button class="tab-btn" id="tab-ingr" onclick="switchTab('ingr')" data-t="tab-ingr">🧊 By Ingredients</button>
      </div>
      <!-- Search -->
      <div class="search-box" id="search-box">
        <span class="search-icon">🔍</span>
        <input type="text" id="search-input" data-t="search-placeholder" data-t-attr="placeholder" placeholder="Search recipes..." oninput="handleSearch(this.value)">
      </div>
      <!-- Calorie filter -->
      <select class="filter-select" id="cal-filter" onchange="applyCalFilter()">
        <option value="all" data-t="cal-filter-all">All Calories</option>
        <option value="300" data-t="cal-filter-300">Under 300</option>
        <option value="500" data-t="cal-filter-500">300–500</option>
        <option value="700" data-t="cal-filter-700">500–700</option>
        <option value="700p" data-t="cal-filter-700p">700+</option>
      </select>
    </div>

    <!-- Ingredient input (hidden by default) -->
    <div class="ingredient-input-wrap" id="ingr-panel" style="display:none;">
      <label data-t="ingr-label">Enter ingredients you have (comma separated):</label>
      <div class="ingredient-row">
        <input type="text" class="ingredient-input" id="ingr-input" data-t="ingr-placeholder" data-t-attr="placeholder" placeholder="e.g. chicken, tomatoes, onion">
        <button class="btn-search-ingr" onclick="searchByIngredients()" data-t="ingr-btn">🔍 Find Recipes</button>
      </div>
    </div>

    <!-- Loading spinner overlay -->
    <div id="recipes-loading" style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:80px 20px;gap:20px;">
      <div style="width:52px;height:52px;border:4px solid var(--cream-dark);border-top-color:var(--saffron);border-radius:50%;animation:spin 0.8s linear infinite;"></div>
      <p style="color:var(--warm-gray);font-size:0.95rem;">Loading Pakistani recipes...</p>
      <p style="color:var(--saffron);font-size:0.8rem;font-weight:600;" id="loading-progress">Fetching from MealDB &amp; Spoonacular...</p>
    </div>
    <style>@keyframes spin{to{transform:rotate(360deg)}}</style>

    <!-- Recipe count -->
    <div id="recipe-count-bar" style="display:none;margin-bottom:16px;color:var(--warm-gray);font-size:0.88rem;font-weight:600;">
      Showing <span id="recipe-count" style="color:var(--saffron);">0</span> Pakistani recipes
    </div>

    <!-- Grid -->
    <div class="recipes-grid" id="recipes-grid" style="display:none;">
    </div>
    <div id="no-results" class="empty-state" style="display:none;">
      <div class="empty-state-icon">🍽️</div>
      <div class="empty-state-msg" data-t="no-results">No recipes found. Try a different search.</div>
    </div>
  </section>
</div>

<!-- Footer -->
<footer class="footer">
  <div class="footer-bottom" data-t="footer-copy">© 2025 Lazzat · Made with ❤️ for Pakistani food lovers</div>
</footer>

<script src="{{ asset('js/translations.js') }}"></script>
<script src="{{ asset('js/navbar.js') }}"></script>
<script>
  injectNavbar('recipes');

  let allMeals = [];
  let currentResults = [];
  let activeTab = 'browse';
  let calRange = 'all';
  let searchDebounce = null;

  function skeletons(n=8) {
    return Array(n).fill(0).map(() => `<div class="skeleton-card"><div class="skeleton-img"></div><div class="skeleton-body"><div class="skeleton-line"></div><div class="skeleton-line short"></div></div></div>`).join('');
  }

  function getCalLabel(meal) {
    const cat = meal.strCategory || '';
    const cats = { Breakfast:350, Dessert:420, Side:280, Starter:220, Beef:650, Chicken:520, Lamb:600, Seafood:430, Pasta:560, Vegetarian:380 };
    return cats[cat] || 480;
  }

  function matchCal(cal) {
    if (calRange === 'all') return true;
    if (calRange === '300') return cal < 300;
    if (calRange === '500') return cal >= 300 && cal <= 500;
    if (calRange === '700') return cal > 500 && cal <= 700;
    if (calRange === '700p') return cal > 700;
    return true;
  }

  function renderRecipes(meals) {
    currentResults = meals;
    const grid = document.getElementById('recipes-grid');
    const noRes = document.getElementById('no-results');
    const spinner = document.getElementById('recipes-loading');
    const countBar = document.getElementById('recipe-count-bar');
    const countEl = document.getElementById('recipe-count');
    const filtered = meals.filter(m => matchCal(getCalLabel(m)));

    // Hide spinner, show grid
    if (spinner) spinner.style.display = 'none';
    grid.style.display = 'grid';

    if (!filtered.length) {
      grid.innerHTML = '';
      grid.style.display = 'none';
      noRes.style.display = 'block';
      if (countBar) countBar.style.display = 'none';
      return;
    }
    noRes.style.display = 'none';
    // Show recipe count
    if (countBar && countEl) {
      countEl.textContent = filtered.length;
      countBar.style.display = 'block';
    }
    grid.innerHTML = filtered.map(m => {
      const cal = getCalLabel(m);
      const area = m.strArea || 'Pakistani';
      const flag = area === 'Indian' ? '🇮🇳' : '🇵🇰';
      return `
      <div class="recipe-card fade-in" onclick="goDetail('${m.idMeal}')">
        <img class="recipe-card-img" src="${m.strMealThumb}/preview" alt="${m.strMeal}" loading="lazy" onerror="this.src='https://www.themealdb.com/images/media/meals/default.jpg'">
        <div class="recipe-card-body">
          <div class="recipe-card-cuisine">${flag} ${area}</div>
          <h3 class="recipe-card-title">${m.strMeal}</h3>
          <div class="recipe-card-meta">
            <span>⏱ 30–45 mins</span>
            <span class="recipe-card-calorie">~${cal} kcal</span>
          </div>
          <button class="recipe-card-btn">${t('view-recipe')}</button>
        </div>
      </div>`;
    }).join('');
  }

  function goDetail(id) {
    window.location.href = `/recipe-detail?id=${id}`;
  }

  function switchTab(tab) {
    activeTab = tab;
    document.getElementById('tab-browse').classList.toggle('active', tab === 'browse');
    document.getElementById('tab-ingr').classList.toggle('active', tab === 'ingr');
    document.getElementById('search-box').style.display = tab === 'browse' ? 'flex' : 'none';
    document.getElementById('ingr-panel').style.display = tab === 'ingr' ? 'block' : 'none';
    if (tab === 'browse') {
      document.getElementById('search-input').value = '';
      renderRecipes(allMeals);
    } else {
      document.getElementById('recipes-grid').innerHTML = '';
      document.getElementById('no-results').style.display = 'none';
    }
  }

  function handleSearch(q) {
    clearTimeout(searchDebounce);
    if (!q.trim()) { renderRecipes(allMeals); return; }
    searchDebounce = setTimeout(async () => {
      document.getElementById('recipes-grid').innerHTML = skeletons(4);
      try {
        const results = await searchMeals(q);
        // filter to Pakistani/Indian only
        renderRecipes(results);
      } catch { renderRecipes([]); }
    }, 500);
  }

  function applyCalFilter() {
    calRange = document.getElementById('cal-filter').value;
    renderRecipes(currentResults.length ? currentResults : allMeals);
  }

  async function searchByIngredients() {
    const raw = document.getElementById('ingr-input').value.trim();
    if (!raw) { showToast('Please enter at least one ingredient', 'error'); return; }
    const ingredients = raw.split(',').map(s => s.trim()).filter(Boolean);
    document.getElementById('recipes-grid').innerHTML = skeletons(4);
    document.getElementById('no-results').style.display = 'none';
    try {
      // Fetch by first ingredient, then filter locally if multiple
      const results = await searchByIngredient(ingredients[0]);
      if (results.length === 0) {
        // fallback to name search
        const nameResults = await searchMeals(ingredients[0]);
        renderRecipes(nameResults);
      } else {
        renderRecipes(results);
        if (ingredients.length > 1) showToast(`Showing results for "${ingredients[0]}". Tip: search one ingredient at a time for best results.`, 'info');
      }
    } catch (e) {
      showToast('Search failed. Please try again.', 'error');
      renderRecipes([]);
    }
  }

  // Load all Pakistani recipes on init
  async function init() {
    const progress = document.getElementById('loading-progress');
    try {
      if (progress) progress.textContent = 'Fetching MealDB Pakistani recipes...';
      // Small delay so the spinner renders first
      await new Promise(r => setTimeout(r, 50));
      if (progress) progress.textContent = 'Loading Spoonacular Pakistani dishes...';
      allMeals = await fetchPakistaniRecipes();
      currentResults = allMeals;
      if (progress) progress.textContent = `Found ${allMeals.length} recipes!`;
      renderRecipes(allMeals);
    } catch (e) {
      const spinner = document.getElementById('recipes-loading');
      if (spinner) spinner.style.display = 'none';
      document.getElementById('recipes-grid').style.display = 'grid';
      showToast('Failed to load recipes. Check internet connection.', 'error');
    }
  }
  init();
</script>
@endsection
