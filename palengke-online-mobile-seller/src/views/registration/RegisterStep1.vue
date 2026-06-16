<template>
  <ion-page>
    <ion-content :fullscreen="true" class="step-content">
      <div class="step-container">
        <!-- Back Button -->
        <div class="back-button" @click="goBack">
          <ion-icon :icon="arrowBackOutline"></ion-icon>
        </div>

        <!-- Progress Indicator -->
        <div class="progress-section">
          <div class="progress-bar">
            <div class="progress-fill" style="width: 14.28%"></div>
          </div>
          <p class="progress-text">Step 1 of 7</p>
        </div>

        <!-- Title -->
        <div class="title-section">
          <h1 class="title">Contact Information</h1>
          <p class="subtitle">We'll use this to verify your account</p>
        </div>

        <!-- Form -->
        <div class="form-section">
          <!-- Email Input -->
          <div class="input-group">
            <label class="input-label">Email Address *</label>
            <div
              class="input-wrapper"
              :class="{ error: errors.email, success: isEmailValid && !isEmailVerified, verified: isEmailVerified }"
            >
              <ion-icon :icon="mailOutline" class="input-icon"></ion-icon>
              <input
                v-model="email"
                type="email"
                placeholder="you@example.com"
                class="custom-input with-icon"
                :disabled="isEmailVerified"
                autocomplete="off"
                @input="onEmailInput"
                @blur="onEmailBlur"
              />
              <ion-icon v-if="isEmailVerified" :icon="checkmarkCircleOutline" class="verified-icon"></ion-icon>
            </div>
            <span v-if="errors.email" class="error-message">{{ errors.email }}</span>
            <span v-else-if="emailExistsError" class="error-message">{{ emailExistsError }}</span>
            <span v-else-if="isCheckingEmail" class="helper-text">Checking email...</span>
            <span v-else-if="isEmailVerified" class="verified-text">Email verified</span>
            <span v-else class="helper-text">We'll send verification to this email</span>
          </div>

          <!-- Phone Number with Country Code -->
          <div class="input-group">
            <label class="input-label">Phone Number *</label>
            <div class="phone-input-row">
              <!-- Country Code Dropdown -->
              <div class="country-code-wrapper">
                <select v-model="countryCode" class="country-code-select">
                  <option v-for="c in countryCodes" :key="c.code" :value="c.code">
                    {{ c.code }} {{ c.abbr || c.country.substring(0, 2).toUpperCase() }}
                  </option>
                </select>
              </div>
              <!-- Phone Number Input -->
              <div
                class="phone-number-wrapper"
                :class="{
                  error: errors.phone,
                  success: isPhoneValid,
                }"
              >
                <input
                  v-model="phone"
                  type="tel"
                  :placeholder="phonePlaceholder"
                  class="phone-input"
                  :maxlength="maxPhoneLength"
                  @input="onPhoneInput"
                  @blur="validateField('phone')"
                />
                <ion-icon v-if="isPhoneValid" :icon="checkmarkCircleOutline" class="success-icon"></ion-icon>
              </div>
            </div>
            <span v-if="errors.phone" class="error-message">{{ errors.phone }}</span>
            <span v-else class="helper-text">{{ phoneHelperText }}</span>
          </div>

          <!-- Password -->
          <div class="input-group">
            <label class="input-label">Password *</label>
            <div class="input-wrapper" :class="{ error: errors.password, success: isPasswordValid }">
              <ion-icon :icon="lockClosedOutline" class="input-icon"></ion-icon>
              <input
                v-model="password"
                type="password"
                placeholder="Min. 8 characters"
                class="custom-input with-icon"
                :disabled="isEmailVerified"
                autocomplete="new-password"
                @input="validateField('password')"
                @blur="validateField('password')"
              />
              <ion-icon
                :icon="showPassword ? eyeOffOutline : eyeOutline"
                class="password-toggle"
                @click="showPassword = !showPassword"
              ></ion-icon>
            </div>
            <span v-if="errors.password" class="error-message">{{ errors.password }}</span>
            <span v-else class="helper-text">At least 8 characters</span>
          </div>

          <!-- Confirm Password -->
          <div class="input-group">
            <label class="input-label">Confirm Password *</label>
            <div class="input-wrapper" :class="{ error: errors.confirmPassword, success: isConfirmPasswordValid }">
              <ion-icon :icon="lockClosedOutline" class="input-icon"></ion-icon>
              <input
                v-model="confirmPassword"
                :type="showConfirmPassword ? 'text' : 'password'"
                placeholder="Repeat password"
                class="custom-input with-icon"
                :disabled="isEmailVerified"
                autocomplete="new-password"
                @input="validateField('confirmPassword')"
                @blur="validateField('confirmPassword')"
              />
              <ion-icon
                :icon="showConfirmPassword ? eyeOffOutline : eyeOutline"
                class="password-toggle"
                @click="showConfirmPassword = !showConfirmPassword"
              ></ion-icon>
            </div>
            <span v-if="errors.confirmPassword" class="error-message">{{ errors.confirmPassword }}</span>
            <span v-else-if="isConfirmPasswordValid" class="success-message">Passwords match</span>
            <span v-else class="helper-text">Re-enter your password</span>
          </div>

          <!-- Send OTP Button (shows when email is valid but OTP not sent) -->
          <ion-button
            v-if="isEmailValid && !showOtpSection && !isEmailVerified && !emailExistsError && isPasswordValid && isConfirmPasswordValid"
            expand="block"
            class="send-otp-button"
            :disabled="isSendingOtp || isCheckingEmail"
            @click="sendOtp"
          >
            <ion-icon slot="start" :icon="paperPlaneOutline"></ion-icon>
            <span v-if="!isSendingOtp">Send Verification Code</span>
            <ion-spinner v-else name="crescent"></ion-spinner>
          </ion-button>

          <!-- OTP Section (shows after sending OTP) -->
          <div v-if="showOtpSection && !isEmailVerified" class="otp-section">
            <div class="otp-header">
              <ion-icon :icon="shieldCheckmarkOutline"></ion-icon>
              <div class="otp-text">
                <h3>Verify Email Address</h3>
                <p>
                  Enter the 6-digit code sent to <strong>{{ maskedEmail }}</strong>
                </p>
              </div>
            </div>

            <div class="otp-inputs">
              <input
                v-for="(digit, index) in otpDigits"
                :key="index"
                :ref="(el) => (otpInputs[index] = el)"
                v-model="otpDigits[index]"
                type="tel"
                maxlength="1"
                class="otp-input"
                :class="{ error: otpError }"
                @input="onOtpInput(index)"
                @keydown="onOtpKeydown($event, index)"
                @paste="onOtpPaste"
              />
            </div>

            <div v-if="otpError" class="otp-error">
              <ion-icon :icon="alertCircleOutline"></ion-icon>
              <span>{{ otpError }}</span>
            </div>

            <div class="otp-actions">
              <button class="resend-button" :disabled="resendTimer > 0" @click="resendOtp">
                {{ resendTimer > 0 ? `Resend in ${resendTimer}s` : 'Resend Code' }}
              </button>
              <button class="change-number-button" @click="changeEmail">Change Email</button>
            </div>

            <ion-button
              expand="block"
              class="verify-button"
              :disabled="otpValue.length !== 6 || isVerifyingOtp"
              @click="verifyOtp"
            >
              <span v-if="!isVerifyingOtp">Verify Code</span>
              <ion-spinner v-else name="crescent"></ion-spinner>
            </ion-button>
          </div>

          <!-- Info Box -->
          <div class="info-box">
            <ion-icon :icon="informationCircleOutline"></ion-icon>
            <div class="info-text">
              <strong>Why we need this:</strong><br />
              Your email address will be verified with a one-time code to ensure account security.
            </div>
          </div>

          <!-- Next Button (only enabled when email is verified) -->
          <ion-button expand="block" class="next-button" @click="handleNext" :disabled="!isFormValid">
            Next
            <ion-icon slot="end" :icon="arrowForwardOutline"></ion-icon>
          </ion-button>
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { API_BASE } from '@/config/api';
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { IonPage, IonContent, IonButton, IonIcon, IonSpinner, toastController } from '@ionic/vue';
import {
  arrowBackOutline,
  arrowForwardOutline,
  mailOutline,
  callOutline,
  lockClosedOutline,
  eyeOutline,
  eyeOffOutline,
  checkmarkCircleOutline,
  shieldCheckmarkOutline,
  paperPlaneOutline,
  informationCircleOutline,
  alertCircleOutline,
} from 'ionicons/icons';

