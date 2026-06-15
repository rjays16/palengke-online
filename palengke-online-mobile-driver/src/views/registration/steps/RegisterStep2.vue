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
            <div class="progress-fill" style="width: 28.56%"></div>
          </div>
          <p class="progress-text">Step 2 of 7</p>
        </div>

        <!-- Title -->
        <div class="title-section">
          <h1 class="title">Personal Information</h1>
          <p class="subtitle">Tell us about yourself</p>
        </div>

        <!-- Form -->
        <div class="form-section">
          <!-- Personal Information Section -->
          <div class="section-header">
            <ion-icon :icon="personOutline"></ion-icon>
            <h2>Your Details</h2>
          </div>

          <!-- First Name -->
          <div class="input-group">
            <label class="input-label">First Name *</label>
            <div class="input-wrapper" :class="{ error: errors.firstName }">
              <input
                v-model="formData.firstName"
                type="text"
                placeholder="Juan"
                class="custom-input"
                @blur="validateField('firstName')"
                @input="errors.firstName = ''"
              />
            </div>
            <span v-if="errors.firstName" class="error-message">{{ errors.firstName }}</span>
          </div>

          <!-- Middle Name (Optional) -->
          <div class="input-group">
            <label class="input-label">Middle Name <span class="optional">(Optional)</span></label>
            <div class="input-wrapper">
              <input v-model="formData.middleName" type="text" placeholder="Santos" class="custom-input" />
            </div>
          </div>

          <!-- Last Name -->
          <div class="input-group">
            <label class="input-label">Last Name *</label>
            <div class="input-wrapper" :class="{ error: errors.lastName }">
              <input
                v-model="formData.lastName"
                type="text"
                placeholder="Dela Cruz"
                class="custom-input"
                @blur="validateField('lastName')"
                @input="errors.lastName = ''"
              />
            </div>
            <span v-if="errors.lastName" class="error-message">{{ errors.lastName }}</span>
          </div>

          <!-- Country -->
          <div class="input-group">
            <label class="input-label">Country *</label>
            <div class="input-wrapper" :class="{ error: errors.country }">
              <ion-icon :icon="globeOutline" class="input-icon"></ion-icon>
              <select
                v-model="formData.country"
                class="custom-input with-icon custom-select"
                @blur="validateField('country')"
                @change="errors.country = ''"
              >
                <option value="" disabled>Select your country</option>
                <option v-for="c in countries" :key="c.code" :value="c.code">
                  {{ c.name }}
                </option>
              </select>
            </div>
            <span v-if="errors.country" class="error-message">{{ errors.country }}</span>
          </div>

          <!-- Birthdate -->
          <div class="input-group">
            <label class="input-label">Birthdate *</label>
            <div class="input-wrapper" :class="{ error: errors.birthdate }">
              <ion-icon :icon="calendarOutline" class="input-icon"></ion-icon>
              <input
                v-model="formData.birthdate"
                type="date"
                class="custom-input with-icon"
                :max="maxDate"
                @blur="validateField('birthdate')"
                @input="errors.birthdate = ''"
              />
            </div>
            <span v-if="errors.birthdate" class="error-message">{{ errors.birthdate }}</span>
            <span v-else class="helper-text">You must be 18 years or older</span>
          </div>

          <!-- Birthplace -->
          <div class="input-group">
            <label class="input-label">Birthplace *</label>
            <div class="input-wrapper" :class="{ error: errors.birthplace }">
              <ion-icon :icon="locationOutline" class="input-icon"></ion-icon>
              <input
                v-model="formData.birthplace"
                type="text"
                placeholder="Cagayan de Oro City"
                class="custom-input with-icon"
                @blur="validateField('birthplace')"
                @input="errors.birthplace = ''"
              />
            </div>
            <span v-if="errors.birthplace" class="error-message">{{ errors.birthplace }}</span>
            <span v-else class="helper-text">City or municipality of birth</span>
          </div>

          <!-- Address Section Header -->
          <div class="section-header" style="margin-top: 32px">
            <ion-icon :icon="homeOutline"></ion-icon>
            <h2>Current Address</h2>
          </div>

          <!-- Checkbox: Same as Birthplace -->
          <div class="checkbox-group">
            <input
              type="checkbox"
              id="sameAsBirthplace"
              v-model="sameAsBirthplace"
              @change="onSameAsBirthplaceChange"
              class="checkbox-input"
            />
            <label for="sameAsBirthplace"> Current address is the same as birthplace </label>
          </div>

          <!-- Current Address -->
          <div class="input-group">
            <label class="input-label">Current Address *</label>
            <div class="input-wrapper" :class="{ error: errors.currentAddress }">
              <ion-icon :icon="homeOutline" class="input-icon"></ion-icon>
              <textarea
                v-model="formData.currentAddress"
                placeholder="123 Main Street, Barangay Carmen, Cagayan de Oro City"
                class="custom-textarea with-icon"
                rows="3"
                :disabled="sameAsBirthplace"
                @blur="validateField('currentAddress')"
                @input="errors.currentAddress = ''"
              ></textarea>
            </div>
            <span v-if="errors.currentAddress" class="error-message">{{ errors.currentAddress }}</span>
            <span v-else class="helper-text">Complete address including barangay and city</span>
          </div>

          <!-- Info Box -->
          <div class="info-box">
            <ion-icon :icon="shieldCheckmarkOutline"></ion-icon>
            <div class="info-text">
              <strong>Your information is secure</strong><br />
              All personal data is encrypted and will only be used for account verification.
            </div>
          </div>

          <!-- Navigation Buttons -->
          <div class="button-group">
            <ion-button expand="block" fill="outline" color="medium" class="back-btn" @click="goBack">
              <ion-icon slot="start" :icon="arrowBackOutline"></ion-icon>
              Back
            </ion-button>

            <ion-button expand="block" class="next-button" @click="handleNext" :disabled="!isFormValid">
              Next
              <ion-icon slot="end" :icon="arrowForwardOutline"></ion-icon>
            </ion-button>
          </div>
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { IonPage, IonContent, IonButton, IonIcon } from '@ionic/vue';
import {
  arrowBackOutline,
  arrowForwardOutline,
  personOutline,
  calendarOutline,
  locationOutline,
  homeOutline,
  shieldCheckmarkOutline,
  globeOutline,
} from 'ionicons/icons';

