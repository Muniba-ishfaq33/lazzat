@extends('layouts.app')

@section('title', 'Dashboard | Lazzat لذّت')

@section('content')
<div class="dash-shell" id="dash-shell">
  <aside class="dash-sidebar">
    <a class="dash-brand" href="/">
      <span class="dash-brand-urdu">لذّت</span>
      <span data-i18n="brand">Lazzat</span>
    </a>

    <div class="dash-profile">
      <div class="dash-avatar" id="dash-avatar">U</div>
      <div>
        <strong id="dash-user-name">User</strong>
        <span data-i18n="welcomeBack">Welcome back</span>
      </div>
    </div>

    <nav class="dash-nav flowing-menu">
      <a class="active" href="/dashboard"><span>⌂</span><b data-i18n="dashboard">Dashboard</b></a>
      <a href="/recipes"><span>▣</span><b data-i18n="recipes">Recipes</b></a>
      <a href="/recipes#ingr"><span>♡</span><b data-i18n="suggestions">AI Suggestions</b></a>
      <a href="/planner"><span>□</span><b data-i18n="planner">Meal Planner</b></a>
      <a href="/grocery"><span>▱</span><b data-i18n="grocery">Grocery List</b></a>
      <a href="/"><span>←</span><b data-i18n="backHome">Back Home</b></a>
      <a href="#" id="logout-btn" onclick="doLogout(event)"><span>⎋</span><b data-i18n="logout">Log Out</b></a>
    </nav>

    <div class="dash-mode-card">
      <div>
        <strong data-i18n="language">Language</strong>
        <span data-i18n="languageHelp">Switch English and Urdu dashboard</span>
      </div>
      <button id="dash-lang-toggle" type="button">اردو</button>
    </div>
  </aside>

  <main class="dash-main">
    <header class="dash-topbar">
      <form class="dash-search" id="dash-search-form">
        <span>⌕</span>
        <input id="dash-search" type="search" data-i18n-placeholder="searchPlaceholder" placeholder="Search recipes, ingredients...">
      </form>
      <div class="dash-top-actions">
        <button class="dash-icon-btn" id="dash-lang-top" type="button">EN / اردو</button>
        <div class="dash-date">
          <strong id="dash-date-main">30 May 2026</strong>
          <span id="dash-date-day">Saturday</span>
        </div>
        <a class="dash-plan-btn" href="/planner" data-i18n="planMeal">+ Plan a Meal</a>
      </div>
    </header>

    <section class="dash-greeting reveal-section">
      <div>
        <h1 id="dash-greet">Good morning</h1>
        <p data-i18n="greetingSub">Let's plan something delicious today.</p>
      </div>
    </section>

    <section class="dash-stat-grid reveal-section">
      <article class="dash-stat-card red tilt-card" onclick="openSavedModal('saved')" style="cursor:pointer;" title="View saved recipes">
        <span>▤</span>
        <strong id="stat-saved">0</strong>
        <p data-i18n="savedRecipes">Saved Recipes</p>
        <small data-i18n="fromCollection">Your collection</small>
      </article>
      <article class="dash-stat-card gold tilt-card" onclick="openSavedModal('favorites')" style="cursor:pointer;" title="View favorite recipes">
        <span>♡</span>
        <strong id="stat-favorites">0</strong>
        <p data-i18n="favoriteMeals">Favorite Meals</p>
        <small data-i18n="mealsYouLove">Meals you love</small>
      </article>
      <article class="dash-stat-card green tilt-card" onclick="window.location.href='/planner'" style="cursor:pointer;" title="Go to planner">
        <span>□</span>
        <strong id="stat-meals">0</strong>
        <p data-i18n="mealsPlanned">Meals Planned</p>
        <small data-i18n="thisWeek">This week</small>
      </article>
      <article class="dash-stat-card purple tilt-card" onclick="window.location.href='/grocery'" style="cursor:pointer;" title="Go to grocery list">
        <span>▱</span>
        <strong id="stat-grocery">0</strong>
        <p data-i18n="groceryItems">Grocery Items</p>
        <small data-i18n="onYourList">On your list</small>
      </article>
      <article class="dash-stat-card blue tilt-card">
        <span>↗</span>
        <strong id="stat-progress">0%</strong>
        <p data-i18n="weeklyProgress">Weekly Progress</p>
        <small id="stat-progress-sub">Start planning</small>
      </article>
    </section>

    <!-- Saved / Favorites Modal -->
    <div class="modal-overlay" id="saved-modal-overlay" onclick="if(event.target===this)closeSavedModal()">
      <div class="modal" style="max-width:600px;">
        <div class="modal-header">
          <h2 class="modal-title" id="saved-modal-title">Saved Recipes</h2>
          <button class="modal-close" onclick="closeSavedModal()">×</button>
        </div>
        <div id="saved-modal-body" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:14px;max-height:60vh;overflow-y:auto;padding-top:4px;"></div>
      </div>
    </div>

    <section class="dash-grid">
      <article class="dash-panel dash-today tilt-card reveal-section">
        <div class="dash-panel-head">
          <h2 data-i18n="todaysMeals">Today's Meals</h2>
        </div>
        <div id="today-meals" class="dash-meal-list"></div>
        <a class="dash-outline-btn" href="/planner" data-i18n="viewPlanner">View Full Planner →</a>
      </article>

      <article class="dash-panel dash-week tilt-card reveal-section">
        <div class="dash-panel-head">
          <h2 data-i18n="weekPlan">This Week's Plan</h2>
          <a href="/planner" data-i18n="viewPlanner">View Full Planner →</a>
        </div>
        <div id="week-plan" class="dash-week-table"></div>
        <div class="dash-quote">
          <strong>“</strong>
          <span data-i18n="quote">The secret of good food is in the love with which it is prepared.</span>
        </div>
      </article>

      <article class="dash-panel dash-grocery-panel tilt-card reveal-section">
        <div class="dash-panel-head">
          <h2 data-i18n="grocery">Grocery List</h2>
          <a href="/grocery" data-i18n="viewAll">View All →</a>
        </div>
        <div class="dash-grocery-progress">
          <div class="dash-ring" id="grocery-ring">0%</div>
          <div>
            <strong id="grocery-count">0 items</strong>
            <span data-i18n="completed">completed</span>
            <div class="dash-mini-bar"><i id="grocery-bar"></i></div>
          </div>
        </div>
        <div id="grocery-preview" class="dash-grocery-items"></div>
      </article>

      <article class="dash-panel dash-suggestions tilt-card reveal-section">
        <div class="dash-panel-head">
          <h2 data-i18n="suggestions">AI Suggestions</h2>
          <span class="dash-badge" data-i18n="dynamic">Dynamic</span>
        </div>
        <p data-i18n="basedOn">Based on your grocery and planner data</p>
        <div id="dash-tags" class="dash-tags"></div>
        <div id="suggestion-grid" class="dash-suggestion-grid"></div>
        <a class="dash-outline-btn" href="/recipes#ingr" data-i18n="moreSuggestions">Get More Suggestions ✨</a>
      </article>

      <article class="dash-panel dash-activity tilt-card reveal-section">
        <div class="dash-panel-head">
          <h2 data-i18n="recentActivity">Recent Activity</h2>
        </div>
        <div id="activity-list"></div>
      </article>
    </section>
  </main>