const router = useRouter();

// Country codes for phone numbers (all countries)
const countryCodes = ref([
  { code: '+93', country: 'Afghanistan', abbr: 'AF', digits: 9 },
  { code: '+355', country: 'Albania', abbr: 'AL', digits: 9 },
  { code: '+213', country: 'Algeria', abbr: 'DZ', digits: 9 },
  { code: '+1-684', country: 'American Samoa', abbr: 'AS', digits: 7 },
  { code: '+376', country: 'Andorra', abbr: 'AD', digits: 9 },
  { code: '+244', country: 'Angola', abbr: 'AO', digits: 9 },
  { code: '+1-264', country: 'Anguilla', abbr: 'AI', digits: 7 },
  { code: '+672', country: 'Antarctica', abbr: 'AQ', digits: 6 },
  { code: '+1-268', country: 'Antigua and Barbuda', abbr: 'AG', digits: 7 },
  { code: '+54', country: 'Argentina', abbr: 'AR', digits: 10 },
  { code: '+374', country: 'Armenia', abbr: 'AM', digits: 8 },
  { code: '+297', country: 'Aruba', abbr: 'AW', digits: 7 },
  { code: '+61', country: 'Australia', abbr: 'AU', digits: 9 },
  { code: '+43', country: 'Austria', abbr: 'AT', digits: 10 },
  { code: '+994', country: 'Azerbaijan', abbr: 'AZ', digits: 9 },
  { code: '+1-242', country: 'Bahamas', abbr: 'BS', digits: 7 },
  { code: '+973', country: 'Bahrain', abbr: 'BH', digits: 8 },
  { code: '+880', country: 'Bangladesh', abbr: 'BD', digits: 10 },
  { code: '+1-246', country: 'Barbados', abbr: 'BB', digits: 7 },
  { code: '+375', country: 'Belarus', abbr: 'BY', digits: 9 },
  { code: '+32', country: 'Belgium', abbr: 'BE', digits: 9 },
  { code: '+501', country: 'Belize', abbr: 'BZ', digits: 7 },
  { code: '+229', country: 'Benin', abbr: 'BJ', digits: 8 },
  { code: '+1-441', country: 'Bermuda', abbr: 'BM', digits: 7 },
  { code: '+975', country: 'Bhutan', abbr: 'BT', digits: 8 },
  { code: '+591', country: 'Bolivia', abbr: 'BO', digits: 8 },
  { code: '+387', country: 'Bosnia and Herzegovina', abbr: 'BA', digits: 8 },
  { code: '+267', country: 'Botswana', abbr: 'BW', digits: 8 },
  { code: '+55', country: 'Brazil', abbr: 'BR', digits: 11 },
  { code: '+246', country: 'British Indian Ocean Territory', abbr: 'IO', digits: 7 },
  { code: '+1-284', country: 'British Virgin Islands', abbr: 'VG', digits: 7 },
  { code: '+673', country: 'Brunei', abbr: 'BN', digits: 7 },
  { code: '+359', country: 'Bulgaria', abbr: 'BG', digits: 9 },
  { code: '+226', country: 'Burkina Faso', abbr: 'BF', digits: 8 },
  { code: '+257', country: 'Burundi', abbr: 'BI', digits: 8 },
  { code: '+855', country: 'Cambodia', abbr: 'KH', digits: 9 },
  { code: '+237', country: 'Cameroon', abbr: 'CM', digits: 9 },
  { code: '+1', country: 'Canada', abbr: 'CA', digits: 10 },
  { code: '+238', country: 'Cape Verde', abbr: 'CV', digits: 7 },
  { code: '+1-345', country: 'Cayman Islands', abbr: 'KY', digits: 7 },
  { code: '+236', country: 'Central African Republic', abbr: 'CF', digits: 8 },
  { code: '+235', country: 'Chad', abbr: 'TD', digits: 8 },
  { code: '+56', country: 'Chile', abbr: 'CL', digits: 9 },
  { code: '+86', country: 'China', abbr: 'CN', digits: 11 },
  { code: '+61', country: 'Christmas Island', abbr: 'CX', digits: 9 },
  { code: '+61', country: 'Cocos Islands', abbr: 'CC', digits: 9 },
  { code: '+57', country: 'Colombia', abbr: 'CO', digits: 10 },
  { code: '+269', country: 'Comoros', abbr: 'KM', digits: 7 },
  { code: '+682', country: 'Cook Islands', abbr: 'CK', digits: 5 },
  { code: '+506', country: 'Costa Rica', abbr: 'CR', digits: 8 },
  { code: '+385', country: 'Croatia', abbr: 'HR', digits: 9 },
  { code: '+53', country: 'Cuba', abbr: 'CU', digits: 8 },
  { code: '+599', country: 'Curacao', abbr: 'CW', digits: 7 },
  { code: '+357', country: 'Cyprus', abbr: 'CY', digits: 8 },
  { code: '+420', country: 'Czech Republic', abbr: 'CZ', digits: 9 },
  { code: '+243', country: 'Democratic Republic of the Congo', abbr: 'CD', digits: 9 },
  { code: '+45', country: 'Denmark', abbr: 'DK', digits: 8 },
  { code: '+253', country: 'Djibouti', abbr: 'DJ', digits: 8 },
  { code: '+1-767', country: 'Dominica', abbr: 'DM', digits: 7 },
  { code: '+1-809', country: 'Dominican Republic', abbr: 'DO', digits: 10 },
  { code: '+670', country: 'East Timor', abbr: 'TL', digits: 8 },
  { code: '+593', country: 'Ecuador', abbr: 'EC', digits: 9 },
  { code: '+20', country: 'Egypt', abbr: 'EG', digits: 10 },
  { code: '+503', country: 'El Salvador', abbr: 'SV', digits: 8 },
  { code: '+240', country: 'Equatorial Guinea', abbr: 'GQ', digits: 9 },
  { code: '+291', country: 'Eritrea', abbr: 'ER', digits: 7 },
  { code: '+372', country: 'Estonia', abbr: 'EE', digits: 8 },
  { code: '+251', country: 'Ethiopia', abbr: 'ET', digits: 9 },
  { code: '+500', country: 'Falkland Islands', abbr: 'FK', digits: 5 },
  { code: '+298', country: 'Faroe Islands', abbr: 'FO', digits: 6 },
  { code: '+679', country: 'Fiji', abbr: 'FJ', digits: 7 },
  { code: '+358', country: 'Finland', abbr: 'FI', digits: 9 },
  { code: '+33', country: 'France', abbr: 'FR', digits: 9 },
  { code: '+689', country: 'French Polynesia', abbr: 'PF', digits: 8 },
  { code: '+241', country: 'Gabon', abbr: 'GA', digits: 8 },
  { code: '+220', country: 'Gambia', abbr: 'GM', digits: 7 },
  { code: '+995', country: 'Georgia', abbr: 'GE', digits: 9 },
  { code: '+49', country: 'Germany', abbr: 'DE', digits: 11 },
  { code: '+233', country: 'Ghana', abbr: 'GH', digits: 9 },
  { code: '+350', country: 'Gibraltar', abbr: 'GI', digits: 8 },
  { code: '+30', country: 'Greece', abbr: 'GR', digits: 10 },
  { code: '+299', country: 'Greenland', abbr: 'GL', digits: 6 },
  { code: '+1-473', country: 'Grenada', abbr: 'GD', digits: 7 },
  { code: '+1-671', country: 'Guam', abbr: 'GU', digits: 7 },
  { code: '+502', country: 'Guatemala', abbr: 'GT', digits: 8 },
  { code: '+44-1481', country: 'Guernsey', abbr: 'GG', digits: 6 },
  { code: '+224', country: 'Guinea', abbr: 'GN', digits: 9 },
  { code: '+245', country: 'Guinea-Bissau', abbr: 'GW', digits: 7 },
  { code: '+592', country: 'Guyana', abbr: 'GY', digits: 7 },
  { code: '+509', country: 'Haiti', abbr: 'HT', digits: 8 },
  { code: '+504', country: 'Honduras', abbr: 'HN', digits: 8 },
  { code: '+852', country: 'Hong Kong', abbr: 'HK', digits: 8 },
  { code: '+36', country: 'Hungary', abbr: 'HU', digits: 9 },
  { code: '+354', country: 'Iceland', abbr: 'IS', digits: 7 },
  { code: '+91', country: 'India', abbr: 'IN', digits: 10 },
  { code: '+62', country: 'Indonesia', abbr: 'ID', digits: 10 },
  { code: '+98', country: 'Iran', abbr: 'IR', digits: 10 },
  { code: '+964', country: 'Iraq', abbr: 'IQ', digits: 10 },
  { code: '+353', country: 'Ireland', abbr: 'IE', digits: 9 },
  { code: '+44-1624', country: 'Isle of Man', abbr: 'IM', digits: 6 },
  { code: '+972', country: 'Israel', abbr: 'IL', digits: 9 },
  { code: '+39', country: 'Italy', abbr: 'IT', digits: 10 },
  { code: '+225', country: 'Ivory Coast', abbr: 'CI', digits: 8 },
  { code: '+1-876', country: 'Jamaica', abbr: 'JM', digits: 7 },
  { code: '+81', country: 'Japan', abbr: 'JP', digits: 10 },
  { code: '+44-1534', country: 'Jersey', abbr: 'JE', digits: 6 },
  { code: '+962', country: 'Jordan', abbr: 'JO', digits: 9 },
  { code: '+7', country: 'Kazakhstan', abbr: 'KZ', digits: 10 },
  { code: '+254', country: 'Kenya', abbr: 'KE', digits: 9 },
  { code: '+686', country: 'Kiribati', abbr: 'KI', digits: 5 },
  { code: '+383', country: 'Kosovo', abbr: 'XK', digits: 8 },
  { code: '+965', country: 'Kuwait', abbr: 'KW', digits: 8 },
  { code: '+996', country: 'Kyrgyzstan', abbr: 'KG', digits: 9 },
  { code: '+856', country: 'Laos', abbr: 'LA', digits: 9 },
  { code: '+371', country: 'Latvia', abbr: 'LV', digits: 8 },
  { code: '+961', country: 'Lebanon', abbr: 'LB', digits: 8 },
  { code: '+266', country: 'Lesotho', abbr: 'LS', digits: 8 },
  { code: '+231', country: 'Liberia', abbr: 'LR', digits: 7 },
  { code: '+218', country: 'Libya', abbr: 'LY', digits: 9 },
  { code: '+423', country: 'Liechtenstein', abbr: 'LI', digits: 9 },
  { code: '+370', country: 'Lithuania', abbr: 'LT', digits: 8 },
  { code: '+352', country: 'Luxembourg', abbr: 'LU', digits: 9 },
  { code: '+853', country: 'Macau', abbr: 'MO', digits: 8 },
  { code: '+389', country: 'Macedonia', abbr: 'MK', digits: 8 },
  { code: '+261', country: 'Madagascar', abbr: 'MG', digits: 9 },
  { code: '+265', country: 'Malawi', abbr: 'MW', digits: 9 },
  { code: '+60', country: 'Malaysia', abbr: 'MY', digits: 10 },
  { code: '+960', country: 'Maldives', abbr: 'MV', digits: 7 },
  { code: '+223', country: 'Mali', abbr: 'ML', digits: 8 },
  { code: '+356', country: 'Malta', abbr: 'MT', digits: 8 },
  { code: '+692', country: 'Marshall Islands', abbr: 'MH', digits: 7 },
  { code: '+222', country: 'Mauritania', abbr: 'MR', digits: 8 },
  { code: '+230', country: 'Mauritius', abbr: 'MU', digits: 8 },
  { code: '+262', country: 'Mayotte', abbr: 'YT', digits: 9 },
  { code: '+52', country: 'Mexico', abbr: 'MX', digits: 10 },
  { code: '+691', country: 'Micronesia', abbr: 'FM', digits: 7 },
  { code: '+373', country: 'Moldova', abbr: 'MD', digits: 8 },
  { code: '+377', country: 'Monaco', abbr: 'MC', digits: 8 },
  { code: '+976', country: 'Mongolia', abbr: 'MN', digits: 8 },
  { code: '+382', country: 'Montenegro', abbr: 'ME', digits: 8 },
  { code: '+1-664', country: 'Montserrat', abbr: 'MS', digits: 7 },
  { code: '+212', country: 'Morocco', abbr: 'MA', digits: 9 },
  { code: '+258', country: 'Mozambique', abbr: 'MZ', digits: 9 },
  { code: '+95', country: 'Myanmar', abbr: 'MM', digits: 9 },
  { code: '+264', country: 'Namibia', abbr: 'NA', digits: 9 },
  { code: '+674', country: 'Nauru', abbr: 'NR', digits: 7 },
  { code: '+977', country: 'Nepal', abbr: 'NP', digits: 10 },
  { code: '+31', country: 'Netherlands', abbr: 'NL', digits: 9 },
  { code: '+687', country: 'New Caledonia', abbr: 'NC', digits: 6 },
  { code: '+64', country: 'New Zealand', abbr: 'NZ', digits: 9 },
  { code: '+505', country: 'Nicaragua', abbr: 'NI', digits: 8 },
  { code: '+227', country: 'Niger', abbr: 'NE', digits: 8 },
  { code: '+234', country: 'Nigeria', abbr: 'NG', digits: 10 },
  { code: '+683', country: 'Niue', abbr: 'NU', digits: 4 },
  { code: '+850', country: 'North Korea', abbr: 'KP', digits: 10 },
  { code: '+1-670', country: 'Northern Mariana Islands', abbr: 'MP', digits: 7 },
  { code: '+47', country: 'Norway', abbr: 'NO', digits: 8 },
  { code: '+968', country: 'Oman', abbr: 'OM', digits: 8 },
  { code: '+92', country: 'Pakistan', abbr: 'PK', digits: 10 },
  { code: '+680', country: 'Palau', abbr: 'PW', digits: 7 },
  { code: '+970', country: 'Palestine', abbr: 'PS', digits: 9 },
  { code: '+507', country: 'Panama', abbr: 'PA', digits: 8 },
  { code: '+675', country: 'Papua New Guinea', abbr: 'PG', digits: 8 },
  { code: '+595', country: 'Paraguay', abbr: 'PY', digits: 9 },
  { code: '+51', country: 'Peru', abbr: 'PE', digits: 9 },
  { code: '+63', country: 'Philippines', abbr: 'PH', digits: 10 },
  { code: '+64', country: 'Pitcairn', abbr: 'PN', digits: 9 },
  { code: '+48', country: 'Poland', abbr: 'PL', digits: 9 },
  { code: '+351', country: 'Portugal', abbr: 'PT', digits: 9 },
  { code: '+1-787', country: 'Puerto Rico', abbr: 'PR', digits: 10 },
  { code: '+974', country: 'Qatar', abbr: 'QA', digits: 8 },
  { code: '+242', country: 'Republic of the Congo', abbr: 'CG', digits: 9 },
  { code: '+262', country: 'Reunion', abbr: 'RE', digits: 9 },
  { code: '+40', country: 'Romania', abbr: 'RO', digits: 10 },
  { code: '+7', country: 'Russia', abbr: 'RU', digits: 10 },
  { code: '+250', country: 'Rwanda', abbr: 'RW', digits: 9 },
  { code: '+590', country: 'Saint Barthelemy', abbr: 'BL', digits: 9 },
  { code: '+290', country: 'Saint Helena', abbr: 'SH', digits: 4 },
  { code: '+1-869', country: 'Saint Kitts and Nevis', abbr: 'KN', digits: 7 },
  { code: '+1-758', country: 'Saint Lucia', abbr: 'LC', digits: 7 },
  { code: '+590', country: 'Saint Martin', abbr: 'MF', digits: 9 },
  { code: '+508', country: 'Saint Pierre and Miquelon', abbr: 'PM', digits: 6 },
  { code: '+1-784', country: 'Saint Vincent and the Grenadines', abbr: 'VC', digits: 7 },
  { code: '+685', country: 'Samoa', abbr: 'WS', digits: 5 },
  { code: '+378', country: 'San Marino', abbr: 'SM', digits: 9 },
  { code: '+239', country: 'Sao Tome and Principe', abbr: 'ST', digits: 7 },
  { code: '+966', country: 'Saudi Arabia', abbr: 'SA', digits: 9 },
  { code: '+221', country: 'Senegal', abbr: 'SN', digits: 9 },
  { code: '+381', country: 'Serbia', abbr: 'RS', digits: 9 },
  { code: '+248', country: 'Seychelles', abbr: 'SC', digits: 7 },
  { code: '+232', country: 'Sierra Leone', abbr: 'SL', digits: 8 },
  { code: '+65', country: 'Singapore', abbr: 'SG', digits: 8 },
  { code: '+1-721', country: 'Sint Maarten', abbr: 'SX', digits: 7 },
  { code: '+421', country: 'Slovakia', abbr: 'SK', digits: 9 },
  { code: '+386', country: 'Slovenia', abbr: 'SI', digits: 9 },
  { code: '+677', country: 'Solomon Islands', abbr: 'SB', digits: 7 },
  { code: '+252', country: 'Somalia', abbr: 'SO', digits: 8 },
  { code: '+27', country: 'South Africa', abbr: 'ZA', digits: 9 },
  { code: '+82', country: 'South Korea', abbr: 'KR', digits: 10 },
  { code: '+211', country: 'South Sudan', abbr: 'SS', digits: 9 },
  { code: '+34', country: 'Spain', abbr: 'ES', digits: 9 },
  { code: '+94', country: 'Sri Lanka', abbr: 'LK', digits: 9 },
  { code: '+249', country: 'Sudan', abbr: 'SD', digits: 9 },
  { code: '+597', country: 'Suriname', abbr: 'SR', digits: 7 },
  { code: '+47', country: 'Svalbard and Jan Mayen', abbr: 'SJ', digits: 8 },
  { code: '+268', country: 'Swaziland', abbr: 'SZ', digits: 8 },
  { code: '+46', country: 'Sweden', abbr: 'SE', digits: 9 },
  { code: '+41', country: 'Switzerland', abbr: 'CH', digits: 9 },
  { code: '+963', country: 'Syria', abbr: 'SY', digits: 9 },
  { code: '+886', country: 'Taiwan', abbr: 'TW', digits: 9 },
  { code: '+992', country: 'Tajikistan', abbr: 'TJ', digits: 9 },
  { code: '+255', country: 'Tanzania', abbr: 'TZ', digits: 9 },
  { code: '+66', country: 'Thailand', abbr: 'TH', digits: 9 },
  { code: '+228', country: 'Togo', abbr: 'TG', digits: 8 },
  { code: '+690', country: 'Tokelau', abbr: 'TK', digits: 4 },
  { code: '+676', country: 'Tonga', abbr: 'TO', digits: 5 },
  { code: '+1-868', country: 'Trinidad and Tobago', abbr: 'TT', digits: 7 },
  { code: '+216', country: 'Tunisia', abbr: 'TN', digits: 8 },
  { code: '+90', country: 'Turkey', abbr: 'TR', digits: 10 },
  { code: '+993', country: 'Turkmenistan', abbr: 'TM', digits: 8 },
  { code: '+1-649', country: 'Turks and Caicos Islands', abbr: 'TC', digits: 7 },
  { code: '+688', country: 'Tuvalu', abbr: 'TV', digits: 5 },
  { code: '+1-340', country: 'U.S. Virgin Islands', abbr: 'VI', digits: 7 },
  { code: '+256', country: 'Uganda', abbr: 'UG', digits: 9 },
  { code: '+380', country: 'Ukraine', abbr: 'UA', digits: 9 },
  { code: '+971', country: 'United Arab Emirates', abbr: 'AE', digits: 9 },
  { code: '+44', country: 'United Kingdom', abbr: 'GB', digits: 10 },
  { code: '+1', country: 'United States', abbr: 'US', digits: 10 },
  { code: '+598', country: 'Uruguay', abbr: 'UY', digits: 8 },
  { code: '+998', country: 'Uzbekistan', abbr: 'UZ', digits: 9 },
  { code: '+678', country: 'Vanuatu', abbr: 'VU', digits: 5 },
  { code: '+39', country: 'Vatican', abbr: 'VA', digits: 10 },
  { code: '+58', country: 'Venezuela', abbr: 'VE', digits: 10 },
  { code: '+84', country: 'Vietnam', abbr: 'VN', digits: 9 },
  { code: '+681', country: 'Wallis and Futuna', abbr: 'WF', digits: 6 },
  { code: '+212', country: 'Western Sahara', abbr: 'EH', digits: 9 },
  { code: '+967', country: 'Yemen', abbr: 'YE', digits: 9 },
  { code: '+260', country: 'Zambia', abbr: 'ZM', digits: 9 },
  { code: '+263', country: 'Zimbabwe', abbr: 'ZW', digits: 9 },
]);

