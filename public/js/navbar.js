// navbar.js - inject shared Laravel navbar
function injectNavbar(activePage) {

  // Check if user is logged in from localStorage (set by AuthController on login)
  const lazzatUser = (() => {
    try { return JSON.parse(localStorage.getItem('lazzat-user')); } catch { return null; }
  })();
  const isLoggedIn = !!(lazzatUser && lazzatUser.id);
  const userName = isLoggedIn ? (lazzatUser.name || '').split(' ')[0] : '';

  // Build the right nav action button
  const navActionBtn = isLoggedIn
    ? `<a class="btn-nav" href="/dashboard" style="display:inline-flex;align-items:center;gap:7px;" data-t="nav-dashboard">
         <span style="width:26px;height:26px;border-radius:50%;background:rgba(255,255,255,0.25);display:inline-flex;align-items:center;justify-content:center;font-size:0.8rem;font-weight:800;">
           ${userName.charAt(0).toUpperCase()}
         </span>
         ${userName || 'Dashboard'}
       </a>`
    : `<a class="btn-nav" href="/login" data-t="nav-login">Login</a>`;

  const mobileActionLink = isLoggedIn
    ? `<a href="/dashboard" data-t="nav-dashboard">🏠 Dashboard</a>`
    : `<a href="/login" data-t="nav-login">👤 Login</a>`;

  const html = `
  <nav class="navbar" id="navbar">
    <a class="nav-logo" href="/">
      <div class="nav-logo-img">🍽️</div>
      <div>
        <div class="nav-logo-text">Lazzat</div>
        <div class="nav-logo-urdu">لذّت</div>
      </div>
    </a>
    <ul class="nav-links">
      <li><a href="/" ${activePage==='home'?'class="active"':''} data-t="nav-home">Home</a></li>
      <li><a href="/recipes" ${activePage==='recipes'?'class="active"':''} data-t="nav-recipes">Recipes</a></li>
      <li><a href="/planner" ${activePage==='planner'?'class="active"':''} data-t="nav-planner">Meal Planner</a></li>
      <li><a href="/grocery" ${activePage==='grocery'?'class="active"':''} data-t="nav-grocery">Grocery List</a></li>
    </ul>
    <div class="nav-actions">
      <button class="lang-toggle" id="langToggle" data-t="lang-btn">🌐 اردو</button>
      ${navActionBtn}
    </div>
    <button class="hamburger" id="hamburger" aria-label="Menu">
      <span></span><span></span><span></span>
    </button>
  </nav>
  <div class="mobile-menu" id="mobileMenu">
    <a href="/" data-t="nav-home">🏠 Home</a>
    <a href="/recipes" data-t="nav-recipes">🍳 Recipes</a>
    <a href="/planner" data-t="nav-planner">📅 Meal Planner</a>
    <a href="/grocery" data-t="nav-grocery">🛒 Grocery List</a>
    ${mobileActionLink}
    <button class="lang-toggle" style="width:fit-content" data-t="lang-btn">🌐 اردو</button>
  </div>
  <div class="toast-container" id="toast-container"></div>
  `;
  const wrapper = document.createElement('div');
  wrapper.innerHTML = html;
  document.body.insertBefore(wrapper, document.body.firstChild);

  initNavbar();
  applyTranslations();
  document.querySelectorAll('.lang-toggle').forEach(btn => btn.addEventListener('click', toggleLang));
}