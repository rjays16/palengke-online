<template>
  <div class="reset-root">
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
          <h2 class="form-title">Set new password</h2>
          <p class="form-subtitle">
            Enter your new password below.
          </p>
        </div>

        <div class="alert-success" v-if="successMessage">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
            <polyline points="22 4 12 14.01 9 11.01" />
          </svg>
          {{ successMessage }}
        </div>

        <form class="reset-form" @submit.prevent="handleReset" v-if="!successMessage">
          <div
            class="field-group"
            :class="{ focused: passFocused, error: errors.password, filled: form.password }"
          >
            <label class="field-label">New Password</label>
            <div class="field-wrap">
              <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
              </svg>
              <input
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                placeholder="Enter new password"
                class="field-input"
                autocomplete="new-password"
                @focus="passFocused = true"
                @blur="passFocused = false; validatePassword()"
              />
              <button type="button" class="eye-btn" @click="showPassword = !showPassword">
                <svg v-if="!showPassword" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                  <circle cx="12" cy="12" r="3" />
                </svg>
                <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24" />
                  <line x1="1" y1="1" x2="23" y2="23" />
                </svg>
              </button>
            </div>
            <span class="field-error" v-if="errors.password">{{ errors.password }}</span>
          </div>

          <div
            class="field-group"
            :class="{ focused: confirmFocused, error: errors.confirm, filled: form.confirm }"
          >
            <label class="field-label">Confirm New Password</label>
            <div class="field-wrap">
              <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
              </svg>
              <input
                v-model="form.confirm"
                :type="showConfirm ? 'text' : 'password'"
                placeholder="Confirm new password"
                class="field-input"
                autocomplete="new-password"
                @focus="confirmFocused = true"
                @blur="confirmFocused = false; validateConfirm()"
              />
              <button type="button" class="eye-btn" @click="showConfirm = !showConfirm">
                <svg v-if="!showConfirm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                  <circle cx="12" cy="12" r="3" />
                </svg>
                <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24" />
                  <line x1="1" y1="1" x2="23" y2="23" />
                </svg>
              </button>
            </div>
            <span class="field-error" v-if="errors.confirm">{{ errors.confirm }}</span>
          </div>

          <button
            type="submit"
            class="submit-btn"
            :class="{ loading: isLoading }"
            :disabled="isLoading"
          >
            <span class="btn-text" v-if="!isLoading">
              Reset Password
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
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

        <div class="back-link" v-if="successMessage">
          <a href="#" @click.prevent="navigateTo('/login')">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="19" y1="12" x2="5" y2="12" />
              <polyline points="12 19 5 12 12 5" />
            </svg>
            Back to Sign In
          </a>
        </div>

        <div class="alert-error" v-if="errorMessage">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10" />
            <line x1="12" y1="8" x2="12" y2="12" />
            <line x1="12" y1="16" x2="12.01" y2="16" />
          </svg>
          {{ errorMessage }}
        </div>
      </div>
    </div>

    <div class="hero-panel">
      <div class="hero-overlay"></div>
      <img
        src="https://images.unsplash.com/photo-1542838132-92c53300491e?q=80&w=1974&auto=format&fit=crop"
        alt="Fresh produce market"
        class="hero-image"
      />
      <div class="hero-content">
        <div class="hero-badge">Admin Portal</div>
        <h2 class="hero-title">Almost done!</h2>
        <p class="hero-desc">
          Choose a strong password you haven't used before to keep your account secure.
        </p>
        <div class="hero-stats">
          <div class="hero-stat">
            <span class="hero-stat-num">6+</span>
            <span class="hero-stat-lbl">Min chars</span>
          </div>
          <div class="hero-stat">
            <span class="hero-stat-num">Strong</span>
            <span class="hero-stat-lbl">Recommended</span>
          </div>
          <div class="hero-stat">
            <span class="hero-stat-num">Unique</span>
            <span class="hero-stat-lbl">Don't reuse</span>
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

const runtimeConfig = useRuntimeConfig();
const apiBase = runtimeConfig.public.apiBaseUrl || "http://localhost:8000";

const route = useRoute();

const form = ref({ password: "", confirm: "" });
const errors = ref({ password: "", confirm: "" });
const showPassword = ref(false);
const showConfirm = ref(false);
const passFocused = ref(false);
const confirmFocused = ref(false);
const isLoading = ref(false);
const errorMessage = ref("");
const successMessage = ref("");

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

const validateConfirm = () => {
  if (!form.value.confirm) {
    errors.value.confirm = "Please confirm your password";
    return false;
  }
  if (form.value.password !== form.value.confirm) {
    errors.value.confirm = "Passwords do not match";
    return false;
  }
  errors.value.confirm = "";
  return true;
};