const countryCode = ref('+63');

// Form data
const email = ref('');
const phone = ref('');
const password = ref('');
const confirmPassword = ref('');
const showPassword = ref(false);
const showConfirmPassword = ref(false);
const isEmailVerified = ref(false);
const isCheckingEmail = ref(false);
const emailExistsError = ref('');

// OTP data
const showOtpSection = ref(false);
const otpDigits = ref(['', '', '', '', '', '']);
const otpInputs = ref([]);
const otpError = ref('');
const resendTimer = ref(0);
const isSendingOtp = ref(false);
const isVerifyingOtp = ref(false);

// Errors
const errors = ref({
  email: '',
  phone: '',
  password: '',
  confirmPassword: '',
});

// Validation
const isEmailValid = computed(() => {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return email.value && emailRegex.test(email.value);
});

const currentCountry = computed(() => {
  return countryCodes.value.find((c) => c.code === countryCode.value) || countryCodes.value[0];
});

const maxPhoneLength = computed(() => {
  return currentCountry.value.digits;
});

const phonePlaceholder = computed(() => {
  const digits = currentCountry.value.digits;
  if (countryCode.value === '+63') return '9171234567';
  return '0'.repeat(digits);
});

const phoneHelperText = computed(() => {
  return `${currentCountry.value.digits} digits for ${currentCountry.value.country}`;
});