const router = useRouter();

const countries = ref([
  { code: 'AF', name: 'Afghanistan' },
  { code: 'AL', name: 'Albania' },
  { code: 'DZ', name: 'Algeria' },
  { code: 'AS', name: 'American Samoa' },
  { code: 'AD', name: 'Andorra' },
  { code: 'AO', name: 'Angola' },
  { code: 'AI', name: 'Anguilla' },
  { code: 'AQ', name: 'Antarctica' },
  { code: 'AG', name: 'Antigua and Barbuda' },
  { code: 'AR', name: 'Argentina' },
  { code: 'AM', name: 'Armenia' },
  { code: 'AW', name: 'Aruba' },
  { code: 'AU', name: 'Australia' },
  { code: 'AT', name: 'Austria' },
  { code: 'AZ', name: 'Azerbaijan' },
  { code: 'BS', name: 'Bahamas' },
  { code: 'BH', name: 'Bahrain' },
  { code: 'BD', name: 'Bangladesh' },
  { code: 'BB', name: 'Barbados' },
  { code: 'BY', name: 'Belarus' },
  { code: 'BE', name: 'Belgium' },
  { code: 'BZ', name: 'Belize' },
  { code: 'BJ', name: 'Benin' },
  { code: 'BM', name: 'Bermuda' },
  { code: 'BT', name: 'Bhutan' },
  { code: 'BO', name: 'Bolivia' },
  { code: 'BA', name: 'Bosnia and Herzegovina' },
  { code: 'BW', name: 'Botswana' },
  { code: 'BR', name: 'Brazil' },
  { code: 'IO', name: 'British Indian Ocean Territory' },
  { code: 'VG', name: 'British Virgin Islands' },
  { code: 'BN', name: 'Brunei' },
  { code: 'BG', name: 'Bulgaria' },
  { code: 'BF', name: 'Burkina Faso' },
  { code: 'BI', name: 'Burundi' },
  { code: 'KH', name: 'Cambodia' },
  { code: 'CM', name: 'Cameroon' },
  { code: 'CA', name: 'Canada' },
  { code: 'CV', name: 'Cape Verde' },
  { code: 'KY', name: 'Cayman Islands' },
  { code: 'CF', name: 'Central African Republic' },
  { code: 'TD', name: 'Chad' },
  { code: 'CL', name: 'Chile' },
  { code: 'CN', name: 'China' },
  { code: 'CX', name: 'Christmas Island' },
  { code: 'CC', name: 'Cocos Islands' },
  { code: 'CO', name: 'Colombia' },
  { code: 'KM', name: 'Comoros' },
  { code: 'CK', name: 'Cook Islands' },
  { code: 'CR', name: 'Costa Rica' },
  { code: 'HR', name: 'Croatia' },
  { code: 'CU', name: 'Cuba' },
  { code: 'CW', name: 'Curacao' },
  { code: 'CY', name: 'Cyprus' },
  { code: 'CZ', name: 'Czech Republic' },
  { code: 'CD', name: 'Democratic Republic of the Congo' },
  { code: 'DK', name: 'Denmark' },
  { code: 'DJ', name: 'Djibouti' },
  { code: 'DM', name: 'Dominica' },
  { code: 'DO', name: 'Dominican Republic' },
  { code: 'TL', name: 'East Timor' },
  { code: 'EC', name: 'Ecuador' },
  { code: 'EG', name: 'Egypt' },
  { code: 'SV', name: 'El Salvador' },
  { code: 'GQ', name: 'Equatorial Guinea' },
  { code: 'ER', name: 'Eritrea' },
  { code: 'EE', name: 'Estonia' },
  { code: 'ET', name: 'Ethiopia' },
  { code: 'FK', name: 'Falkland Islands' },
  { code: 'FO', name: 'Faroe Islands' },
  { code: 'FJ', name: 'Fiji' },
  { code: 'FI', name: 'Finland' },
  { code: 'FR', name: 'France' },
  { code: 'PF', name: 'French Polynesia' },
  { code: 'GA', name: 'Gabon' },
  { code: 'GM', name: 'Gambia' },
  { code: 'GE', name: 'Georgia' },
  { code: 'DE', name: 'Germany' },
  { code: 'GH', name: 'Ghana' },
  { code: 'GI', name: 'Gibraltar' },
  { code: 'GR', name: 'Greece' },
  { code: 'GL', name: 'Greenland' },
  { code: 'GD', name: 'Grenada' },
  { code: 'GU', name: 'Guam' },
  { code: 'GT', name: 'Guatemala' },
  { code: 'GG', name: 'Guernsey' },
  { code: 'GN', name: 'Guinea' },
  { code: 'GW', name: 'Guinea-Bissau' },
  { code: 'GY', name: 'Guyana' },
  { code: 'HT', name: 'Haiti' },
  { code: 'HN', name: 'Honduras' },
  { code: 'HK', name: 'Hong Kong' },
  { code: 'HU', name: 'Hungary' },
  { code: 'IS', name: 'Iceland' },
  { code: 'IN', name: 'India' },
  { code: 'ID', name: 'Indonesia' },
  { code: 'IR', name: 'Iran' },
  { code: 'IQ', name: 'Iraq' },
  { code: 'IE', name: 'Ireland' },
  { code: 'IM', name: 'Isle of Man' },
  { code: 'IL', name: 'Israel' },
  { code: 'IT', name: 'Italy' },
  { code: 'CI', name: 'Ivory Coast' },
  { code: 'JM', name: 'Jamaica' },
  { code: 'JP', name: 'Japan' },
  { code: 'JE', name: 'Jersey' },
  { code: 'JO', name: 'Jordan' },
  { code: 'KZ', name: 'Kazakhstan' },
  { code: 'KE', name: 'Kenya' },
  { code: 'KI', name: 'Kiribati' },
  { code: 'XK', name: 'Kosovo' },
  { code: 'KW', name: 'Kuwait' },
  { code: 'KG', name: 'Kyrgyzstan' },
  { code: 'LA', name: 'Laos' },
  { code: 'LV', name: 'Latvia' },
  { code: 'LB', name: 'Lebanon' },
  { code: 'LS', name: 'Lesotho' },
  { code: 'LR', name: 'Liberia' },
  { code: 'LY', name: 'Libya' },
  { code: 'LI', name: 'Liechtenstein' },
  { code: 'LT', name: 'Lithuania' },
  { code: 'LU', name: 'Luxembourg' },
  { code: 'MO', name: 'Macau' },
  { code: 'MK', name: 'Macedonia' },
  { code: 'MG', name: 'Madagascar' },
  { code: 'MW', name: 'Malawi' },
  { code: 'MY', name: 'Malaysia' },
  { code: 'MV', name: 'Maldives' },
  { code: 'ML', name: 'Mali' },
  { code: 'MT', name: 'Malta' },
  { code: 'MH', name: 'Marshall Islands' },
  { code: 'MR', name: 'Mauritania' },
  { code: 'MU', name: 'Mauritius' },
  { code: 'YT', name: 'Mayotte' },
  { code: 'MX', name: 'Mexico' },
  { code: 'FM', name: 'Micronesia' },
  { code: 'MD', name: 'Moldova' },
  { code: 'MC', name: 'Monaco' },
  { code: 'MN', name: 'Mongolia' },
  { code: 'ME', name: 'Montenegro' },
  { code: 'MS', name: 'Montserrat' },
  { code: 'MA', name: 'Morocco' },
  { code: 'MZ', name: 'Mozambique' },
  { code: 'MM', name: 'Myanmar' },
  { code: 'NA', name: 'Namibia' },
  { code: 'NR', name: 'Nauru' },
  { code: 'NP', name: 'Nepal' },
  { code: 'NL', name: 'Netherlands' },
  { code: 'NC', name: 'New Caledonia' },
  { code: 'NZ', name: 'New Zealand' },
  { code: 'NI', name: 'Nicaragua' },
  { code: 'NE', name: 'Niger' },
  { code: 'NG', name: 'Nigeria' },
  { code: 'NU', name: 'Niue' },
  { code: 'KP', name: 'North Korea' },
  { code: 'MP', name: 'Northern Mariana Islands' },
  { code: 'NO', name: 'Norway' },
  { code: 'OM', name: 'Oman' },
  { code: 'PK', name: 'Pakistan' },
  { code: 'PW', name: 'Palau' },
  { code: 'PS', name: 'Palestine' },
  { code: 'PA', name: 'Panama' },
  { code: 'PG', name: 'Papua New Guinea' },
  { code: 'PY', name: 'Paraguay' },
  { code: 'PE', name: 'Peru' },
  { code: 'PH', name: 'Philippines' },
  { code: 'PN', name: 'Pitcairn' },
  { code: 'PL', name: 'Poland' },
  { code: 'PT', name: 'Portugal' },
  { code: 'PR', name: 'Puerto Rico' },
  { code: 'QA', name: 'Qatar' },
  { code: 'CG', name: 'Republic of the Congo' },
  { code: 'RE', name: 'Reunion' },
  { code: 'RO', name: 'Romania' },
  { code: 'RU', name: 'Russia' },
  { code: 'RW', name: 'Rwanda' },
  { code: 'BL', name: 'Saint Barthelemy' },
  { code: 'SH', name: 'Saint Helena' },
  { code: 'KN', name: 'Saint Kitts and Nevis' },
  { code: 'LC', name: 'Saint Lucia' },
  { code: 'MF', name: 'Saint Martin' },
  { code: 'PM', name: 'Saint Pierre and Miquelon' },
  { code: 'VC', name: 'Saint Vincent and the Grenadines' },
  { code: 'WS', name: 'Samoa' },
  { code: 'SM', name: 'San Marino' },
  { code: 'ST', name: 'Sao Tome and Principe' },
  { code: 'SA', name: 'Saudi Arabia' },
  { code: 'SN', name: 'Senegal' },
  { code: 'RS', name: 'Serbia' },
  { code: 'SC', name: 'Seychelles' },
  { code: 'SL', name: 'Sierra Leone' },
  { code: 'SG', name: 'Singapore' },
  { code: 'SX', name: 'Sint Maarten' },
  { code: 'SK', name: 'Slovakia' },
  { code: 'SI', name: 'Slovenia' },
  { code: 'SB', name: 'Solomon Islands' },
  { code: 'SO', name: 'Somalia' },
  { code: 'ZA', name: 'South Africa' },
  { code: 'KR', name: 'South Korea' },
  { code: 'SS', name: 'South Sudan' },
  { code: 'ES', name: 'Spain' },
  { code: 'LK', name: 'Sri Lanka' },
  { code: 'SD', name: 'Sudan' },
  { code: 'SR', name: 'Suriname' },
  { code: 'SJ', name: 'Svalbard and Jan Mayen' },
  { code: 'SZ', name: 'Swaziland' },
  { code: 'SE', name: 'Sweden' },
  { code: 'CH', name: 'Switzerland' },
  { code: 'SY', name: 'Syria' },
  { code: 'TW', name: 'Taiwan' },
  { code: 'TJ', name: 'Tajikistan' },
  { code: 'TZ', name: 'Tanzania' },
  { code: 'TH', name: 'Thailand' },
  { code: 'TG', name: 'Togo' },
  { code: 'TK', name: 'Tokelau' },
  { code: 'TO', name: 'Tonga' },
  { code: 'TT', name: 'Trinidad and Tobago' },
  { code: 'TN', name: 'Tunisia' },
  { code: 'TR', name: 'Turkey' },
  { code: 'TM', name: 'Turkmenistan' },
  { code: 'TC', name: 'Turks and Caicos Islands' },
  { code: 'TV', name: 'Tuvalu' },
  { code: 'VI', name: 'U.S. Virgin Islands' },
  { code: 'UG', name: 'Uganda' },
  { code: 'UA', name: 'Ukraine' },
  { code: 'AE', name: 'United Arab Emirates' },
  { code: 'GB', name: 'United Kingdom' },
  { code: 'US', name: 'United States' },
  { code: 'UY', name: 'Uruguay' },
  { code: 'UZ', name: 'Uzbekistan' },
  { code: 'VU', name: 'Vanuatu' },
  { code: 'VA', name: 'Vatican' },
  { code: 'VE', name: 'Venezuela' },
  { code: 'VN', name: 'Vietnam' },
  { code: 'WF', name: 'Wallis and Futuna' },
  { code: 'EH', name: 'Western Sahara' },
  { code: 'YE', name: 'Yemen' },
  { code: 'ZM', name: 'Zambia' },
  { code: 'ZW', name: 'Zimbabwe' },
]);