const handleReset = async () => {
  errorMessage.value = "";
  successMessage.value = "";

  const token = route.query.token;
  const email = route.query.email;

  if (!token || !email) {
    errorMessage.value = "Invalid reset link. Missing token or email.";
    return;
  }

  const passOk = validatePassword();
  const confirmOk = validateConfirm();
  if (!passOk || !confirmOk) return;

  isLoading.value = true;

  try {
    const response = await $fetch(`${apiBase}/api/admin/reset-password`, {
      method: "POST",
      body: {
        email,
        token,
        password: form.value.password,
        password_confirmation: form.value.confirm,
      },
    });

    successMessage.value = response.message || "Password reset successfully!";
  } catch (err) {
    errorMessage.value = err?.data?.message || err?.data?.email?.[0] || "Invalid or expired reset link.";
  } finally {
    isLoading.value = false;
  }
};
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap");

:global(html), :global(body) {
  margin: 0; padding: 0; height: 100%; overflow: hidden;
}
:global(#__nuxt) { height: 100%; }
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

.reset-root {
  display: flex; min-height: 100vh; height: 100vh;
  font-family: "Inter", sans-serif; background: #fff; overflow: hidden;
}

.form-panel {
  flex: 1; display: flex; align-items: center; justify-content: center;
  padding: 48px; background: #ffffff; min-height: 100vh;
}

.form-inner {
  width: 100%; max-width: 480px; animation: formIn 0.5s ease both;
}

@keyframes formIn {
  from { opacity: 0; transform: translateY(12px); }
  to { opacity: 1; transform: translateY(0); }
}

.logo-row {
  display: flex; align-items: center; gap: 12px; margin-bottom: 40px;
}
.logo { width: 56px; height: 56px; border-radius: 14px; object-fit: cover; }
.logo-text {
  font-family: "Plus Jakarta Sans", sans-serif;
  font-size: 26px; font-weight: 700; color: #111827;
}

.form-header { margin-bottom: 32px; }
.form-title {
  font-family: "Plus Jakarta Sans", sans-serif;
  font-size: 34px; font-weight: 700; color: #111827; line-height: 1.2; margin-bottom: 8px;
}
.form-subtitle { font-size: 16px; color: #6b7280; font-weight: 400; }

.reset-form { display: flex; flex-direction: column; gap: 20px; margin-bottom: 20px; }

.field-group { display: flex; flex-direction: column; gap: 6px; }
.field-label { font-size: 14px; font-weight: 600; color: #374151; transition: color 0.2s; }
.field-group.focused .field-label { color: #059669; }
.field-group.error .field-label { color: #dc2626; }

.field-wrap { position: relative; display: flex; align-items: center; }
.field-icon {
  position: absolute; left: 14px; width: 20px; height: 20px;
  color: #9ca3af; pointer-events: none; flex-shrink: 0;
}
.field-group.focused .field-icon { color: #059669; }
.field-group.error .field-icon { color: #dc2626; }

.field-input {
  width: 100%; padding: 15px 16px 15px 48px;
  border: 1.5px solid #e5e7eb; border-radius: 10px; font-size: 15px;
  font-family: "Inter", sans-serif; color: #111827;
  background: #f9fafb; outline: none;
  transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
}
.field-input::placeholder { color: #9ca3af; }
.field-group.focused .field-input {
  border-color: #059669; box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1); background: #fff;
}
.field-group.error .field-input {
  border-color: #dc2626; box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.08); background: #fff;
}

.eye-btn {
  position: absolute; right: 12px; background: none; border: none;
  cursor: pointer; padding: 6px; color: #9ca3af; display: flex;
  align-items: center; transition: color 0.2s;
}
.eye-btn:hover { color: #059669; }
.eye-btn svg { width: 18px; height: 18px; }

.field-error { font-size: 12px; color: #dc2626; font-weight: 500; padding-left: 2px; animation: shake 0.3s ease; }

@keyframes shake {
  0%, 100% { transform: translateX(0); }
  25% { transform: translateX(-4px); }
  75% { transform: translateX(4px); }
}

.submit-btn {
  width: 100%; padding: 16px 24px; background: #059669; color: white;
  border: none; border-radius: 10px; font-size: 16px;
  font-family: "Inter", sans-serif; font-weight: 600;
  cursor: pointer; transition: all 0.2s; position: relative; overflow: hidden; margin-top: 4px;
}
.submit-btn:hover { background: #047857; transform: translateY(-1px); box-shadow: 0 4px 16px rgba(5, 150, 105, 0.35); }
.submit-btn:active { transform: translateY(0); }
.submit-btn:disabled { cursor: not-allowed; opacity: 0.75; transform: none; box-shadow: none; }

.btn-text { display: flex; align-items: center; justify-content: center; gap: 8px; }
.btn-text svg { width: 16px; height: 16px; transition: transform 0.2s; }
.submit-btn:hover .btn-text svg { transform: translateX(4px); }

.btn-loader { display: flex; align-items: center; justify-content: center; gap: 6px; }
.loader-dot {
  width: 7px; height: 7px; background: white; border-radius: 50%;
  animation: bounce 1.2s ease-in-out infinite;
}
.loader-dot:nth-child(2) { animation-delay: 0.2s; }
.loader-dot:nth-child(3) { animation-delay: 0.4s; }
@keyframes bounce {
  0%, 80%, 100% { transform: scale(0.7); opacity: 0.5; }
  40% { transform: scale(1); opacity: 1; }
}

.back-link {
  text-align: center; margin-bottom: 16px;
}
.back-link a {
  display: inline-flex; align-items: center; gap: 6px;
  font-size: 14px; color: #059669; text-decoration: none; font-weight: 600;
}
.back-link a:hover { color: #047857; }
.back-link a svg { width: 16px; height: 16px; }

.alert-success {
  display: flex; align-items: center; gap: 10px;
  padding: 12px 16px; background: #f0fdf4; border: 1px solid #bbf7d0;
  border-radius: 10px; font-size: 13px; color: #16a34a; font-weight: 500;
  margin-bottom: 16px; animation: fadeIn 0.3s ease;
}
.alert-success svg { width: 18px; height: 18px; flex-shrink: 0; }

.alert-error {
  display: flex; align-items: center; gap: 10px;
  padding: 12px 16px; background: #fef2f2; border: 1px solid #fecaca;
  border-radius: 10px; font-size: 13px; color: #dc2626; font-weight: 500;
  animation: shake 0.3s ease;
}
.alert-error svg { width: 18px; height: 18px; flex-shrink: 0; }

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-4px); }
  to { opacity: 1; transform: translateY(0); }
}

.hero-panel {
  width: 48%; min-height: 100vh; position: relative; overflow: hidden; flex-shrink: 0;
}
.hero-image { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; object-position: center; }
.hero-overlay {
  position: absolute; inset: 0;
  background: linear-gradient(135deg, rgba(5, 150, 105, 0.75) 0%, rgba(4, 120, 87, 0.6) 40%, rgba(0,0,0,0.5) 100%);
  z-index: 1;
}
.hero-content {
  position: relative; z-index: 2; display: flex; flex-direction: column;
  justify-content: center; align-items: center; text-align: center;
  min-height: 100vh; padding: 60px 48px;
}
.hero-badge {
  display: inline-block; padding: 8px 20px;
  background: rgba(255,255,255,0.15); backdrop-filter: blur(8px);
  border: 1px solid rgba(255,255,255,0.25); border-radius: 100px;
  font-size: 12px; font-weight: 600; color: #fff;
  text-transform: uppercase; letter-spacing: 2px; margin-bottom: 24px;
}
.hero-title {
  font-family: "Plus Jakarta Sans", sans-serif;
  font-size: 40px; font-weight: 800; color: #fff;
  line-height: 1.15; margin-bottom: 16px; max-width: 480px;
}
.hero-desc {
  font-size: 16px; color: rgba(255,255,255,0.8);
  line-height: 1.6; max-width: 400px; margin-bottom: 40px; font-weight: 400;
}
.hero-stats {
  display: flex; gap: 32px; padding: 20px 32px;
  background: rgba(255,255,255,0.1); backdrop-filter: blur(8px);
  border-radius: 16px; border: 1px solid rgba(255,255,255,0.15);
}
.hero-stat { text-align: center; }
.hero-stat-num {
  display: block; font-family: "Plus Jakarta Sans", sans-serif;
  font-size: 24px; font-weight: 700; color: #fff; line-height: 1; margin-bottom: 4px;
}
.hero-stat-lbl {
  display: block; font-size: 11px; color: rgba(255,255,255,0.7);
  text-transform: uppercase; letter-spacing: 1.5px; font-weight: 500;
}
.hero-footer {
  position: absolute; bottom: 0; left: 0; right: 0; z-index: 2;
  padding: 24px 48px; text-align: center; font-size: 12px; color: rgba(255,255,255,0.5);
}

@media (max-width: 900px) {
  .reset-root { flex-direction: column; }
  .hero-panel { width: 100%; min-height: 40vh; }
  .hero-content { min-height: 40vh; padding: 32px 24px; }
  .hero-title { font-size: 28px; }
  .form-panel { min-height: auto; padding: 40px 24px; }
}
@media (max-width: 480px) {
  .hero-panel { display: none; }
  .form-panel { padding: 32px 20px; align-items: flex-start; }
  .form-inner { max-width: 100%; }
  .logo-row { margin-bottom: 24px; }
  .logo { width: 44px; height: 44px; }
  .logo-text { font-size: 20px; }
  .form-title { font-size: 26px; }
  .form-subtitle { font-size: 14px; }
  .field-input { font-size: 14px; padding: 13px 14px 13px 42px; }
}
</style>
