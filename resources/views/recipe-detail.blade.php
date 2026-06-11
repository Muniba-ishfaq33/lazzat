@extends('layouts.app')

@section('title', 'Recipe | Lazzat لذّت')

@section('content')
<!-- Navbar injected -->

<div id="detail-root">
  <!-- Loading state -->
  <div class="recipe-detail-hero" style="background:var(--cream-dark); display:flex; align-items:center; justify-content:center; margin-top:70px;">
    <div style="text-align:center; color:var(--warm-gray);">
      <div style="font-size:2rem; margin-bottom:10px;">🍳</div>
      <div>Loading recipe...</div>
    </div>
  </div>
</div>

<!-- Add to Planner Modal -->
<div class="modal-overlay" id="plannerModal">
  <div class="modal">
    <div class="modal-header">
      <h3 class="modal-title">Add to Meal Planner</h3>
      <button class="modal-close" onclick="closePlannerModal()">✕</button>
    </div>
    <div id="modal-meal-name" style="font-weight:600; margin-bottom:16px; color:var(--charcoal);"></div>
    <div class="form-group">
      <label class="form-label">Day</label>
      <select class="form-input" id="modal-day">
        <option value="0">Monday</option><option value="1">Tuesday</option>
        <option value="2">Wednesday</option><option value="3">Thursday</option>
        <option value="4">Friday</option><option value="5">Saturday</option><option value="6">Sunday</option>
      </select>
    </div>
    <div class="form-group">
      <label class="form-label">Meal Type</label>
      <select class="form-input" id="modal-mealtype">
        <option value="0">Breakfast</option><option value="1">Lunch</option><option value="2">Dinner</option>
      </select>
    </div>
    <button class="btn-auth" onclick="confirmAddToPlanner()" style="margin-top:12px;">✅ Add to Planner</button>
  </div>
</div>

<footer class="footer" style="margin-top:0;">
  <div class="footer-bottom" data-t="footer-copy">© 2025 Lazzat · Made with ❤️ for Pakistani food lovers</div>
</footer>

