@extends('layouts.app')

@section('title', 'Meal Planner | Lazzat لذّت')

@section('content')
<div class="page-top">
  <div class="section-header" style="padding:40px 5% 0;">
    <span class="section-tag">Plan Your Week</span>
    <h1 class="section-title" data-t="planner-title">Weekly Meal Planner</h1>
    <p class="section-sub" data-t="planner-sub">Plan your meals for the whole week</p>
    <div style="display:flex; gap:12px; justify-content:center; margin-top:20px; flex-wrap:wrap;">
      <button class="btn-primary" onclick="generateGrocery()">🛒 <span data-t="planner-grocery">Generate Grocery List</span></button>
      <button style="background:var(--cream-dark);border:1px solid var(--cream-dark);border-radius:10px;padding:10px 20px;font-size:0.9rem;cursor:pointer;font-family:inherit;" onclick="clearAll()" data-t="planner-clear">🗑 Clear All</button>
    </div>
  </div>

  <section class="section" style="padding-top:28px;">
    <div class="planner-grid" id="planner-grid"></div>
  </section>
</div>

<!-- Recipe Picker Modal -->
<div class="modal-overlay" id="pickerModal">
  <div class="modal" style="max-width:600px;">
    <div class="modal-header">
      <h3 class="modal-title">Pick a Recipe</h3>
      <button class="modal-close" onclick="closePicker()">✕</button>
    </div>
    <div class="search-box" style="margin-bottom:16px;">
      <span class="search-icon">🔍</span>
      <input type="text" id="picker-search" placeholder="Search recipes..." oninput="pickerSearch(this.value)" style="width:100%;padding:10px 16px 10px 40px;border:2px solid var(--cream-dark);border-radius:10px;font-size:0.9rem;font-family:inherit;outline:none;">
    </div>
    <div id="picker-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:12px;max-height:380px;overflow-y:auto;"></div>
  </div>
</div>

<footer class="footer">
  <div class="footer-bottom" data-t="footer-copy">© 2025 Lazzat · Made with ❤️ for Pakistani food lovers</div>
</footer>

