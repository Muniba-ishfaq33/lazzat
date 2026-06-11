@extends('layouts.app')

@section('title', 'Login | Lazzat لذّت')

@section('content')
<div class="auth-page">
  <!-- Left Panel -->
  <div class="auth-left">
    <div class="auth-brand">
      <div class="auth-brand-logo">🍽️</div>
      <div class="auth-brand-name">Lazzat</div>
      <span class="auth-brand-urdu">لذّت</span>
      <p class="auth-brand-tag">Discover authentic Pakistani recipes.<br>Plan your week. Shop smarter.</p>
    </div>
  </div>

  <!-- Right Panel -->
  <div class="auth-right">
    <div class="auth-form-wrap">
      <!-- Language toggle -->
      <div style="display:flex;justify-content:flex-end;margin-bottom:24px;">
        <button class="lang-toggle" data-t="lang-btn">🌐 اردو</button>
      </div>

      <h2 class="auth-form-title" data-t="login-title">Welcome back</h2>
      <p class="auth-form-sub" data-t="login-sub">Log in to access your meal planner & saved recipes</p>

      <form id="loginForm" novalidate>
        <div class="form-group">
          <label class="form-label" data-t="label-email">Email Address</label>
          <input type="email" class="form-input" id="login-email" data-t="label-email" data-t-attr="placeholder" placeholder="Email Address" autocomplete="email">
          <div class="form-error" id="err-login-email" data-t="err-email">Please enter a valid email</div>
        </div>
        <div class="form-group">
          <label class="form-label" data-t="label-password">Password</label>
          <div class="password-wrap">
            <input type="password" class="form-input" id="login-password" data-t="label-password" data-t-attr="placeholder" placeholder="Password" autocomplete="current-password">
            <button type="button" class="toggle-pass" onclick="togglePass('login-password',this)">👁</button>
          </div>
          <div class="form-error" id="err-login-password" data-t="err-password">Password must be at least 6 characters</div>
        </div>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
          <label style="display:flex;align-items:center;gap:8px;font-size:0.88rem;cursor:pointer;">
            <input type="checkbox" style="accent-color:var(--saffron);"> Remember me
          </label>
          <a href="#" style="color:var(--saffron);font-size:0.88rem;text-decoration:none;font-weight:600;">Forgot password?</a>
        </div>
        <button type="submit" class="btn-auth" id="login-btn" data-t="btn-login">Log In</button>
      </form>

      <p class="auth-switch"><a href="/register" data-t="switch-to-register">Don't have an account? Sign up</a></p>

      <!-- Demo hint -->
      <div style="margin-top:24px;background:rgba(232,160,32,0.08);border:1px solid rgba(232,160,32,0.2);border-radius:10px;padding:14px;font-size:0.82rem;color:var(--warm-gray);">
        💡 <strong>Demo:</strong> Use any valid email and a 6+ character password to continue.
      </div>
    </div>
  </div>
</div>

<script src="{{ asset('js/translations.js') }}"></script>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    applyTranslations();
    document.querySelectorAll('.lang-toggle').forEach(btn => btn.addEventListener('click', toggleLang));
  });

  function showErr(id, show) {
    const el = document.getElementById(id);
    const input = el.previousElementSibling?.tagName === 'DIV' ? el.previousElementSibling.querySelector('input') : el.previousElementSibling;
    if (show) { el.classList.add('show'); if(input) input.classList.add('error'); }
    else { el.classList.remove('show'); if(input) input.classList.remove('error'); }
  }

  function validateEmail(email) { return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email); }

  function togglePass(id, btn) {
    const input = document.getElementById(id);
    if (input.type === 'password') { input.type = 'text'; btn.textContent = '🙈'; }
    else { input.type = 'password'; btn.textContent = '👁'; }
  }

  document.getElementById('loginForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    let valid = true;
    const email = document.getElementById('login-email').value.trim();
    const pass = document.getElementById('login-password').value;

    if (!validateEmail(email)) { showErr('err-login-email', true); valid = false; }
    else showErr('err-login-email', false);

    if (pass.length < 6) { showErr('err-login-password', true); valid = false; }
    else showErr('err-login-password', false);

    if (!valid) return;

    const btn = document.getElementById('login-btn');
    const originalText = btn.textContent;
    btn.textContent = 'Logging in...';
    btn.disabled = true;

    try {
      const res = await fetch('/auth/login', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Accept': 'application/json',
        },
        body: JSON.stringify({ email, password: pass }),
      });

      const data = await res.json();

      if (data.success) {
        // Clear any previous user's localStorage data before redirecting
        localStorage.removeItem('lazzat-user');
        localStorage.removeItem('lazzat-saved');
        localStorage.removeItem('lazzat-savedRecipes');
        localStorage.removeItem('lazzat-bookmarks');
        localStorage.removeItem('lazzat-favorites');
        localStorage.removeItem('lazzat-favoriteMeals');
        localStorage.removeItem('lazzat-planner');
        localStorage.removeItem('lazzat-grocery');
        localStorage.removeItem('lazzat-grocery-checked');
        store.set('user', { id: data.user.id, email: data.user.email, name: data.user.name });
        window.location.href = '/dashboard';
      } else {
        // Show error message under form
        let errEl = document.getElementById('login-server-error');
        if (!errEl) {
          errEl = document.createElement('div');
          errEl.id = 'login-server-error';
          errEl.style.cssText = 'color:#e74c3c;font-size:0.88rem;margin-top:10px;text-align:center;font-weight:600;';
          document.getElementById('login-btn').after(errEl);
        }
        errEl.textContent = data.message || 'Wrong email or password.';
        btn.textContent = originalText;
        btn.disabled = false;
      }
    } catch (err) {
      btn.textContent = originalText;
      btn.disabled = false;
      alert('Network error. Please try again.');
    }
  });

  // Clear errors on input
  ['login-email','login-password'].forEach(id => {
    document.getElementById(id).addEventListener('input', () => {
      document.getElementById(id).classList.remove('error');
    });
  });
</script>
@endsection