<script src="{{ asset('js/translations.js') }}"></script>
<script src="{{ asset('js/navbar.js') }}"></script>
<script>
  injectNavbar('recipes');

  const params = new URLSearchParams(window.location.search);
  const mealId = params.get('id');
  let currentMeal = null;

  async function loadDetail() {
    if (!mealId) { window.location.href = '/recipes'; return; }
    try {
      currentMeal = await fetchMealDetail(mealId);
      if (!currentMeal) throw new Error('Not found');
      renderDetail(currentMeal);
      // Load save/favorite state from DB for this user
      await Promise.all([loadSavedState(), loadFavoriteState()]);
      updateSaveButton();
    } catch(e) {
      document.getElementById('detail-root').innerHTML = `
        <div class="page-top" style="padding:80px 5%; text-align:center; color:var(--warm-gray);">
          <div style="font-size:3rem; margin-bottom:16px;">😕</div>
          <h2>Recipe not found</h2>
          <a href="/recipes" style="color:var(--saffron); font-weight:600; text-decoration:none; margin-top:16px; display:inline-block;">← Back to Recipes</a>
        </div>`;
    }
  }

  function getIngredients(meal) {
    const ingredients = [];
    for (let i = 1; i <= 20; i++) {
      const name = meal[`strIngredient${i}`];
      const measure = meal[`strMeasure${i}`];
      if (name && name.trim()) {
        ingredients.push({ name: name.trim(), measure: measure ? measure.trim() : '' });
      }
    }
    return ingredients;
  }

  function getSteps(meal) {
    if (!meal.strInstructions) return [];
    return meal.strInstructions
      .split(/\r\n|\n|\r/)
      .map(s => s.trim())
      .filter(s => s.length > 20)
      .slice(0, 12);
  }

  function renderDetail(meal) {
    const ingredients = getIngredients(meal);
    const steps = getSteps(meal);
    const cal = estimateCal(meal);
    const ytId = meal.strYoutube ? meal.strYoutube.split('v=')[1] : null;
    const area = meal.strArea || 'Pakistani';
    const flag = area === 'Indian' ? '🇮🇳' : '🇵🇰';

    document.title = `${meal.strMeal} | Lazzat لذّت`;

    const html = `
    <!-- Back link -->
    <div style="padding:80px 5% 0; display:flex; align-items:center;">
      <a href="/recipes" style="color:var(--saffron); font-weight:600; text-decoration:none; display:flex; align-items:center; gap:6px; font-size:0.95rem;" data-t="detail-back">← Back to Recipes</a>
    </div>

    <!-- Hero image -->
    <div class="recipe-detail-hero" style="margin-top:16px;">
      <img class="recipe-detail-img" src="${meal.strMealThumb}" alt="${meal.strMeal}" onerror="this.style.background='var(--cream-dark)'">
      <div class="recipe-detail-overlay"></div>
      <div class="recipe-detail-info">
        <h1 class="recipe-detail-title">${meal.strMeal}</h1>
        <div class="recipe-detail-meta">
          <span class="recipe-meta-chip">${flag} ${area}</span>
          <span class="recipe-meta-chip">🍽️ ${meal.strCategory || 'Main'}</span>
          <span class="recipe-meta-chip">🔥 ~${cal} kcal</span>
          <span class="recipe-meta-chip">⏱ 30–45 mins</span>
          <span class="recipe-meta-chip">👥 4 servings</span>
        </div>
        <div class="recipe-detail-actions">
          <button class="btn-save-recipe ${isRecipeSaved(meal.idMeal) ? 'saved' : ''}" id="saveRecipeBtn" onclick="toggleSaveRecipe()">
            <span class="save-icon">${isRecipeSaved(meal.idMeal) ? '✓' : '＋'}</span>
            <span>${isRecipeSaved(meal.idMeal) ? 'Saved Recipe' : 'Save Recipe'}</span>
          </button>
          <button class="btn-save-recipe favorite ${isFavoriteRecipe(meal.idMeal) ? 'saved' : ''}" id="favoriteRecipeBtn" onclick="toggleFavoriteRecipe()">
            <span class="save-icon">${isFavoriteRecipe(meal.idMeal) ? '♥' : '♡'}</span>
            <span>${isFavoriteRecipe(meal.idMeal) ? 'Favorited' : 'Add Favorite'}</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Body -->
    <div class="recipe-detail-body">
      <div class="recipe-detail-grid">
        <!-- Ingredients -->
        <div>
          <div class="ingredients-card">
            <h3 data-t="detail-ingredients">Ingredients</h3>
            ${ingredients.map(ing => `
              <div class="ingredient-item">
                <span class="ingredient-dot"></span>
                <span><strong>${ing.measure}</strong> ${ing.name}</span>
              </div>`).join('')}
          </div>
          <button class="btn-add-planner" onclick="openPlannerModal()" style="width:100%; justify-content:center; margin-top:16px;">
            <span data-t="detail-add-planner">+ Add to Meal Planner</span>
          </button>
          ${ytId ? `
          <a href="${meal.strYoutube}" target="_blank" rel="noopener" style="display:flex;align-items:center;justify-content:center;gap:8px;background:#FF0000;color:#fff;border-radius:12px;padding:12px 20px;text-decoration:none;font-weight:700;margin-top:12px;">
            ▶ Watch on YouTube
          </a>` : ''}
        </div>

        <!-- Steps -->
        <div>
          <div class="steps-section">
            <h3 data-t="detail-steps">Step-by-Step Instructions</h3>
            ${steps.length ? steps.map((step, i) => `
              <div class="step-item fade-in" style="animation-delay:${i*0.05}s">
                <div class="step-num">${i + 1}</div>
                <div class="step-text">${step}</div>
              </div>`).join('') : `<p style="color:var(--warm-gray); line-height:1.8;">${meal.strInstructions || 'Instructions not available.'}</p>`}
          </div>

          ${ytId ? `
          <div class="youtube-wrap" style="margin-top:36px;">
            <h3 style="font-family:'Playfair Display',serif; margin-bottom:16px; color:var(--charcoal);">📺 Video Guide</h3>
            <iframe src="https://www.youtube.com/embed/${ytId}" allowfullscreen loading="lazy" style="width:100%;aspect-ratio:16/9;border-radius:var(--radius);border:none;"></iframe>
          </div>` : ''}
        </div>
      </div>
    </div>`;

    document.getElementById('detail-root').innerHTML = html;
    applyTranslations();

    // Set modal meal name
    document.getElementById('modal-meal-name').textContent = meal.strMeal;
  }

  const CSRF = document.querySelector('meta[name="csrf-token"]').content;

  // ── DB API helpers ────────────────────────────────────────────
  async function apiPost(url, body) {
    const res = await fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
      body: JSON.stringify(body),
    });
    return res.json();
  }

  async function apiDelete(url) {
    const res = await fetch(url, {
      method: 'DELETE',
      headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    });
    return res.json();
  }

  // ── Saved Recipes ─────────────────────────────────────────────
  let _savedIds = new Set();
  let _savedMap = {}; // mealdb_id -> DB record id

  async function loadSavedState() {
    try {
      const res = await fetch('/api/saved-recipes', { headers: { 'Accept': 'application/json' } });
      const data = await res.json();
      _savedIds = new Set(data.map(r => String(r.mealdb_id)));
      _savedMap = Object.fromEntries(data.map(r => [String(r.mealdb_id), r.id]));
    } catch { _savedIds = new Set(); }
  }

  function isRecipeSaved(id) { return _savedIds.has(String(id)); }

  async function toggleSaveRecipe() {
    if (!currentMeal) return;
    const id = String(currentMeal.idMeal);
    if (isRecipeSaved(id)) {
      const dbId = _savedMap[id];
      await apiDelete(`/api/saved-recipes/${dbId}`);
      _savedIds.delete(id);
      delete _savedMap[id];
      showToast(`"${currentMeal.strMeal}" removed from saved`, 'info');
    } else {
      const data = await apiPost('/api/saved-recipes', {
        mealdb_id: id,
        name: currentMeal.strMeal,
        thumbnail: currentMeal.strMealThumb || '',
        category: currentMeal.strCategory || '',
        area: currentMeal.strArea || '',
      });
      _savedIds.add(id);
      _savedMap[id] = data.id;
      showToast(`"${currentMeal.strMeal}" saved! ✓`, 'success');
    }
    updateSaveButton();
  }

  // ── Favorite Recipes ──────────────────────────────────────────
  let _favIds = new Set();
  let _favMap = {};

  async function loadFavoriteState() {
    try {
      const res = await fetch('/api/favorite-recipes', { headers: { 'Accept': 'application/json' } });
      const data = await res.json();
      _favIds = new Set(data.map(r => String(r.mealdb_id)));
      _favMap = Object.fromEntries(data.map(r => [String(r.mealdb_id), r.id]));
    } catch { _favIds = new Set(); }
  }

  function isFavoriteRecipe(id) { return _favIds.has(String(id)); }

  async function toggleFavoriteRecipe() {
    if (!currentMeal) return;
    const id = String(currentMeal.idMeal);
    if (isFavoriteRecipe(id)) {
      const dbId = _favMap[id];
      await apiDelete(`/api/favorite-recipes/${dbId}`);
      _favIds.delete(id);
      delete _favMap[id];
      showToast(`"${currentMeal.strMeal}" removed from favorites`, 'info');
    } else {
      const data = await apiPost('/api/favorite-recipes', {
        mealdb_id: id,
        name: currentMeal.strMeal,
        thumbnail: currentMeal.strMealThumb || '',
        category: currentMeal.strCategory || '',
        area: currentMeal.strArea || '',
      });
      _favIds.add(id);
      _favMap[id] = data.id;
      showToast(`"${currentMeal.strMeal}" added to favorites! ♥`, 'success');
    }
    updateSaveButton();
  }

  function updateSaveButton() {
    const btn = document.getElementById('saveRecipeBtn');
    if (btn && currentMeal) {
      const saved = isRecipeSaved(currentMeal.idMeal);
      btn.classList.toggle('saved', saved);
      btn.innerHTML = `<span class="save-icon">${saved ? '✓' : '＋'}</span><span>${saved ? 'Saved Recipe' : 'Save Recipe'}</span>`;
    }
    const favoriteBtn = document.getElementById('favoriteRecipeBtn');
    if (favoriteBtn && currentMeal) {
      const fav = isFavoriteRecipe(currentMeal.idMeal);
      favoriteBtn.classList.toggle('saved', fav);
      favoriteBtn.innerHTML = `<span class="save-icon">${fav ? '♥' : '♡'}</span><span>${fav ? 'Favorited' : 'Add Favorite'}</span>`;
    }
  }

  function closePlannerModal() {
    document.getElementById('plannerModal').classList.remove('open');
  }
  document.getElementById('plannerModal').addEventListener('click', (e) => {
    if (e.target === e.currentTarget) closePlannerModal();
  });

  // ── Add to Planner ────────────────────────────────────────────
  async function confirmAddToPlanner() {
    const dayIdx  = parseInt(document.getElementById('modal-day').value);
    const mealIdx = parseInt(document.getElementById('modal-mealtype').value);
    try {
      await apiPost('/api/meal-plans', {
        day_index:  dayIdx,
        meal_index: mealIdx,
        mealdb_id:  String(currentMeal.idMeal),
        name:       currentMeal.strMeal,
        thumbnail:  currentMeal.strMealThumb || '',
      });
      closePlannerModal();
      showToast(`"${currentMeal.strMeal}" added to planner! 🎉`, 'success');
    } catch (e) {
      showToast('Could not add to planner. Please try again.', 'error');
    }
  }

  loadDetail();
</script>
@endsection