const formData = ref({
  firstName: '',
  middleName: '',
  lastName: '',
  country: '',
  birthdate: '',
  birthplace: '',
  currentAddress: '',
});

const sameAsBirthplace = ref(false);

const errors = ref({
  firstName: '',
  lastName: '',
  country: '',
  birthdate: '',
  birthplace: '',
  currentAddress: '',
});

const maxDate = computed(() => {
  const today = new Date();
  const year = today.getFullYear() - 18;
  const month = String(today.getMonth() + 1).padStart(2, '0');
  const day = String(today.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
});

onMounted(() => {
  const savedData = localStorage.getItem('driverRegistrationData');
  if (savedData) {
    const data = JSON.parse(savedData);
    if (data.firstName) formData.value.firstName = data.firstName;
    if (data.middleName) formData.value.middleName = data.middleName;
    if (data.lastName) formData.value.lastName = data.lastName;
    if (data.country) formData.value.country = data.country;
    if (data.birthdate) formData.value.birthdate = data.birthdate;
    if (data.birthplace) formData.value.birthplace = data.birthplace;
    if (data.currentAddress) formData.value.currentAddress = data.currentAddress;
    if (data.sameAsBirthplace) sameAsBirthplace.value = data.sameAsBirthplace;
  }
});

const validateField = (fieldName) => {
  switch (fieldName) {
    case 'firstName':
      if (!formData.value.firstName.trim()) {
        errors.value.firstName = 'First name is required';
        return false;
      }
      if (formData.value.firstName.trim().length < 2) {
        errors.value.firstName = 'First name must be at least 2 characters';
        return false;
      }
      errors.value.firstName = '';
      return true;

    case 'lastName':
      if (!formData.value.lastName.trim()) {
        errors.value.lastName = 'Last name is required';
        return false;
      }
      if (formData.value.lastName.trim().length < 2) {
        errors.value.lastName = 'Last name must be at least 2 characters';
        return false;
      }
      errors.value.lastName = '';
      return true;

    case 'country':
      if (!formData.value.country) {
        errors.value.country = 'Country is required';
        return false;
      }
      errors.value.country = '';
      return true;

    case 'birthdate':
      if (!formData.value.birthdate) {
        errors.value.birthdate = 'Birthdate is required';
        return false;
      }

      const birthDate = new Date(formData.value.birthdate);
      const today = new Date();
      let age = today.getFullYear() - birthDate.getFullYear();
      const monthDiff = today.getMonth() - birthDate.getMonth();

      if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        age--;
      }

      if (age < 18) {
        errors.value.birthdate = 'You must be at least 18 years old';
        return false;
      }

      errors.value.birthdate = '';
      return true;

    case 'birthplace':
      if (!formData.value.birthplace.trim()) {
        errors.value.birthplace = 'Birthplace is required';
        return false;
      }
      if (formData.value.birthplace.trim().length < 3) {
        errors.value.birthplace = 'Please enter a valid birthplace';
        return false;
      }
      errors.value.birthplace = '';
      return true;

    case 'currentAddress':
      if (!formData.value.currentAddress.trim()) {
        errors.value.currentAddress = 'Current address is required';
        return false;
      }
      if (formData.value.currentAddress.trim().length < 10) {
        errors.value.currentAddress = 'Please provide a complete address';
        return false;
      }
      errors.value.currentAddress = '';
      return true;

    default:
      return true;
  }
};