const isPhoneValid = computed(() => {
  return phone.value.length === currentCountry.value.digits && /^\d+$/.test(phone.value);
});

const isPasswordValid = computed(() => {
  return password.value.length >= 8;
});

const isConfirmPasswordValid = computed(() => {
  return confirmPassword.value === password.value && password.value.length >= 8;
});

const otpValue = computed(() => {
  return otpDigits.value.join('');
});

const isFormValid = computed(() => {
  return (
    isEmailValid.value &&
    isEmailVerified.value &&
    !emailExistsError.value &&
    isPhoneValid.value &&
    phone.value.length === currentCountry.value.digits &&
    isPasswordValid.value &&
    isConfirmPasswordValid.value
  );
});

// Check if email already exists in the system
const checkEmailExists = async () => {
  if (!isEmailValid.value || isEmailVerified.value) {
    emailExistsError.value = '';
    return;
  }

  isCheckingEmail.value = true;
  emailExistsError.value = '';

  try {
    const response = await fetch(`${API_BASE}/seller/login`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email: email.value }),
    });

    if (response.status === 404) {
      emailExistsError.value = '';
    } else if (response.status === 200) {
      emailExistsError.value = 'This email is already registered.';
    } else {
      const data = await response.json();
      if (data.status === 'rejected') {
        emailExistsError.value = '';
      } else {
        emailExistsError.value = 'This email is already registered.';
      }
    }
  } catch {
    emailExistsError.value = '';
  } finally {
    isCheckingEmail.value = false;
  }
};

