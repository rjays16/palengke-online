<template>
  <div class="login-root">
    <!-- Left Panel: Login Form -->
    <div class="form-panel">
      <div class="form-inner">
        <div class="logo-row">
          <img
            src="/assets/images/logo.png"
            alt="Palengke Online"
            class="logo"
          />
          <span class="logo-text">Palengke Online</span>
        </div>

        <div class="form-header">
          <h2 class="form-title">Welcome back</h2>
          <p class="form-subtitle">Sign in to your admin account</p>
        </div>

        <form class="login-form" @submit.prevent="handleLogin">
          <div
            class="field-group"
            :class="{
              focused: emailFocused,
              error: errors.email,
              filled: form.email,
            }"
          >
            <label class="field-label">Email Address</label>
            <div class="field-wrap">
              <svg
                class="field-icon"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
              >
                <path
                  d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"
                />
                <polyline points="22,6 12,13 2,6" />
              </svg>
              <input
                v-model="form.email"
                type="email"
                placeholder="superadmin@palengke.com"
                class="field-input"
                autocomplete="email"
                @focus="emailFocused = true"
                @blur="
                  emailFocused = false;
                  validateEmail();
                "
              />
            </div>
            <span class="field-error" v-if="errors.email">{{
              errors.email
            }}</span>
          </div>

          <div
            class="field-group"
            :class="{
              focused: passFocused,
              error: errors.password,
              filled: form.password,
            }"
          >
            <label class="field-label">Password</label>
            <div class="field-wrap">
              <svg
                class="field-icon"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
              >
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
              </svg>
              <input
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                placeholder="Enter your password"
                class="field-input"
                autocomplete="current-password"
                @focus="passFocused = true"
                @blur="
                  passFocused = false;
                  validatePassword();
                "
              />
              <button
                type="button"
                class="eye-btn"
                @click="showPassword = !showPassword"
              >
                <svg
                  v-if="!showPassword"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                >
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                  <circle cx="12" cy="12" r="3" />
                </svg>
                <svg
                  v-else
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                >
                  <path
                    d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"
                  />
                  <line x1="1" y1="1" x2="23" y2="23" />
                </svg>
              </button>
            </div>
            <span class="field-error" v-if="errors.password">{{
              errors.password
            }}</span>
          </div>

          <div class="form-options">
            <label class="remember-label">
              <input
                type="checkbox"
                v-model="rememberMe"
                class="remember-check"
              />
              <span class="remember-custom"></span>
              <span>Remember me</span>
            </label>
            <a href="#" class="forgot-link" @click.prevent="handleForgot"
              >Forgot password?</a
            >
          </div>

          <button
            type="submit"
            class="submit-btn"
            :class="{ loading: auth.isLoading }"
            :disabled="auth.isLoading"
          >
            <span class="btn-text" v-if="!auth.isLoading">
              Sign In
              <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2.5"
              >
                <line x1="5" y1="12" x2="19" y2="12" />
                <polyline points="12 5 19 12 12 19" />
              </svg>
            </span>
            <span class="btn-loader" v-else>
              <span class="loader-dot"></span>
              <span class="loader-dot"></span>
              <span class="loader-dot"></span>
            </span>
          </button>
        </form>

        <div class="alert-error" v-if="auth.loginError">
          <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
          >
            <circle cx="12" cy="12" r="10" />
            <line x1="12" y1="8" x2="12" y2="12" />
            <line x1="12" y1="16" x2="12.01" y2="16" />
          </svg>
          {{ auth.loginError }}
        </div>
      </div>
    </div>

    <!-- Right Panel: Hero Image -->
    <div class="hero-panel">
      <div class="hero-overlay"></div>
      <img
        src="https://images.unsplash.com/photo-1542838132-92c53300491e?q=80&w=1974&auto=format&fit=crop"
        alt="Fresh produce market"
        class="hero-image"
      />
      <div class="hero-content">
        <div class="hero-badge">Admin Portal</div>
        <h2 class="hero-title">Manage your marketplace</h2>
        <p class="hero-desc">
          Oversee sellers, drivers, customers, and analytics — all in one place.
        </p>
        <div class="hero-stats">
          <div class="hero-stat">
            <span class="hero-stat-num">3</span>
            <span class="hero-stat-lbl">Apps</span>
          </div>
          <div class="hero-stat">
            <span class="hero-stat-num">24/7</span>
            <span class="hero-stat-lbl">Monitoring</span>
          </div>
          <div class="hero-stat">
            <span class="hero-stat-num">∞</span>
            <span class="hero-stat-lbl">Scalable</span>
          </div>
        </div>
      </div>
      <div class="hero-footer">
        <span>&copy; 2026 Palengke Online Inc.</span>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: "auth" });