<script src="{{ asset('js/translations.js') }}"></script>
<script src="{{ asset('js/navbar.js') }}"></script>
<script>
  injectNavbar('planner');

  const DAYS_EN = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
  const DAYS_UR = ['پیر','منگل','بدھ','جمعرات','جمعہ','ہفتہ','اتوار'];
  const MEALS_EN = ['Breakfast','Lunch','Dinner'];
  const MEALS_UR = ['ناشتہ','دوپہر','رات'];

  let allMeals = [];
  let pickerTarget = null;
  let pickerDebounce = null;
  let _planDb = {}; // { "di-mi": { dbId, id, name, thumb } }

  const CSRF = document.querySelector('meta[name="csrf-token"]').content;

  async function apiPost(url, body) {
    const res = await fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
      body: JSON.stringify(body),
    });
    return res.json();
  }

  async function apiDelete(url) {
    return fetch(url, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' } });
  }

  // Load planner from DB
  async function loadPlanFromDb() {
    try {
      const res = await fetch('/api/meal-plans', { headers: { 'Accept': 'application/json' } });
      const data = await res.json();
      _planDb = {};
      data.forEach(mp => {
        _planDb[`${mp.day_index}-${mp.meal_index}`] = {
          dbId: mp.id, id: mp.mealdb_id, name: mp.name, thumb: mp.thumbnail || ''
        };
      });
    } catch { _planDb = {}; }
  }

  function getDays() { return currentLang === 'ur' ? DAYS_UR : DAYS_EN; }
  function getMeals() { return currentLang === 'ur' ? MEALS_UR : MEALS_EN; }

  function renderPlanner() {
    const days = getDays();
    const meals = getMeals();
    const grid = document.getElementById('planner-grid');
    grid.innerHTML = days.map((day, di) => `
      <div class="day-col">
        <div class="day-header">${day}</div>
        <div class="day-slots">
          ${meals.map((meal, mi) => {
            const entry = _planDb[`${di}-${mi}`];
            const thumb = entry ? (entry.thumb.startsWith('http') ? entry.thumb : '') : '';
            return `<div class="meal-slot ${entry ? 'filled' : ''}" onclick="openPicker(${di},${mi})">
              <span class="meal-slot-label">${meal}</span>
              ${entry ? `
                <img src="${thumb}" alt="${entry.name}" style="width:100%;height:50px;object-fit:cover;border-radius:6px;margin:4px 0;" onerror="this.style.display='none'">
                <span class="meal-slot-name">${entry.name.length > 18 ? entry.name.slice(0,18)+'...' : entry.name}</span>
                <button onclick="event.stopPropagation();removeMeal(${di},${mi})" style="font-size:0.65rem;color:var(--error);background:none;border:none;cursor:pointer;margin-top:2px;">✕ remove</button>
              ` : `<span style="font-size:0.7rem;color:var(--warm-gray);">${currentLang==='ur'?'شامل کریں':'+ Add'}</span>`}
            </div>`;
          }).join('')}
        </div>
      </div>`).join('');
  }

  async function removeMeal(di, mi) {
    const key = `${di}-${mi}`;
    const entry = _planDb[key];
    if (!entry) return;
    await apiDelete(`/api/meal-plans/${entry.dbId}`);
    delete _planDb[key];
    renderPlanner();
    showToast('Meal removed', 'info');
  }

  async function clearAll() {
    const deletes = Object.values(_planDb).map(e => apiDelete(`/api/meal-plans/${e.dbId}`));
    await Promise.all(deletes);
    _planDb = {};
    renderPlanner();
    showToast('Planner cleared', 'info');
  }

  function openPicker(di, mi) {
    pickerTarget = {di, mi};
    document.getElementById('pickerModal').classList.add('open');
    document.getElementById('picker-search').value = '';
    renderPickerGrid(allMeals.slice(0,20));
  }
  function closePicker() {
    document.getElementById('pickerModal').classList.remove('open');
    pickerTarget = null;
  }
  document.getElementById('pickerModal').addEventListener('click', e => { if(e.target===e.currentTarget) closePicker(); });

  function renderPickerGrid(meals) {
    document.getElementById('picker-grid').innerHTML = meals.length
      ? meals.map(m => `
        <div onclick="selectMeal('${m.idMeal}','${m.strMeal.replace(/'/g,"\\'")}','${m.strMealThumb || ''}')"
          style="background:var(--cream);border-radius:10px;overflow:hidden;cursor:pointer;transition:all 0.2s;border:2px solid transparent;"
          onmouseover="this.style.borderColor='var(--saffron)'" onmouseout="this.style.borderColor='transparent'">
          <img src="${m.strMealThumb || ''}" style="width:100%;height:80px;object-fit:cover;" onerror="this.style.display='none'">
          <div style="padding:8px;font-size:0.8rem;font-weight:600;color:var(--charcoal);line-height:1.3;">${m.strMeal}</div>
        </div>`).join('')
      : '<div style="grid-column:1/-1;text-align:center;color:var(--warm-gray);padding:20px;">No recipes found</div>';
  }

  function pickerSearch(q) {
    clearTimeout(pickerDebounce);
    if (!q.trim()) { renderPickerGrid(allMeals.slice(0,20)); return; }
    pickerDebounce = setTimeout(async () => {
      const results = await searchMeals(q);
      renderPickerGrid(results.slice(0,20));
    }, 400);
  }

  async function selectMeal(id, name, thumb) {
    if (!pickerTarget) return;
    const { di, mi } = pickerTarget;
    try {
      const data = await apiPost('/api/meal-plans', {
        day_index: di, meal_index: mi,
        mealdb_id: id, name, thumbnail: thumb,
      });
      _planDb[`${di}-${mi}`] = { dbId: data.id, id, name, thumb };
      closePicker();
      renderPlanner();
      showToast(`"${name}" added! 🎉`, 'success');
    } catch (e) {
      showToast('Could not save meal. Try again.', 'error');
    }
  }

  async function generateGrocery() {
    const mealEntries = Object.values(_planDb);
    if (!mealEntries.length) { showToast('Add some meals to your planner first!', 'error'); return; }
    showToast('Generating grocery list...', 'info');
    try {
      const details = await Promise.all(mealEntries.slice(0,7).map(e => fetchMealDetail(e.id)));
      // Post each ingredient to /api/grocery-items
      const groceryPosts = [];
      details.forEach(meal => {
        if (!meal) return;
        for (let i=1;i<=20;i++) {
          const name = meal[`strIngredient${i}`];
          const measure = meal[`strMeasure${i}`];
          if (name && name.trim()) {
            groceryPosts.push(apiPost('/api/grocery-items', {
              name: name.trim(), measure: measure ? measure.trim() : '', source: meal.strMeal, checked: false
            }));
          }
        }
      });
      await Promise.all(groceryPosts);
      showToast('Grocery list ready! Redirecting...', 'success');
      setTimeout(() => window.location.href = '/grocery', 1200);
    } catch(e) {
      showToast('Failed to generate list. Try again.', 'error');
    }
  }

  async function init() {
    await loadPlanFromDb();
    allMeals = await fetchPakistaniRecipes();
    renderPlanner();
  }
  init();
</script>
@endsection