// Email blur handler
const onEmailBlur = () => {
  validateField('email');
  if (isEmailValid.value && !isEmailVerified.value) {
    checkEmailExists();
  }
};

// Email input handler
const onEmailInput = () => {
  errors.value.email = '';
  emailExistsError.value = '';
};

// Phone input handler
const onPhoneInput = () => {
  // Only allow numbers
  phone.value = phone.value.replace(/\D/g, '');
  // Limit to max digits
  if (phone.value.length > currentCountry.value.digits) {
    phone.value = phone.value.slice(0, currentCountry.value.digits);
  }
  errors.value.phone = '';
};

// Password input handler
const onPasswordInput = () => {
  errors.value.password = '';
};

// Confirm password input handler
const onConfirmPasswordInput = () => {
  errors.value.confirmPassword = '';
};

// Validate field
const validateField = (field) => {
  if (field === 'email') {
    if (!email.value) {
      errors.value.email = 'Email is required';
      return false;
    }
    if (!isEmailValid.value) {
      errors.value.email = 'Please enter a valid email';
      return false;
    }
    errors.value.email = '';
    return true;
  }

  if (field === 'phone') {
    if (!phone.value) {
      errors.value.phone = 'Phone number is required';
      return false;
    }
    if (phone.value.length !== currentCountry.value.digits) {
      errors.value.phone = `Phone must be ${currentCountry.value.digits} digits`;
      return false;
    }
    if (!/^\d+$/.test(phone.value)) {
      errors.value.phone = 'Phone must contain only numbers';
      return false;
    }
    errors.value.phone = '';
    return true;
  }

  if (field === 'password') {
    if (!password.value) {
      errors.value.password = 'Password is required';
      return false;
    }
    if (password.value.length < 8) {
      errors.value.password = 'Password must be at least 8 characters';
      return false;
    }
    errors.value.password = '';
    return true;
  }

  if (field === 'confirmPassword') {
    if (!confirmPassword.value) {
      errors.value.confirmPassword = 'Please confirm your password';
      return false;
    }
    if (confirmPassword.value !== password.value) {
      errors.value.confirmPassword = 'Passwords do not match';
      return false;
    }
    errors.value.confirmPassword = '';
    return true;
  }
};

