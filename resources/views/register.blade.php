@extends('layouts.app')

@section('title', 'Sign Up | Lazzat لذّت')

@section('content')
<div class="auth-page">
  <div class="auth-left">
    <div class="auth-brand">
      <div class="auth-brand-logo">🍽️</div>
      <div class="auth-brand-name">Lazzat</div>
      <span class="auth-brand-urdu">لذّت</span>
      <p class="auth-brand-tag">Join thousands of home cooks who plan smarter with Lazzat.</p>
    </div>
  </div>

  <div class="auth-right">
    <div class="auth-form-wrap">
      <div style="display:flex;justify-content:flex-end;margin-bottom:24px;">
        <button class="lang-toggle" data-t="lang-btn">🌐 اردو</button>
      </div>
      <h2 class="auth-form-title" data-t="register-title">Create account</h2>
      <p class="auth-form-sub" data-t="register-sub">Join Lazzat and start planning delicious meals</p>

      <form id="registerForm" novalidate>
        <div class="form-group">
          <label class="form-label" data-t="label-name">Full Name</label>
          <input type="text" class="form-input" id="reg-name" data-t="label-name" data-t-attr="placeholder" placeholder="Full Name" autocomplete="name">
          <div class="form-error" id="err-name" data-t="err-name">Please enter your full name</div>
        </div>
        <div class="form-group">
          <label class="form-label" data-t="label-email">Email Address</label>
          <input type="email" class="form-input" id="reg-email" data-t="label-email" data-t-attr="placeholder" placeholder="Email Address" autocomplete="email">
          <div class="form-error" id="err-email" data-t="err-email">Please enter a valid email</div>
        </div>
        <div class="form-group">
          <label class="form-label" data-t="label-password">Password</label>
          <div class="password-wrap">
            <input type="password" class="form-input" id="reg-password" placeholder="Min. 6 characters" autocomplete="new-password">
            <button type="button" class="toggle-pass" onclick="togglePass('reg-password',this)">👁</button>
          </div>
          <div class="form-error" id="err-password" data-t="err-password">Password must be at least 6 characters</div>
          <!-- Strength bar -->
          <div id="strength-wrap" style="margin-top:8px;display:none;">
            <div style="height:4px;background:var(--cream-dark);border-radius:4px;overflow:hidden;">
              <div id="strength-bar" style="height:100%;width:0;transition:all 0.3s;border-radius:4px;"></div>
            </div>
            <div id="strength-label" style="font-size:0.75rem;margin-top:4px;color:var(--warm-gray);"></div>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label" data-t="label-confirm">Confirm Password</label>
          <div class="password-wrap">
            <input type="password" class="form-input" id="reg-confirm" data-t="label-confirm" data-t-attr="placeholder" placeholder="Confirm Password" autocomplete="new-password">
            <button type="button" class="toggle-pass" onclick="togglePass('reg-confirm',this)">👁</button>
          </div>
          <div class="form-error" id="err-confirm" data-t="err-confirm">Passwords do not match</div>
        </div>
        <button type="submit" class="btn-auth" id="reg-btn" data-t="btn-register">Create Account</button>
      </form>

      <p class="auth-switch"><a href="/login" data-t="switch-to-login">Already have an account? Log in</a></p>
    </div>
  </div>
</div>

<script src="{{ asset('js/translations.js') }}"></script>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    applyTranslations();
    document.querySelectorAll('.lang-toggle').forEach(btn => btn.addEventListener('click', toggleLang));
  });

  function togglePass(id, btn) {
    const input = document.getElementById(id);
    if (input.type === 'password') { input.type = 'text'; btn.textContent = '🙈'; }
    else { input.type = 'password'; btn.textContent = '👁'; }
  }

  function showErr(id, show) {
    const el = document.getElementById(id);
    const wrap = el.previousElementSibling;
    const input = wrap?.classList.contains('password-wrap') ? wrap.querySelector('input') : wrap;
    if (show) { el.classList.add('show'); if(input) input.classList.add('error'); }
    else { el.classList.remove('show'); if(input) input.classList.remove('error'); }
  }

  function validateEmail(e) { return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(e); }

  // Password strength
  document.getElementById('reg-password').addEventListener('input', function() {
    const val = this.value;
    const wrap = document.getElementById('strength-wrap');
    const bar = document.getElementById('strength-bar');
    const label = document.getElementById('strength-label');
    if (!val) { wrap.style.display='none'; return; }
    wrap.style.display = 'block';
    let score = 0;
    if (val.length >= 6) score++;
    if (val.length >= 10) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    const colors = ['#e74c3c','#e67e22','#f1c40f','#2ecc71','#27ae60'];
    const labels = ['Very Weak','Weak','Fair','Strong','Very Strong'];
    bar.style.width = (score * 20) + '%';
    bar.style.background = colors[score-1] || '#e74c3c';
    label.textContent = labels[score-1] || '';
  });

  document.getElementById('registerForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    let valid = true;
    const name    = document.getElementById('reg-name').value.trim();
    const email   = document.getElementById('reg-email').value.trim();
    const pass    = document.getElementById('reg-password').value;
    const confirm = document.getElementById('reg-confirm').value;

    if (name.length < 2)         { showErr('err-name',     true); valid=false; } else showErr('err-name',     false);
    if (!validateEmail(email))   { showErr('err-email',    true); valid=false; } else showErr('err-email',    false);
    if (pass.length < 6)         { showErr('err-password', true); valid=false; } else showErr('err-password', false);
    if (pass !== confirm)        { showErr('err-confirm',  true); valid=false; } else showErr('err-confirm',  false);

    if (!valid) return;

    const btn = document.getElementById('reg-btn');
    const originalText = btn.textContent;
    btn.textContent = 'Creating account...';
    btn.disabled = true;

    try {
      const res = await fetch('/auth/register', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Accept': 'application/json',
        },
        body: JSON.stringify({ name, email, password: pass, password_confirmation: confirm }),
      });

      const data = await res.json();

      if (data.success) {
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
        // Show server-side error (e.g. email already taken)
        let errEl = document.getElementById('reg-server-error');
        if (!errEl) {
          errEl = document.createElement('div');
          errEl.id = 'reg-server-error';
          errEl.style.cssText = 'color:#e74c3c;font-size:0.88rem;margin-top:10px;text-align:center;font-weight:600;';
          document.getElementById('reg-btn').after(errEl);
        }
        // Laravel returns validation errors as { errors: { field: ['msg'] } }
        if (data.errors) {
          const firstError = Object.values(data.errors)[0];
          errEl.textContent = Array.isArray(firstError) ? firstError[0] : firstError;
          // Highlight the specific field
          if (data.errors.email) showErr('err-email', true);
          if (data.errors.name)  showErr('err-name',  true);
        } else {
          errEl.textContent = data.message || 'Registration failed. Please try again.';
        }
        btn.textContent = originalText;
        btn.disabled = false;
      }
    } catch (err) {
      btn.textContent = originalText;
      btn.disabled = false;
      alert('Network error. Please try again.');
    }
  });

  ['reg-name','reg-email','reg-password','reg-confirm'].forEach(id => {
    document.getElementById(id)?.addEventListener('input', () => document.getElementById(id).classList.remove('error'));
  });
</script>
@endsection