</div>

<script src="{{ asset('js/translations.js') }}"></script>
<script>
  const dashText = {
    en: {
      brand: 'Lazzat', welcomeBack: 'Welcome back', dashboard: 'Dashboard', recipes: 'Recipes',
      suggestions: 'AI Suggestions', planner: 'Meal Planner', grocery: 'Grocery List', backHome: 'Back Home', logout: 'Log Out',
      language: 'Language', languageHelp: 'Switch English and Urdu dashboard', searchPlaceholder: 'Search recipes, ingredients...',
      planMeal: '+ Plan a Meal', greetingSub: "Let's plan something delicious today.", savedRecipes: 'Saved Recipes',
      fromCollection: 'Your collection', favoriteMeals: 'Favorite Meals', mealsYouLove: 'Meals you love',
      mealsPlanned: 'Meals Planned', thisWeek: 'This week', groceryItems: 'Grocery Items', onYourList: 'On your list',
      weeklyProgress: 'Weekly Progress', todaysMeals: "Today's Meals", viewPlanner: 'View Full Planner →',
      weekPlan: "This Week's Plan", quote: 'The secret of good food is in the love with which it is prepared.',
      viewAll: 'View All →', completed: 'completed', dynamic: 'Dynamic', basedOn: 'Based on your grocery and planner data',
      moreSuggestions: 'Get More Suggestions ✨', recentActivity: 'Recent Activity', noMeals: 'No meals planned yet',
      noGroceries: 'No grocery items yet', noActivity: 'Your activity will appear here', startPlanning: 'Start planning',
      greatGoing: 'Great going!', goodMorning: 'Good morning', planned: 'Planned', breakfast: 'Breakfast',
      lunch: 'Lunch', dinner: 'Dinner'
    },
    ur: {
      brand: 'لذّت', welcomeBack: 'خوش آمدید', dashboard: 'ڈیش بورڈ', recipes: 'ریسیپیز',
      suggestions: 'AI تجاویز', planner: 'میل پلانر', grocery: 'گروسری لسٹ', backHome: 'واپس ہوم', logout: 'لاگ آؤٹ',
      language: 'زبان', languageHelp: 'انگریزی اور اردو ڈیش بورڈ بدلیں', searchPlaceholder: 'ریسیپی یا اجزا تلاش کریں...',
      planMeal: '+ کھانا پلان کریں', greetingSub: 'آج کچھ مزیدار پلان کرتے ہیں۔', savedRecipes: 'محفوظ ریسیپیز',
      fromCollection: 'آپ کی کلیکشن', favoriteMeals: 'پسندیدہ کھانے', mealsYouLove: 'آپ کی پسند',
      mealsPlanned: 'پلان شدہ کھانے', thisWeek: 'اس ہفتے', groceryItems: 'گروسری آئٹمز', onYourList: 'آپ کی لسٹ میں',
      weeklyProgress: 'ہفتہ وار پیش رفت', todaysMeals: 'آج کے کھانے', viewPlanner: 'مکمل پلانر دیکھیں ←',
      weekPlan: 'اس ہفتے کا پلان', quote: 'اچھے کھانے کا راز محبت سے پکانے میں ہے۔',
      viewAll: 'سب دیکھیں ←', completed: 'مکمل', dynamic: 'ڈائنامک', basedOn: 'آپ کی گروسری اور پلانر ڈیٹا کے مطابق',
      moreSuggestions: 'مزید تجاویز ✨', recentActivity: 'حالیہ سرگرمی', noMeals: 'ابھی کوئی کھانا پلان نہیں',
      noGroceries: 'ابھی کوئی گروسری آئٹم نہیں', noActivity: 'آپ کی سرگرمی یہاں آئے گی', startPlanning: 'پلاننگ شروع کریں',
      greatGoing: 'بہت خوب!', goodMorning: 'صبح بخیر', planned: 'پلان شدہ', breakfast: 'ناشتہ',
      lunch: 'دوپہر', dinner: 'رات'
    }
  };

  const storageArrays = (...keys) => keys.flatMap((key) => {
    const value = store.get(key);
    if (Array.isArray(value)) return value;
    if (value && typeof value === 'object') return Object.values(value);
    return [];
  });

  // Wrap in async IIFE so we can use await at top level
  (async () => {  // ── Real auth: load user from server session ──────────────────
  async function loadAuthUser() {
    try {
      const res  = await fetch('/auth/me', { headers: { 'Accept': 'application/json' } });
      const data = await res.json();
      if (data.authenticated) {
        // Sync to localStorage so rest of page JS can use it
        store.set('user', { id: data.user.id, name: data.user.name, email: data.user.email });
        return data.user;
      } else {
        // Session expired — redirect to login
        window.location.href = '/login';
        return null;
      }
    } catch (e) {
      return store.get('user') || { name: 'Guest' };
    }
  }

  async function doLogout(e) {
    e.preventDefault();
    try {
      await fetch('/auth/logout', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Accept': 'application/json',
        },
      });
    } catch (_) {}
    store.set('user', null);
    window.location.href = '/login';
  }

  const user = (await loadAuthUser()) || store.get('user') || { name: 'Guest' };

  // ── Load all data from DB (scoped to logged-in user) ──────────
  const CSRF = document.querySelector('meta[name="csrf-token"]').content;

  async function dbGet(url) {
    try {
      const res = await fetch(url, {
        credentials: 'same-origin',
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
      });
      if (!res.ok) {
        console.error(`dbGet ${url} failed:`, res.status, await res.text());
        return [];
      }
      const data = await res.json();
      return Array.isArray(data) ? data : [];
    } catch(e) {
      console.error(`dbGet ${url} error:`, e);
      return [];
    }
  }

  const [savedRecipes, favoriteRecipes, mealPlansDb, groceryDb] = await Promise.all([
    dbGet('/api/saved-recipes'),
    dbGet('/api/favorite-recipes'),
    dbGet('/api/meal-plans'),
    dbGet('/api/grocery-items'),
  ]);

  // Debug — remove after confirming it works
  console.log('DB data loaded:', {
    saved: savedRecipes.length,
    favorites: favoriteRecipes.length,
    mealPlans: mealPlansDb.length,
    grocery: groceryDb.length
  });

  // Convert DB meal plans array → planner object shape { dayIndex: { mealIndex: {...} } }
  const plan = {};
  mealPlansDb.forEach(mp => {
    if (!plan[mp.day_index]) plan[mp.day_index] = {};
    plan[mp.day_index][mp.meal_index] = { idMeal: mp.mealdb_id, strMeal: mp.name, strMealThumb: mp.thumbnail };
  });

  // Convert DB grocery array → format expected by dashboard
  const grocery = groceryDb.map(g => ({ name: g.name, measure: g.measure, source: g.source, checked: !!g.checked }));
  const checkedItems = groceryDb.filter(g => g.checked).map(g => g.name.toLowerCase());
  const mealLabelsEn = ['Breakfast', 'Lunch', 'Dinner'];
  const mealLabelsUr = ['ناشتہ', 'دوپہر', 'رات'];
  const daysEn = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
  const daysUr = ['پیر', 'منگل', 'بدھ', 'جمعرات', 'جمعہ', 'ہفتہ', 'اتوار'];

  let dashLang = localStorage.getItem('lazzat-lang') || 'en';

  function flatMeals() {
    const meals = [];
    Object.entries(plan).forEach(([day, dayMeals]) => {
      Object.entries(dayMeals || {}).forEach(([meal, value]) => meals.push({ day: Number(day), meal: Number(meal), ...value }));
    });
    return meals;
  }

  function normalizedCheckedItems() {
    return checkedItems.map(item => String(item).toLowerCase());
  }

  function dedupedGroceryItems() {
    const seen = new Map();
    grocery.forEach((item) => {
      if (!item || !item.name) return;
      const key = item.name.toLowerCase();
      if (!seen.has(key)) {
        seen.set(key, { ...item, nameKey: key, sources: item.source ? [item.source] : [] });
      } else if (item.source) {
        seen.get(key).sources.push(item.source);
      }
    });
    return [...seen.values()];
  }

  function isGroceryChecked(item, index) {
    const checked = normalizedCheckedItems();
    return item.checked || checked.includes(String(index)) || checked.includes(item.nameKey || String(item.name).toLowerCase());
  }

  function groceryDoneCount() {
    return dedupedGroceryItems().filter((item, index) => isGroceryChecked(item, index)).length;
  }

  function applyDashboardLanguage() {
    const dict = dashText[dashLang];
    document.documentElement.lang = dashLang === 'ur' ? 'ur' : 'en';
    document.getElementById('dash-shell').classList.toggle('dash-rtl', dashLang === 'ur');
    document.querySelectorAll('[data-i18n]').forEach((el) => { el.textContent = dict[el.dataset.i18n] || el.textContent; });
    document.querySelectorAll('[data-i18n-placeholder]').forEach((el) => { el.placeholder = dict[el.dataset.i18nPlaceholder] || el.placeholder; });
    document.getElementById('dash-lang-toggle').textContent = dashLang === 'ur' ? 'EN' : 'اردو';
    document.getElementById('dash-lang-top').textContent = dashLang === 'ur' ? 'EN' : 'اردو';
    renderDashboard();
  }

  function setLang(next) {
    dashLang = next;
    localStorage.setItem('lazzat-lang', dashLang);
    applyDashboardLanguage();
  }

  function renderDashboard() {
    const dict = dashText[dashLang];
    const meals = flatMeals();
    const displayName = user.name ? user.name.charAt(0).toUpperCase() + user.name.slice(1) : 'Guest';
    const firstName = displayName.split(' ')[0];
    const now = new Date();
    const groceryList = dedupedGroceryItems();
    const groceryDone = groceryDoneCount();
    const mealProgress = Math.round((meals.length / 21) * 100);
    const groceryProgress = groceryList.length ? Math.round((groceryDone / groceryList.length) * 100) : 0;
    const totalProgress = Math.round((mealProgress + groceryProgress) / 2);

    document.getElementById('dash-user-name').textContent = displayName;
    document.getElementById('dash-avatar').textContent = displayName.charAt(0).toUpperCase();
    document.getElementById('dash-greet').textContent = `${dict.goodMorning}, ${firstName}`;
    document.getElementById('dash-date-main').textContent = now.toLocaleDateString(dashLang === 'ur' ? 'ur-PK' : 'en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
    document.getElementById('dash-date-day').textContent = now.toLocaleDateString(dashLang === 'ur' ? 'ur-PK' : 'en-US', { weekday: 'long' });

    document.getElementById('stat-saved').textContent = savedRecipes.length;
    document.getElementById('stat-favorites').textContent = favoriteRecipes.length;
    document.getElementById('stat-meals').textContent = meals.length;
    document.getElementById('stat-grocery').textContent = groceryList.length;
    document.getElementById('stat-progress').textContent = `${totalProgress}%`;
    document.getElementById('stat-progress-sub').textContent = totalProgress ? dict.greatGoing : dict.startPlanning;
    document.getElementById('grocery-ring').textContent = `${groceryProgress}%`;
    document.getElementById('grocery-ring').style.setProperty('--progress', `${groceryProgress}%`);
    document.getElementById('grocery-bar').style.width = `${groceryProgress}%`;
    document.getElementById('grocery-count').textContent = `${groceryDone} / ${groceryList.length} ${dashLang === 'ur' ? 'آئٹمز' : 'items'}`;

    renderToday(meals, dict);
    renderWeek(dict);
    renderGrocery(dict);
    renderSuggestions(meals, dict);
    renderActivity(meals, dict);
  }

  function mealFromPlan(dayIndex, mealIndex) {
    return plan[dayIndex] && plan[dayIndex][mealIndex] ? plan[dayIndex][mealIndex] : null;
  }

  function renderToday(meals, dict) {
    const todayIndex = (new Date().getDay() + 6) % 7;
    const labels = dashLang === 'ur' ? mealLabelsUr : mealLabelsEn;
    const todays = [0, 1, 2].map((mealIndex) => mealFromPlan(todayIndex, mealIndex)).filter(Boolean);
    document.getElementById('today-meals').innerHTML = todays.length ? todays.map((meal, index) => `
      <div class="dash-meal-row" onclick="window.location.href='/recipe-detail?id=${meal.id || meal.idMeal}'">
        <span class="dash-meal-icon">${index === 2 ? '☾' : '☼'}</span>
        <div class="dash-meal-time"><strong>${labels[index]}</strong><small>${dict.planned}</small></div>
        <img src="${meal.thumb || meal.strMealThumb}/preview" alt="${meal.name || meal.strMeal}" onerror="this.style.display='none'">
        <div class="dash-meal-name"><strong>${meal.name || meal.strMeal}</strong><small>${dict.thisWeek}</small><span>${dict.planned}</span></div>
      </div>
    `).join('') : `<div class="dash-empty">${dict.noMeals}</div>`;
  }

  function renderWeek(dict) {
    const labels = dashLang === 'ur' ? mealLabelsUr : mealLabelsEn;
    const days = dashLang === 'ur' ? daysUr : daysEn;
    const rows = [0, 1, 2].map((mealIndex) => `
      <div class="dash-week-label">${labels[mealIndex]}</div>
      ${days.map((day, dayIndex) => {
        const meal = mealFromPlan(dayIndex, mealIndex);
        return meal
          ? `<a href="/recipe-detail?id=${meal.id || meal.idMeal}" class="dash-week-cell"><img src="${meal.thumb || meal.strMealThumb}/preview" alt="${meal.name || meal.strMeal}" onerror="this.style.display='none'"></a>`
          : `<div class="dash-week-cell empty">+</div>`;
      }).join('')}
    `).join('');
    document.getElementById('week-plan').innerHTML = `<div></div>${days.map(day => `<strong>${day}</strong>`).join('')}${rows}`;
  }

  function renderGrocery(dict) {
    const items = dedupedGroceryItems().slice(0, 6);
    document.getElementById('grocery-preview').innerHTML = items.length ? items.map((item, index) => {
      const done = isGroceryChecked(item, index);
      return `<div class="${done ? 'done' : ''}"><span>${done ? '✓' : ''}</span><strong>${item.name}</strong><em>${item.measure || ''}</em></div>`;
    }).join('') : `<div class="dash-empty">${dict.noGroceries}</div>`;
  }

  async function renderSuggestions(meals, dict) {
    const ingredientNames = dedupedGroceryItems().map(item => item.name).filter(Boolean).slice(0, 5);
    document.getElementById('dash-tags').innerHTML = ingredientNames.length
      ? ingredientNames.map(name => `<span>${name}</span>`).join('')
      : `<span>${dashLang === 'ur' ? 'اجزا شامل کریں' : 'Add grocery items'}</span>`;
    const grid = document.getElementById('suggestion-grid');
    const seed = ingredientNames[0] || meals[0]?.name || 'chicken';
    grid.innerHTML = `<div class="dash-empty">${dashLang === 'ur' ? 'تجاویز لوڈ ہو رہی ہیں...' : 'Loading suggestions...'}</div>`;
    try {
      const results = await searchMeals(seed);
      grid.innerHTML = (results || []).slice(0, 3).map(meal => `
        <a href="/recipe-detail?id=${meal.idMeal}" class="dash-suggestion">
          <img src="${meal.strMealThumb}/preview" alt="${meal.strMeal}" onerror="this.style.display='none'">
          <strong>${meal.strMeal}</strong>
          <span>★ 4.${Math.floor(Math.random() * 3) + 7}</span>
        </a>
      `).join('') || `<div class="dash-empty">${dict.noMeals}</div>`;
    } catch (error) {
      grid.innerHTML = `<div class="dash-empty">${dict.noMeals}</div>`;
    }
  }

  function renderActivity(meals, dict) {
    const rows = [];
    if (savedRecipes.length) rows.push(`${dashLang === 'ur' ? 'ریسیپی محفوظ کی' : 'Recipe saved'}<small>${savedRecipes[0].name || savedRecipes[0].strMeal || ''}</small>`);
    if (meals.length) rows.push(`${dashLang === 'ur' ? 'کھانا پلان کیا' : 'Meal planned'}<small>${meals[0].name || meals[0].strMeal}</small>`);
    const groceryList = dedupedGroceryItems();
    if (groceryList.length) rows.push(`${dashLang === 'ur' ? 'گروسری شامل کی' : 'Item added'}<small>${groceryList[0].name}</small>`);
    document.getElementById('activity-list').innerHTML = rows.length
      ? rows.map((row, index) => `<div class="dash-activity-row"><span>${index + 1}</span><strong>${row}</strong><em>${dashLang === 'ur' ? 'ابھی' : 'now'}</em></div>`).join('')
      : `<div class="dash-empty">${dict.noActivity}</div>`;
  }

  document.getElementById('dash-lang-toggle').addEventListener('click', () => setLang(dashLang === 'ur' ? 'en' : 'ur'));
  document.getElementById('dash-lang-top').addEventListener('click', () => setLang(dashLang === 'ur' ? 'en' : 'ur'));
  document.getElementById('dash-search-form').addEventListener('submit', (event) => {
    event.preventDefault();
    const query = document.getElementById('dash-search').value.trim();
    window.location.href = query ? `/recipes?q=${encodeURIComponent(query)}` : '/recipes';
  });

  // ── Saved / Favorites Modal ───────────────────────────────────
  function openSavedModal(type) {
    const overlay = document.getElementById('saved-modal-overlay');
    const title   = document.getElementById('saved-modal-title');
    const body    = document.getElementById('saved-modal-body');
    const list    = type === 'saved' ? savedRecipes : favoriteRecipes;
    const label   = type === 'saved' ? 'Saved Recipes' : 'Favorite Meals';

    title.textContent = `${label} (${list.length})`;

    if (!list.length) {
      body.innerHTML = `<div style="grid-column:1/-1;text-align:center;padding:40px 20px;color:var(--warm-gray);">
        <div style="font-size:2.5rem;margin-bottom:12px;">${type === 'saved' ? '📚' : '♡'}</div>
        <p style="font-weight:600;margin-bottom:8px;">No ${label.toLowerCase()} yet</p>
        <a href="/recipes" style="color:var(--saffron);font-weight:700;text-decoration:none;">Browse recipes →</a>
      </div>`;
    } else {
      body.innerHTML = list.map(r => {
        const thumb = r.thumbnail || r.strMealThumb || '';
        const name  = r.name || r.strMeal || 'Recipe';
        const id    = r.mealdb_id || r.idMeal || '';
        return `
        <div onclick="window.location.href='/recipe-detail?id=${id}'"
          style="background:var(--cream);border-radius:12px;overflow:hidden;cursor:pointer;border:2px solid transparent;transition:all 0.2s;"
          onmouseover="this.style.borderColor='var(--saffron)';this.style.transform='translateY(-3px)'"
          onmouseout="this.style.borderColor='transparent';this.style.transform='translateY(0)'">
          <img src="${thumb}" alt="${name}" style="width:100%;height:100px;object-fit:cover;display:block;"
            onerror="this.src='https://www.themealdb.com/images/media/meals/default.jpg'">
          <div style="padding:10px;">
            <div style="font-size:0.82rem;font-weight:700;color:var(--charcoal);line-height:1.3;
              white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${name}</div>
            <div style="font-size:0.72rem;color:var(--saffron);margin-top:4px;font-weight:600;">View Recipe →</div>
          </div>
        </div>`;
      }).join('');
    }

    overlay.classList.add('open');
  }

  function closeSavedModal() {
    document.getElementById('saved-modal-overlay').classList.remove('open');
  }

  applyDashboardLanguage();
  })(); // end async IIFE
</script>
@endsection