// Format phone for display
const formatPhone = (phoneNum) => {
  if (!phoneNum || phoneNum.length !== 11) return phoneNum;
  return `${phoneNum.slice(0, 4)} ${phoneNum.slice(4, 7)} ${phoneNum.slice(7)}`;
};

// Mask email for display
const maskedEmail = computed(() => {
  if (!email.value) return '';

  const atIndex = email.value.indexOf('@');
  if (atIndex === -1) return email.value;

  const localPart = email.value.substring(0, atIndex);
  const domain = email.value.substring(atIndex);

  if (localPart.length > 3) {
    return localPart.substring(0, 2) + '***' + localPart.substring(localPart.length - 1) + domain;
  } else if (localPart.length > 1) {
    return localPart.substring(0, 1) + '***' + domain;
  }

  return '***' + domain;
});

// Send OTP to email
const sendOtp = async () => {
  if (!validateField('email')) return;

  isSendingOtp.value = true;

  try {
    const response = await fetch(`${API_BASE}/send-otp`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email: email.value }),
    });

    const data = await response.json();

    if (!response.ok) {
      throw new Error(data.message || 'Failed to send code.');
    }

    const toast = await toastController.create({
      message: `Verification code sent to ${maskedEmail.value}`,
      duration: 3000,
      color: 'success',
      position: 'top',
    });
    await toast.present();

    showOtpSection.value = true;
    startResendTimer();

    setTimeout(() => {
      if (otpInputs.value[0]) {
        otpInputs.value[0].focus();
      }
    }, 300);
  } catch (error) {
    const toast = await toastController.create({
      message: error.message || 'Failed to send code. Please try again.',
      duration: 3000,
      color: 'danger',
      position: 'top',
    });
    await toast.present();
  } finally {
    isSendingOtp.value = false;
  }
};

// Resend OTP
const resendOtp = async () => {
  if (resendTimer.value > 0) return;

  // Clear OTP inputs
  otpDigits.value = ['', '', '', '', '', ''];
  otpError.value = '';

  // Resend
  await sendOtp();
};

