<template>
  <ion-page>
    <ion-content :fullscreen="true" class="step-content">
      <div class="step-container">

        <div class="back-button" @click="goBack">
          <ion-icon :icon="arrowBackOutline"></ion-icon>
        </div>

        <div class="progress-section">
          <div class="progress-bar"><div class="progress-fill" style="width: 66.64%"></div></div>
          <p class="progress-text">Step 4 of 6</p>
        </div>

        <div class="title-section">
          <h1 class="title">Set Your Password</h1>
          <p class="subtitle">Create a strong password for your account</p>
        </div>

        <div class="form-section">

          <!-- Password -->
          <div class="input-group">
            <label class="input-label">Password *</label>
            <div class="input-wrapper" :class="{ error: errors.password, success: isPasswordStrong }">
              <ion-icon :icon="lockClosedOutline" class="input-icon"></ion-icon>
              <input
                v-model="password"
                :type="showPassword ? 'text' : 'password'"
                placeholder="Create a password"
                class="custom-input with-icon with-toggle"
                @input="onPasswordInput"
                @blur="validatePassword"
              >
              <ion-icon
                :icon="showPassword ? eyeOffOutline : eyeOutline"
                class="toggle-icon"
                @click="showPassword = !showPassword"
              ></ion-icon>
            </div>
            <span v-if="errors.password" class="error-message">{{ errors.password }}</span>

            <!-- Password strength meter -->
            <div v-if="password" class="strength-section">
              <div class="strength-bar">
                <div
                  class="strength-fill"
                  :class="strengthClass"
                  :style="{ width: strengthPercent + '%' }"
                ></div>
              </div>
              <span class="strength-label" :class="strengthClass">{{ strengthLabel }}</span>
            </div>

            <!-- Criteria list -->
            <div v-if="password" class="criteria-list">
              <div class="criteria-item" :class="{ met: password.length >= 8 }">
                <ion-icon :icon="password.length >= 8 ? checkmarkCircleOutline : ellipseOutline"></ion-icon>
                At least 8 characters
              </div>
              <div class="criteria-item" :class="{ met: hasUppercase }">
                <ion-icon :icon="hasUppercase ? checkmarkCircleOutline : ellipseOutline"></ion-icon>
                One uppercase letter
              </div>
              <div class="criteria-item" :class="{ met: hasNumber }">
                <ion-icon :icon="hasNumber ? checkmarkCircleOutline : ellipseOutline"></ion-icon>
                One number
              </div>
              <div class="criteria-item" :class="{ met: hasSpecial }">
                <ion-icon :icon="hasSpecial ? checkmarkCircleOutline : ellipseOutline"></ion-icon>
                One special character (!@#$...)
              </div>
            </div>
          </div>

          <!-- Confirm Password -->
          <div class="input-group">
            <label class="input-label">Confirm Password *</label>
            <div class="input-wrapper" :class="{ error: errors.confirmPassword, success: isConfirmValid }">
              <ion-icon :icon="lockClosedOutline" class="input-icon"></ion-icon>
              <input
                v-model="confirmPassword"
                :type="showConfirm ? 'text' : 'password'"
                placeholder="Re-enter your password"
                class="custom-input with-icon with-toggle"
                @input="errors.confirmPassword = ''"
                @blur="validateConfirm"
              >
              <ion-icon
                :icon="showConfirm ? eyeOffOutline : eyeOutline"
                class="toggle-icon"
                @click="showConfirm = !showConfirm"
              ></ion-icon>
            </div>
            <span v-if="errors.confirmPassword" class="error-message">{{ errors.confirmPassword }}</span>
            <span v-else-if="isConfirmValid" class="success-message">
              <ion-icon :icon="checkmarkCircleOutline"></ion-icon> Passwords match
            </span>
          </div>

          <!-- Terms & Conditions -->
          <div class="terms-section">
            <div class="checkbox-row">
              <input type="checkbox" id="terms" v-model="acceptedTerms" class="checkbox-input">
              <label for="terms">
                I agree to the
                <a href="#" @click.prevent="openTerms">Terms & Conditions</a>
                and
                <a href="#" @click.prevent="openPrivacy">Privacy Policy</a>
              </label>
            </div>
            <div class="checkbox-row" style="margin-top: 12px;">
              <input type="checkbox" id="marketing" v-model="acceptMarketing" class="checkbox-input">
              <label for="marketing">
                Receive updates, promotions and market deals via SMS/email
                <span class="optional">(Optional)</span>
              </label>
            </div>
          </div>

          <div class="security-note">
            <ion-icon :icon="shieldCheckmarkOutline"></ion-icon>
            <div>
              <strong>Your password is encrypted</strong><br>
              We never store your password in plain text. It is secured using industry-standard encryption.
            </div>
          </div>

          <div class="button-group">
            <ion-button expand="block" fill="outline" color="medium" class="back-btn" @click="goBack">
              <ion-icon slot="start" :icon="arrowBackOutline"></ion-icon>Back
            </ion-button>
            <ion-button expand="block" class="next-button" :disabled="!isFormValid" @click="handleNext">
              Next<ion-icon slot="end" :icon="arrowForwardOutline"></ion-icon>
            </ion-button>
          </div>

        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { IonPage, IonContent, IonButton, IonIcon, alertController } from '@ionic/vue';
import {
  arrowBackOutline, arrowForwardOutline, lockClosedOutline,
  eyeOutline, eyeOffOutline, checkmarkCircleOutline, ellipseOutline,
  shieldCheckmarkOutline
} from 'ionicons/icons';

const router = useRouter();

const password = ref('');
const confirmPassword = ref('');
const showPassword = ref(false);
const showConfirm = ref(false);
const acceptedTerms = ref(false);
const acceptMarketing = ref(false);
const errors = ref({ password: '', confirmPassword: '' });

const hasUppercase = computed(() => /[A-Z]/.test(password.value));
const hasNumber = computed(() => /\d/.test(password.value));
const hasSpecial = computed(() => /[!@#$%^&*(),.?":{}|<>]/.test(password.value));

const strengthScore = computed(() => {
  let score = 0;
  if (password.value.length >= 8) score++;
  if (password.value.length >= 12) score++;
  if (hasUppercase.value) score++;
  if (hasNumber.value) score++;
  if (hasSpecial.value) score++;
  return score;
});

const strengthPercent = computed(() => (strengthScore.value / 5) * 100);
const isPasswordStrong = computed(() => password.value.length >= 8 && hasUppercase.value && hasNumber.value);

const strengthClass = computed(() => {
  if (strengthScore.value <= 1) return 'weak';
  if (strengthScore.value <= 3) return 'fair';
  if (strengthScore.value === 4) return 'good';
  return 'strong';
});

const strengthLabel = computed(() => {
  if (strengthScore.value <= 1) return 'Weak';
  if (strengthScore.value <= 3) return 'Fair';
  if (strengthScore.value === 4) return 'Good';
  return 'Strong';
});

const isConfirmValid = computed(() =>
  confirmPassword.value && password.value === confirmPassword.value
);

const isFormValid = computed(() =>
  password.value.length >= 8 &&
  password.value === confirmPassword.value &&
  acceptedTerms.value
);

const onPasswordInput = () => { errors.value.password = ''; };

const validatePassword = () => {
  if (!password.value) { errors.value.password = 'Password is required'; return false; }
  if (password.value.length < 8) { errors.value.password = 'Password must be at least 8 characters'; return false; }
  errors.value.password = ''; return true;
};

const validateConfirm = () => {
  if (!confirmPassword.value) { errors.value.confirmPassword = 'Please confirm your password'; return false; }
  if (confirmPassword.value !== password.value) { errors.value.confirmPassword = 'Passwords do not match'; return false; }
  errors.value.confirmPassword = ''; return true;
};

const openTerms = async () => {
  const alert = await alertController.create({
    header: 'Terms & Conditions',
    message: 'By creating a Palengke Online account, you agree to our terms of service. You must be at least 18 years old. We may update these terms from time to time.',
    buttons: ['Close']
  });
  await alert.present();
};

const openPrivacy = async () => {
  const alert = await alertController.create({
    header: 'Privacy Policy',
    message: 'We collect personal information to provide our services. Your data is encrypted and never sold to third parties. We comply with the Data Privacy Act of 2012 (RA 10173).',
    buttons: ['Close']
  });
  await alert.present();
};

const goBack = () => router.push('/register/step3');

const handleNext = () => {
  if (!isFormValid.value) return;
  const existing = JSON.parse(localStorage.getItem('buyerRegistrationData') || '{}');
  existing.password = password.value;
  existing.acceptMarketing = acceptMarketing.value;
  existing.termsAccepted = true;
  localStorage.setItem('buyerRegistrationData', JSON.stringify(existing));
  router.push('/register/step5');
};
</script>

<style scoped>
.step-content { --background: #ffffff; }
.step-container { min-height: 100vh; padding: 24px; padding-bottom: 100px; position: relative; }

.back-button {
  position: absolute; top: 24px; left: 24px; width: 40px; height: 40px;
  display: flex; align-items: center; justify-content: center;
  background: #f5f5f5; border-radius: 12px; cursor: pointer; z-index: 10;
}
.back-button:active { transform: scale(0.95); }
.back-button ion-icon { font-size: 24px; color: #1a1a1a; }

.progress-section { margin-top: 70px; margin-bottom: 32px; }
.progress-bar { width: 100%; height: 6px; background: #e0e0e0; border-radius: 3px; overflow: hidden; margin-bottom: 8px; }
.progress-fill { height: 100%; background: #4CAF50; }
.progress-text { text-align: center; font-size: 13px; color: #666; margin: 0; font-weight: 600; }

.title-section { text-align: center; margin-bottom: 40px; }
.title { font-size: 28px; font-weight: 700; color: #1a1a1a; margin: 0 0 8px 0; }
.subtitle { font-size: 15px; color: #666; margin: 0; }

.form-section { max-width: 480px; margin: 0 auto; width: 100%; }

.input-group { margin-bottom: 20px; }
.input-label { display: block; font-size: 14px; font-weight: 600; color: #1a1a1a; margin-bottom: 8px; }

.input-wrapper { position: relative; display: flex; align-items: center; }
.input-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); font-size: 20px; color: #4CAF50; z-index: 2; }
.toggle-icon { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); font-size: 20px; color: #999; cursor: pointer; z-index: 2; }

.custom-input {
  width: 100%; padding: 14px 16px; border: 2px solid #e0e0e0;
  border-radius: 12px; font-size: 15px; background: white;
  transition: border-color 0.2s; font-family: inherit;
}
.custom-input.with-icon { padding-left: 46px; }
.custom-input.with-toggle { padding-right: 46px; }
.custom-input:focus { outline: none; border-color: #4CAF50; }
.custom-input::placeholder { color: #999; }

.input-wrapper.error .custom-input { border-color: #f44336; }
.input-wrapper.success .custom-input { border-color: #4CAF50; }

.error-message { display: block; font-size: 12px; color: #f44336; margin-top: 6px; font-weight: 500; }
.success-message {
  display: flex; align-items: center; gap: 4px;
  font-size: 12px; color: #4CAF50; margin-top: 6px; font-weight: 600;
}
.success-message ion-icon { font-size: 16px; }

/* Strength meter */
.strength-section { display: flex; align-items: center; gap: 10px; margin-top: 10px; }
.strength-bar { flex: 1; height: 6px; background: #e0e0e0; border-radius: 3px; overflow: hidden; }
.strength-fill { height: 100%; border-radius: 3px; transition: width 0.3s, background 0.3s; }
.strength-fill.weak { background: #f44336; }
.strength-fill.fair { background: #FF9800; }
.strength-fill.good { background: #2196F3; }
.strength-fill.strong { background: #4CAF50; }

.strength-label { font-size: 12px; font-weight: 600; min-width: 44px; }
.strength-label.weak { color: #f44336; }
.strength-label.fair { color: #FF9800; }
.strength-label.good { color: #2196F3; }
.strength-label.strong { color: #4CAF50; }

/* Criteria */
.criteria-list { display: flex; flex-direction: column; gap: 6px; margin-top: 12px; }
.criteria-item { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #999; }
.criteria-item ion-icon { font-size: 16px; flex-shrink: 0; color: #e0e0e0; }
.criteria-item.met { color: #4CAF50; }
.criteria-item.met ion-icon { color: #4CAF50; }

/* Terms */
.terms-section { margin: 28px 0; }
.checkbox-row { display: flex; align-items: flex-start; gap: 10px; font-size: 13px; color: #555; }
.checkbox-input { width: 18px; height: 18px; accent-color: #4CAF50; cursor: pointer; flex-shrink: 0; margin-top: 2px; }
.checkbox-row label { flex: 1; line-height: 1.5; cursor: pointer; }
.checkbox-row a { color: #4CAF50; font-weight: 600; text-decoration: none; }
.optional { color: #999; font-weight: 400; }

/* Security note */
.security-note {
  display: flex; gap: 12px; background: #E8F5E9; border-left: 4px solid #4CAF50;
  padding: 16px; border-radius: 8px; margin-bottom: 24px;
  font-size: 13px; color: #2E7D32; line-height: 1.5;
}
.security-note ion-icon { font-size: 22px; color: #4CAF50; flex-shrink: 0; margin-top: 2px; }
.security-note strong { font-weight: 700; }

.button-group { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.back-btn { --border-radius: 12px; --border-width: 2px; height: 52px; font-size: 15px; font-weight: 600; text-transform: none; }
.next-button { --background: #4CAF50; --background-hover: #45a049; --border-radius: 12px; height: 52px; font-size: 15px; font-weight: 600; text-transform: none; }
.next-button:disabled { --background: #e0e0e0; --color: #999; }

@media (max-width: 480px) {
  .title { font-size: 24px; }
  .step-container { padding: 20px 16px 100px 16px; }
  .button-group { grid-template-columns: 1fr; }
  .back-btn { order: 2; }
  .next-button { order: 1; }
}
</style>