const onSameAsBirthplaceChange = () => {
  if (sameAsBirthplace.value) {
    formData.value.currentAddress = formData.value.birthplace;
    errors.value.currentAddress = '';
  } else {
    formData.value.currentAddress = '';
  }
};

const isFormValid = computed(() => {
  return (
    formData.value.firstName.trim() &&
    formData.value.lastName.trim() &&
    formData.value.country &&
    formData.value.birthdate &&
    formData.value.birthplace.trim() &&
    formData.value.currentAddress.trim()
  );
});

const goBack = () => {
  saveData();
  router.push('/register/step1');
};

const saveData = () => {
  const existingData = JSON.parse(localStorage.getItem('driverRegistrationData') || '{}');
  const updatedData = {
    ...existingData,
    firstName: formData.value.firstName,
    middleName: formData.value.middleName,
    lastName: formData.value.lastName,
    country: formData.value.country,
    birthdate: formData.value.birthdate,
    birthplace: formData.value.birthplace,
    currentAddress: formData.value.currentAddress,
    sameAsBirthplace: sameAsBirthplace.value,
  };
  localStorage.setItem('driverRegistrationData', JSON.stringify(updatedData));
};

const handleNext = () => {
  const isFirstNameValid = validateField('firstName');
  const isLastNameValid = validateField('lastName');
  const isCountryValid = validateField('country');
  const isBirthdateValid = validateField('birthdate');
  const isBirthplaceValid = validateField('birthplace');
  const isAddressValid = validateField('currentAddress');

  if (
    isFirstNameValid &&
    isLastNameValid &&
    isCountryValid &&
    isBirthdateValid &&
    isBirthplaceValid &&
    isAddressValid
  ) {
    saveData();
    router.push('/register/step3');
  }
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

/* Section Header */
.section-header {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 24px;
  padding-bottom: 12px;
  border-bottom: 2px solid var(--color-card-border);
}

.section-header ion-icon {
  font-size: 24px;
  color: var(--color-icon-primary);
}

.section-header h2 {
  font-size: 18px;
  font-weight: 700;
  color: var(--color-text-primary);
  margin: 0;
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

.optional {
  font-weight: 400;
  color: var(--color-text-tertiary);
  font-size: 13px;
}

.input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.input-icon {
  position: absolute;
  left: 16px;
  top: 16px;
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
}

.custom-input:focus {
  outline: none;
  border-color: var(--color-border-focus);
}

.custom-input::placeholder {
  color: var(--color-input-placeholder);
}

.custom-select {
  appearance: none;
  -webkit-appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 16px center;
  padding-right: 40px;
}

.custom-select:invalid {
  color: var(--color-input-placeholder);
}

/* Error State */
.input-wrapper.error .custom-input {
  border-color: var(--color-error);
}

.input-wrapper.error .input-icon {
  color: var(--color-error);
}

/* Helper/Error Text */
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

/* Info Box */
.info-box {
  display: flex;
  gap: 12px;
  background: var(--color-success-bg);
  border-left: 4px solid var(--color-success-border);
  padding: 16px;
  border-radius: 8px;
  margin: 32px 0 24px 0;
}

.info-box ion-icon {
  font-size: 24px;
  color: var(--color-success-border);
  flex-shrink: 0;
  margin-top: 2px;
}

.info-text {
  font-size: 13px;
  color: var(--color-success-text);
  line-height: 1.5;
}

.info-text strong {
  font-weight: 700;
}

/* Textarea */
.custom-textarea {
  width: 100%;
  padding: 14px 16px;
  border: 2px solid var(--color-border);
  border-radius: 12px;
  font-size: 15px;
  background: var(--color-input-bg);
  color: var(--color-text-primary);
  transition: border-color 0.2s;
  font-family: inherit;
  resize: vertical;
  min-height: 80px;
}

.custom-textarea.with-icon {
  padding-left: 48px;
}

.custom-textarea:focus {
  outline: none;
  border-color: var(--color-border-focus);
}

.custom-textarea::placeholder {
  color: var(--color-input-placeholder);
}

.custom-textarea:disabled {
  background: var(--color-input-disabled-bg);
  color: var(--color-text-tertiary);
  cursor: not-allowed;
}

/* Checkbox Group */
.checkbox-group {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 20px;
  padding: 16px;
  background: var(--color-bg-secondary);
  border-radius: 12px;
}

.checkbox-input {
  width: 20px;
  height: 20px;
  cursor: pointer;
  accent-color: var(--color-primary);
  flex-shrink: 0;
}

.checkbox-group label {
  font-size: 14px;
  color: var(--color-text-primary);
  font-weight: 500;
  cursor: pointer;
  flex: 1;
  line-height: 1.5;
}

/* Button Group */
.button-group {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
  margin-top: 32px;
}

.back-btn {
  --border-radius: 12px;
  --border-width: 2px;
  height: 52px;
  font-size: 15px;
  font-weight: 600;
  text-transform: none;
}

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

  .button-group {
    grid-template-columns: 1fr;
  }

  .back-btn {
    order: 2;
  }

  .next-button {
    order: 1;
  }
}
</style>