// Start resend timer
const startResendTimer = () => {
  resendTimer.value = 60;
  const interval = setInterval(() => {
    resendTimer.value--;
    if (resendTimer.value <= 0) {
      clearInterval(interval);
    }
  }, 1000);
};

// Change email
const changeEmail = () => {
  showOtpSection.value = false;
  otpDigits.value = ['', '', '', '', '', ''];
  otpError.value = '';
  isEmailVerified.value = false;
  resendTimer.value = 0;
};

// OTP input handler
const onOtpInput = (index) => {
  // Only allow numbers
  otpDigits.value[index] = otpDigits.value[index].replace(/\D/g, '');

  // Clear error
  otpError.value = '';

  // Auto-focus next input
  if (otpDigits.value[index] && index < 5) {
    otpInputs.value[index + 1]?.focus();
  }
};

// OTP keydown handler
const onOtpKeydown = (event, index) => {
  // Backspace handling
  if (event.key === 'Backspace' && !otpDigits.value[index] && index > 0) {
    otpInputs.value[index - 1]?.focus();
  }
};

// OTP paste handler
const onOtpPaste = (event) => {
  event.preventDefault();
  const pastedData = event.clipboardData.getData('text').replace(/\D/g, '');

  if (pastedData.length === 6) {
    for (let i = 0; i < 6; i++) {
      otpDigits.value[i] = pastedData[i] || '';
    }
    otpInputs.value[5]?.focus();
  }
};

// Verify OTP
const verifyOtp = async () => {
  if (otpValue.value.length !== 6) return;

  isVerifyingOtp.value = true;
  otpError.value = '';

  try {
    const response = await fetch(`${API_BASE}/verify-otp`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email: email.value, otp: otpValue.value }),
    });

    const data = await response.json();

    if (!response.ok) {
      otpError.value = data.message || 'Invalid code. Please try again.';
      return;
    }

    isEmailVerified.value = true;
    showOtpSection.value = false;

    const toast = await toastController.create({
      message: 'Email verified successfully!',
      duration: 2500,
      color: 'success',
      position: 'top',
    });
    await toast.present();
  } catch (error) {
    otpError.value = 'Verification failed. Please try again.';
  } finally {
    isVerifyingOtp.value = false;
  }
};

// Navigation
const goBack = () => {
  router.push('/welcome');
};

const handleNext = () => {
  if (!isFormValid.value) return;

  // Save data
  const registrationData = {
    email: email.value,
    countryCode: countryCode.value,
    phone: phone.value,
    fullPhone: `${countryCode.value}${phone.value}`,
    emailVerified: isEmailVerified.value,
    password: password.value,
  };
  localStorage.setItem('registrationData', JSON.stringify(registrationData));

  // Navigate to Step 2
  router.push('/register/step2');
};
</script>

<style scoped>
.step-content {
  --background: var(--color-bg-primary);
}

.step-container {
  min-height: 100vh;
  padding: 24px;
  padding-bottom: 100px;
  position: relative;
}

/* Back Button */
.back-button {
  position: absolute;
  top: 24px;
  left: 24px;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--color-bg-tertiary);
  border-radius: 12px;
  cursor: pointer;
  z-index: 10;
  transition: background 0.2s;
}

.back-button:active {
  transform: scale(0.95);
  background: var(--color-border);
}

.back-button ion-icon {
  font-size: 24px;
  color: var(--color-text-primary);
}

/* Progress Section */
.progress-section {
  margin-top: 70px;
  margin-bottom: 32px;
}

.progress-bar {
  width: 100%;
  height: 6px;
  background: var(--color-border);
  border-radius: 3px;
  overflow: hidden;
  margin-bottom: 8px;
}

.progress-fill {
  height: 100%;
  background: var(--color-primary);
  transition: width 0.3s ease;
}

.progress-text {
  text-align: center;
  font-size: 13px;
  color: var(--color-text-secondary);
  margin: 0;
  font-weight: 600;
}

/* Title Section */
.title-section {
  text-align: center;
  margin-bottom: 40px;
}

.title {
  font-size: 28px;
  font-weight: 700;
  color: var(--color-text-primary);
  margin: 0 0 8px 0;
}

.subtitle {
  font-size: 15px;
  color: var(--color-text-secondary);
  margin: 0;
}

/* Form Section */
.form-section {
  max-width: 500px;
  margin: 0 auto;
  width: 100%;
}

/* Input Group */
.input-group {
  margin-bottom: 20px;
}

.input-label {
  display: block;
  font-size: 14px;
  font-weight: 600;
  color: var(--color-text-primary);
  margin-bottom: 8px;
}

.input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.input-icon {
  position: absolute;
  left: 16px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 20px;
  color: var(--color-icon-primary);
  z-index: 2;
}

.custom-input {
  width: 100%;
  padding: 14px 16px;
  border: 2px solid var(--color-border);
  border-radius: 12px;
  font-size: 15px;
  background: var(--color-input-bg);
  color: var(--color-text-primary);
  transition: border-color 0.2s;
  font-family: inherit;
}

.custom-input.with-icon {
  padding-left: 48px;
  padding-right: 48px;
}

.custom-input.with-toggle {
  padding-right: 48px;
}

.custom-input:focus {
  outline: none;
  border-color: var(--color-border-focus);
}

.custom-input:disabled {
  background: var(--color-input-disabled-bg);
  color: var(--color-text-tertiary);
  cursor: not-allowed;
}

.custom-input::placeholder {
  color: var(--color-input-placeholder);
}

/* Phone Input Row with Country Code */
.phone-input-row {
  display: flex;
  gap: 8px;
}

.country-code-wrapper {
  flex-shrink: 0;
}

.country-code-select {
  padding: 14px 36px 14px 16px;
  border: 2px solid var(--color-border);
  border-radius: 12px;
  font-size: 15px;
  background: var(--color-input-bg);
  color: var(--color-text-primary);
  font-family: inherit;
  appearance: none;
  -webkit-appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 12px center;
  cursor: pointer;
  min-width: 110px;
  font-weight: 500;
}

.country-code-select:focus {
  outline: none;
  border-color: var(--color-border-focus);
}

/* On mobile, the native select picker will be scrollable automatically */
.country-code-select option {
  font-size: 16px;
  padding: 10px;
}

