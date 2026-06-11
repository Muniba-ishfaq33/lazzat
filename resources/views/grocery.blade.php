@extends('layouts.app')

@section('title', 'Grocery List | Lazzat لذّت')

@section('content')
<div class="page-top">
  <div class="section-header" style="padding:40px 5% 0;">
    <span class="section-tag">Shopping</span>
    <h1 class="section-title" data-t="grocery-title">Grocery List</h1>
    <p class="section-sub" data-t="grocery-sub">Auto-generated from your meal plan</p>
    <div style="display:flex;gap:12px;justify-content:center;margin-top:20px;flex-wrap:wrap;">
      <button class="btn-primary" onclick="window.print()">🖨️ <span data-t="grocery-print">Print</span></button>
      <button style="background:var(--cream-dark);border:none;border-radius:10px;padding:10px 20px;cursor:pointer;font-family:inherit;" onclick="clearGrocery()">🗑 <span data-t="grocery-clear">Clear List</span></button>
    </div>
  </div>

  <section class="section" style="padding-top:28px; max-width:700px; margin:0 auto;">
    <!-- Progress -->
    <div class="grocery-progress" id="grocery-progress" style="display:none;">
      <div style="display:flex;justify-content:space-between;align-items:center;">
        <span style="font-weight:700;color:var(--charcoal);" id="progress-text">0 items bought</span>
        <span style="color:var(--warm-gray);font-size:0.88rem;" id="progress-pct">0%</span>
      </div>
      <div class="progress-bar-wrap"><div class="progress-bar-fill" id="progress-bar" style="width:0%"></div></div>
    </div>

    <div class="grocery-list" id="grocery-list"></div>

    <div class="empty-state" id="grocery-empty" style="display:none;">
      <div class="empty-state-icon">🛒</div>
      <div class="empty-state-msg" data-t="grocery-empty">Your grocery list is empty. Add meals to your planner first!</div>
      <a href="/planner" class="btn-primary" style="display:inline-flex;margin-top:20px;">Go to Planner →</a>
    </div>
  </section>
</div>

<footer class="footer">
  <div class="footer-bottom" data-t="footer-copy">© 2025 Lazzat · Made with ❤️ for Pakistani food lovers</div>
</footer>

<script src="{{ asset('js/translations.js') }}"></script>
<script src="{{ asset('js/navbar.js') }}"></script>
<script>
  injectNavbar('grocery');

  const CSRF = document.querySelector('meta[name="csrf-token"]').content;
  let groceryItems = []; // [{dbId, name, measure, source, checked}]

  async function apiPatch(url, body) {
    return fetch(url, {
      method: 'PATCH',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
      body: JSON.stringify(body),
    });
  }

  async function apiDelete(url) {
    return fetch(url, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' } });
  }

  async function loadGrocery() {
    try {
      const res = await fetch('/api/grocery-items', { headers: { 'Accept': 'application/json' } });
      groceryItems = await res.json();
    } catch { groceryItems = []; }
    renderGrocery();
  }

  function renderGrocery() {
    const list = document.getElementById('grocery-list');
    const empty = document.getElementById('grocery-empty');
    const progress = document.getElementById('grocery-progress');

    if (!groceryItems.length) {
      list.innerHTML = '';
      empty.style.display = 'block';
      progress.style.display = 'none';
      return;
    }
    empty.style.display = 'none';
    progress.style.display = 'block';

    // Deduplicate by ingredient name
    const seen = new Map();
    groceryItems.forEach(item => {
      const key = item.name.toLowerCase();
      if (!seen.has(key)) {
        seen.set(key, { ...item, sources: [item.source], dbIds: [item.id] });
      } else {
        seen.get(key).sources.push(item.source);
        seen.get(key).dbIds.push(item.id);
      }
    });
    const deduped = [...seen.values()];

    list.innerHTML = deduped.map((item, i) => `
      <div class="grocery-item ${item.checked ? 'checked' : ''}" id="item-${i}">
        <div class="grocery-checkbox ${item.checked ? 'checked' : ''}" onclick="toggleItem(${i})">
          ${item.checked ? '✓' : ''}
        </div>
        <div class="grocery-name">${item.measure ? `<span style="color:var(--saffron);font-weight:700;">${item.measure}</span> ` : ''}${item.name}</div>
        <div class="grocery-source">📍 ${[...new Set(item.sources)].filter(Boolean).slice(0,2).join(', ')}</div>
        <button onclick="deleteItem(${i})" style="margin-left:auto;background:none;border:none;color:var(--error);cursor:pointer;font-size:1rem;">✕</button>
      </div>`).join('');

    updateProgress(deduped);
  }

  function updateProgress(deduped) {
    const total = deduped.length;
    const bought = deduped.filter(item => item.checked).length;
    const pct = total ? Math.round((bought/total)*100) : 0;
    document.getElementById('progress-text').textContent = `${bought} / ${total} ${t('grocery-progress')}`;
    document.getElementById('progress-pct').textContent = `${pct}%`;
    document.getElementById('progress-bar').style.width = pct + '%';
    if (pct === 100 && total > 0) showToast('All items bought! 🎉', 'success');
  }

  async function toggleItem(i) {
    const deduped = getDedupedList();
    const item = deduped[i];
    if (!item) return;
    const newChecked = !item.checked;
    // Update all DB records with this name
    await Promise.all(item.dbIds.map(dbId => apiPatch(`/api/grocery-items/${dbId}`, { checked: newChecked })));
    // Update local state
    groceryItems.forEach(g => { if (g.name.toLowerCase() === item.name.toLowerCase()) g.checked = newChecked; });
    renderGrocery();
  }

  async function deleteItem(i) {
    const deduped = getDedupedList();
    const item = deduped[i];
    if (!item) return;
    await Promise.all(item.dbIds.map(dbId => apiDelete(`/api/grocery-items/${dbId}`)));
    groceryItems = groceryItems.filter(g => g.name.toLowerCase() !== item.name.toLowerCase());
    renderGrocery();
    showToast('Item removed', 'info');
  }

  function getDedupedList() {
    const seen = new Map();
    groceryItems.forEach(item => {
      const key = item.name.toLowerCase();
      if (!seen.has(key)) seen.set(key, { ...item, sources: [item.source], dbIds: [item.id] });
      else { seen.get(key).sources.push(item.source); seen.get(key).dbIds.push(item.id); }
    });
    return [...seen.values()];
  }

  async function clearGrocery() {
    await Promise.all(groceryItems.map(g => apiDelete(`/api/grocery-items/${g.id}`)));
    groceryItems = [];
    renderGrocery();
    showToast('Grocery list cleared', 'info');
  }

  // Init — load from DB
  loadGrocery();
</script>
@endsection