const auth = useAuthStore();
const form = ref({ email: "", password: "" });
const errors = ref({ email: "", password: "" });
const showPassword = ref(false);
const rememberMe = ref(false);
const emailFocused = ref(false);
const passFocused = ref(false);

const validateEmail = () => {
  if (!form.value.email) {
    errors.value.email = "Email is required";
    return false;
  }
  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.value.email)) {
    errors.value.email = "Enter a valid email address";
    return false;
  }
  errors.value.email = "";
  return true;
};

const validatePassword = () => {
  if (!form.value.password) {
    errors.value.password = "Password is required";
    return false;
  }
  if (form.value.password.length < 6) {
    errors.value.password = "Password must be at least 6 characters";
    return false;
  }
  errors.value.password = "";
  return true;
};

const handleLogin = async () => {
  auth.loginError = "";
  const emailOk = validateEmail();
  const passOk = validatePassword();
  if (!emailOk || !passOk) return;
  const success = await auth.login(form.value.email, form.value.password);
  if (success) navigateTo("/dashboard");
};

const handleForgot = () => {
  alert("Password reset link will be sent to your email.");
};
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap");

:global(html),
:global(body) {
  margin: 0;
  padding: 0;
  height: 100%;
  overflow: hidden;
}

:global(#__nuxt) {
  height: 100%;
}