.phone-number-wrapper {
  flex: 1;
  position: relative;
  display: flex;
  align-items: center;
}

.phone-number-wrapper.error .phone-input {
  border-color: var(--color-error);
}

.phone-number-wrapper.success .phone-input {
  border-color: var(--color-success);
}

.phone-input {
  width: 100%;
  padding: 14px 48px 14px 16px;
  border: 2px solid var(--color-border);
  border-radius: 12px;
  font-size: 15px;
  background: var(--color-input-bg);
  color: var(--color-text-primary);
  transition: border-color 0.2s;
  font-family: inherit;
}

.phone-input:focus {
  outline: none;
  border-color: var(--color-border-focus);
}

.phone-input::placeholder {
  color: var(--color-input-placeholder);
}

/* Success/Error Icons */
.success-icon,
.verified-icon {
  position: absolute;
  right: 16px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 24px;
  color: var(--color-success);
  z-index: 2;
}

.password-toggle {
  position: absolute;
  right: 14px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 20px;
  color: var(--color-icon-tertiary);
  z-index: 2;
  cursor: pointer;
}

.verified-icon {
  animation: scaleIn 0.3s ease-out;
}

@keyframes scaleIn {
  0% {
    transform: translateY(-50%) scale(0);
  }
  50% {
    transform: translateY(-50%) scale(1.2);
  }
  100% {
    transform: translateY(-50%) scale(1);
  }
}

/* Input States */
.input-wrapper.success .custom-input {
  border-color: var(--color-success);
}

.input-wrapper.verified .custom-input {
  border-color: var(--color-success);
  background: var(--color-success-bg);
}

.input-wrapper.error .custom-input {
  border-color: var(--color-error);
}

.input-wrapper.error .input-icon {
  color: var(--color-error);
}

/* Helper/Error/Success Text */
.helper-text {
  display: block;
  font-size: 12px;
  color: var(--color-text-tertiary);
  margin-top: 6px;
}

.error-message {
  display: block;
  font-size: 12px;
  color: var(--color-error);
  margin-top: 6px;
  font-weight: 500;
}

.success-message {
  display: block;
  font-size: 12px;
  color: var(--color-success);
  margin-top: 6px;
  font-weight: 600;
}

/* Send OTP Button */
.send-otp-button {
  --background: var(--color-info);
  --background-hover: var(--color-info);
  --border-radius: 12px;
  height: 52px;
  font-size: 15px;
  font-weight: 600;
  text-transform: none;
  margin-bottom: 20px;
}

/* OTP Section */
.otp-section {
  background: var(--color-otp-bg);
  border-radius: 16px;
  padding: 24px;
  margin-bottom: 20px;
  border: 2px solid var(--color-otp-border);
}

.otp-header {
  display: flex;
  gap: 12px;
  margin-bottom: 20px;
}

.otp-header ion-icon {
  font-size: 32px;
  color: var(--color-otp-border);
  flex-shrink: 0;
}

.otp-text h3 {
  font-size: 16px;
  font-weight: 700;
  color: var(--color-otp-text);
  margin: 0 0 6px 0;
}

.otp-text p {
  font-size: 13px;
  color: var(--color-otp-text);
  margin: 0;
  line-height: 1.4;
}

.otp-text strong {
  font-weight: 700;
}

/* OTP Inputs */
.otp-inputs {
  display: flex;
  gap: 8px;
  justify-content: center;
  margin-bottom: 16px;
}

.otp-input {
  width: 48px;
  height: 56px;
  border: 2px solid var(--color-info);
  border-radius: 12px;
  text-align: center;
  font-size: 24px;
  font-weight: 700;
  color: var(--color-text-primary);
  background: var(--color-input-bg);
  transition: all 0.2s;
}

.otp-input:focus {
  outline: none;
  border-color: var(--color-info);
  box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.2);
}

.otp-input.error {
  border-color: var(--color-error);
}

/* OTP Error */
.otp-error {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px;
  background: var(--color-error-bg);
  border-radius: 8px;
  margin-bottom: 16px;
}

.otp-error ion-icon {
  font-size: 20px;
  color: var(--color-error);
  flex-shrink: 0;
}

.otp-error span {
  font-size: 13px;
  color: var(--color-error-text);
  font-weight: 500;
}

/* OTP Actions */
.otp-actions {
  display: flex;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 16px;
}

.resend-button,
.change-number-button {
  background: none;
  border: none;
  color: var(--color-info);
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  padding: 8px;
  font-family: inherit;
}

.resend-button:disabled {
  color: var(--color-text-tertiary);
  cursor: not-allowed;
}

.change-number-button {
  color: var(--color-text-secondary);
}

/* Verify Button */
.verify-button {
  --background: var(--color-info);
  --background-hover: var(--color-info);
  --border-radius: 12px;
  height: 52px;
  font-size: 15px;
  font-weight: 600;
  text-transform: none;
}

.verify-button:disabled {
  --background: var(--color-border);
  --color: var(--color-text-tertiary);
}

/* Info Box */
.info-box {
  display: flex;
  gap: 12px;
  background: var(--color-info-bg);
  border-left: 4px solid var(--color-info-border);
  padding: 16px;
  border-radius: 8px;
  margin-bottom: 24px;
}

.info-box ion-icon {
  font-size: 24px;
  color: var(--color-info-border);
  flex-shrink: 0;
  margin-top: 2px;
}

.info-text {
  font-size: 13px;
  color: var(--color-info-text);
  line-height: 1.5;
}

.info-text strong {
  font-weight: 700;
}

/* Next Button */
.next-button {
  --background: var(--color-primary);
  --background-hover: var(--color-primary-hover);
  --border-radius: 12px;
  height: 52px;
  font-size: 15px;
  font-weight: 600;
  text-transform: none;
}

.next-button:disabled {
  --background: var(--color-border);
  --color: var(--color-text-tertiary);
}

/* Responsive */
@media (max-width: 480px) {
  .title {
    font-size: 24px;
  }

  .step-container {
    padding: 20px 16px 100px 16px;
  }

  .otp-input {
    width: 44px;
    height: 52px;
    font-size: 22px;
  }
}
</style>