*,
*::before,
*::after {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

.login-root {
  display: flex;
  min-height: 100vh;
  height: 100vh;
  font-family: "Inter", sans-serif;
  background: #fff;
  overflow: hidden;
}

/* ── LEFT FORM PANEL ─────────────────────── */
.form-panel {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 48px;
  background: #ffffff;
  min-height: 100vh;
}

.form-inner {
  width: 100%;
  max-width: 480px;
  animation: formIn 0.5s ease both;
}

@keyframes formIn {
  from {
    opacity: 0;
    transform: translateY(12px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.logo-row {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 40px;
}

.logo {
  width: 56px;
  height: 56px;
  border-radius: 14px;
  object-fit: cover;
}

.logo-text {
  font-family: "Plus Jakarta Sans", sans-serif;
  font-size: 26px;
  font-weight: 700;
  color: #111827;
}

.form-header {
  margin-bottom: 32px;
}

.form-title {
  font-family: "Plus Jakarta Sans", sans-serif;
  font-size: 34px;
  font-weight: 700;
  color: #111827;
  line-height: 1.2;
  margin-bottom: 8px;
}

.form-subtitle {
  font-size: 16px;
  color: #6b7280;
  font-weight: 400;
}

/* ── FORM FIELDS ─────────────────────────── */
.login-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
  margin-bottom: 20px;
}

.field-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.field-label {
  font-size: 14px;
  font-weight: 600;
  color: #374151;
  letter-spacing: 0.2px;
  transition: color 0.2s;
}

.field-group.focused .field-label {
  color: #059669;
}
.field-group.error .field-label {
  color: #dc2626;
}

.field-wrap {
  position: relative;
  display: flex;
  align-items: center;
}

.field-icon {
  position: absolute;
  left: 14px;
  width: 20px;
  height: 20px;
  color: #9ca3af;
  transition: color 0.2s;
  pointer-events: none;
  flex-shrink: 0;
}

.field-group.focused .field-icon {
  color: #059669;
}
.field-group.error .field-icon {
  color: #dc2626;
}

.field-input {
  width: 100%;
  padding: 15px 16px 15px 48px;
  border: 1.5px solid #e5e7eb;
  border-radius: 10px;
  font-size: 15px;
  font-family: "Inter", sans-serif;
  color: #111827;
  background: #f9fafb;
  outline: none;
  transition:
    border-color 0.2s,
    box-shadow 0.2s,
    background 0.2s;
}

.field-input::placeholder {
  color: #9ca3af;
}

.field-group.focused .field-input {
  border-color: #059669;
  box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
  background: #fff;
}

.field-group.error .field-input {
  border-color: #dc2626;
  box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.08);
  background: #fff;
}

.eye-btn {
  position: absolute;
  right: 12px;
  background: none;
  border: none;
  cursor: pointer;
  padding: 6px;
  color: #9ca3af;
  display: flex;
  align-items: center;
  transition: color 0.2s;
}
.eye-btn:hover {
  color: #059669;
}
.eye-btn svg {
  width: 18px;
  height: 18px;
}

.field-error {
  font-size: 12px;
  color: #dc2626;
  font-weight: 500;
  padding-left: 2px;
  animation: shake 0.3s ease;
}

@keyframes shake {
  0%,
  100% {
    transform: translateX(0);
  }
  25% {
    transform: translateX(-4px);
  }
  75% {
    transform: translateX(4px);
  }
}

/* ── OPTIONS ROW ─────────────────────────── */
.form-options {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.remember-label {
  display: flex;
  align-items: center;
  gap: 10px;
  cursor: pointer;
  font-size: 13px;
  color: #4b5563;
  font-weight: 500;
  user-select: none;
}

.remember-check {
  display: none;
}

.remember-custom {
  width: 18px;
  height: 18px;
  border: 2px solid #d1d5db;
  border-radius: 5px;
  background: white;
  position: relative;
  transition: all 0.2s;
  flex-shrink: 0;
}

.remember-check:checked + .remember-custom {
  background: #059669;
  border-color: #059669;
}

.remember-check:checked + .remember-custom::after {
  content: "";
  position: absolute;
  top: 2px;
  left: 5px;
  width: 5px;
  height: 9px;
  border: 2px solid white;
  border-top: none;
  border-left: none;
  transform: rotate(45deg);
}

.forgot-link {
  font-size: 13px;
  color: #059669;
  text-decoration: none;
  font-weight: 600;
  transition: color 0.2s;
}
.forgot-link:hover {
  color: #047857;
}

/* ── SUBMIT BUTTON ───────────────────────── */
.submit-btn {
  width: 100%;
  padding: 16px 24px;
  background: #059669;
  color: white;
  border: none;
  border-radius: 10px;
  font-size: 16px;
  font-family: "Inter", sans-serif;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  position: relative;
  overflow: hidden;
  margin-top: 4px;
}

.submit-btn:hover {
  background: #047857;
  transform: translateY(-1px);
  box-shadow: 0 4px 16px rgba(5, 150, 105, 0.35);
}

.submit-btn:active {
  transform: translateY(0);
}

.submit-btn:disabled {
  cursor: not-allowed;
  opacity: 0.75;
  transform: none;
  box-shadow: none;
}

.btn-text {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.btn-text svg {
  width: 16px;
  height: 16px;
  transition: transform 0.2s;
}

.submit-btn:hover .btn-text svg {
  transform: translateX(4px);
}

.btn-loader {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
}

.loader-dot {
  width: 7px;
  height: 7px;
  background: white;
  border-radius: 50%;
  animation: bounce 1.2s ease-in-out infinite;
}
.loader-dot:nth-child(2) {
  animation-delay: 0.2s;
}
.loader-dot:nth-child(3) {
  animation-delay: 0.4s;
}

@keyframes bounce {
  0%,
  80%,
  100% {
    transform: scale(0.7);
    opacity: 0.5;
  }
  40% {
    transform: scale(1);
    opacity: 1;
  }
}

/* ── ERROR ALERT ─────────────────────────── */
.alert-error {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 16px;
  background: #fef2f2;
  border: 1px solid #fecaca;
  border-radius: 10px;
  font-size: 13px;
  color: #dc2626;
  font-weight: 500;
  margin-bottom: 16px;
  animation: shake 0.3s ease;
}
.alert-error svg {
  width: 18px;
  height: 18px;
  flex-shrink: 0;
}

/* ── FORM FOOTER ─────────────────────────── */
.form-footer {
  text-align: center;
  font-size: 13px;
  color: #9ca3af;
}
.form-footer a {
  color: #059669;
  text-decoration: none;
  font-weight: 600;
}
.form-footer a:hover {
  color: #047857;
}

/* ── RIGHT HERO PANEL ────────────────────── */
.hero-panel {
  width: 48%;
  min-height: 100vh;
  position: relative;
  overflow: hidden;
  flex-shrink: 0;
}

.hero-image {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
}

.hero-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(
    135deg,
    rgba(5, 150, 105, 0.75) 0%,
    rgba(4, 120, 87, 0.6) 40%,
    rgba(0, 0, 0, 0.5) 100%
  );
  z-index: 1;
}

.hero-content {
  position: relative;
  z-index: 2;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;
  min-height: 100vh;
  padding: 60px 48px;
}

.hero-badge {
  display: inline-block;
  padding: 8px 20px;
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.25);
  border-radius: 100px;
  font-size: 12px;
  font-weight: 600;
  color: #fff;
  text-transform: uppercase;
  letter-spacing: 2px;
  margin-bottom: 24px;
}

.hero-title {
  font-family: "Plus Jakarta Sans", sans-serif;
  font-size: 40px;
  font-weight: 800;
  color: #fff;
  line-height: 1.15;
  margin-bottom: 16px;
  max-width: 480px;
}

.hero-desc {
  font-size: 16px;
  color: rgba(255, 255, 255, 0.8);
  line-height: 1.6;
  max-width: 400px;
  margin-bottom: 40px;
  font-weight: 400;
}

.hero-stats {
  display: flex;
  gap: 32px;
  padding: 20px 32px;
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(8px);
  border-radius: 16px;
  border: 1px solid rgba(255, 255, 255, 0.15);
}

.hero-stat {
  text-align: center;
}

.hero-stat-num {
  display: block;
  font-family: "Plus Jakarta Sans", sans-serif;
  font-size: 24px;
  font-weight: 700;
  color: #fff;
  line-height: 1;
  margin-bottom: 4px;
}

.hero-stat-lbl {
  display: block;
  font-size: 11px;
  color: rgba(255, 255, 255, 0.7);
  text-transform: uppercase;
  letter-spacing: 1.5px;
  font-weight: 500;
}

.hero-footer {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  z-index: 2;
  padding: 24px 48px;
  text-align: center;
  font-size: 12px;
  color: rgba(255, 255, 255, 0.5);
}

/* ── RESPONSIVE ──────────────────────────── */
@media (max-width: 900px) {
  .login-root {
    flex-direction: column;
  }
  .hero-panel {
    width: 100%;
    min-height: 40vh;
  }
  .hero-content {
    min-height: 40vh;
    padding: 32px 24px;
  }
  .hero-title {
    font-size: 28px;
  }
  .form-panel {
    min-height: auto;
    padding: 40px 24px;
  }
}
@media (max-width: 480px) {
  .hero-panel {
    display: none;
  }
  .form-panel {
    padding: 32px 20px;
    align-items: flex-start;
  }
  .form-inner {
    max-width: 100%;
  }
  .logo-row {
    margin-bottom: 24px;
  }
  .logo {
    width: 44px;
    height: 44px;
  }
  .logo-text {
    font-size: 20px;
  }
  .form-title {
    font-size: 26px;
  }
  .form-subtitle {
    font-size: 14px;
  }
  .form-options {
    flex-direction: column;
    align-items: flex-start;
    gap: 8px;
  }
  .form-footer {
    font-size: 11px;
  }
  .field-input {
    font-size: 14px;
    padding: 13px 14px 13px 42px;
  }
}
</style>
