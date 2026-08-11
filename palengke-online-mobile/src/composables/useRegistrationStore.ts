import { reactive, watch } from 'vue';

const STORAGE_KEY = 'buyerRegistrationData';

export interface CustomerRegistrationData {
  email: string;
  countryCode: string;
  phone: string;
  fullPhone: string;
  emailVerified: boolean;
  firstName: string;
  middleName: string;
  lastName: string;
  noMiddleName: boolean;
  birthdate: string;
  region: string;
  city: string;
  barangay: string;
  street: string;
  landmark: string;
  addressLabel: string;
  acceptMarketing: boolean;
  termsAccepted: boolean;
}

function defaults(): CustomerRegistrationData {
  return {
    email: '',
    countryCode: '',
    phone: '',
    fullPhone: '',
    emailVerified: false,
    firstName: '',
    middleName: '',
    lastName: '',
    noMiddleName: false,
    birthdate: '',
    region: '',
    city: '',
    barangay: '',
    street: '',
    landmark: '',
    addressLabel: 'Home',
    acceptMarketing: false,
    termsAccepted: false,
  };
}

function load(): CustomerRegistrationData {
  try {
    const raw = localStorage.getItem(STORAGE_KEY);
    if (raw) return { ...defaults(), ...JSON.parse(raw) };
  } catch {
    /* ignore */
  }
  return defaults();
}

const state = reactive<CustomerRegistrationData>(load());

let timer: ReturnType<typeof setTimeout> | null = null;
function save() {
  if (timer) clearTimeout(timer);
  timer = setTimeout(() => {
    localStorage.setItem(STORAGE_KEY, JSON.stringify({ ...state }));
  }, 300);
}

watch(state, () => save(), { deep: true });

export function useRegistrationStore() {
  function setContactInfo(data: {
    email: string;
    countryCode: string;
    phone: string;
    fullPhone: string;
    emailVerified: boolean;
  }) {
    Object.assign(state, data);
  }

  function setPersonalInfo(data: {
    firstName: string;
    middleName: string;
    lastName: string;
    noMiddleName: boolean;
    birthdate: string;
  }) {
    Object.assign(state, data);
  }

  function setAddressInfo(data: {
    region: string;
    city: string;
    barangay: string;
    street: string;
    landmark: string;
    addressLabel: string;
  }) {
    Object.assign(state, data);
  }

  function setTerms(data: {
    acceptMarketing: boolean;
    termsAccepted: boolean;
  }) {
    Object.assign(state, data);
  }

  function getApiPayload(): Record<string, unknown> {
    return {
      email: state.email,
      country_code: state.countryCode,
      phone: state.phone,
      full_phone: state.fullPhone,
      email_verified: state.emailVerified,
      first_name: state.firstName,
      middle_name: state.middleName || '',
      last_name: state.lastName,
      birthdate: state.birthdate,
      region: state.region,
      city: state.city,
      barangay: state.barangay,
      street: state.street,
      landmark: state.landmark || '',
      address_label: state.addressLabel || '',
      accept_marketing: state.acceptMarketing,
      terms_accepted: state.termsAccepted,
    };
  }

  function getEmail(): string {
    return state.email;
  }

  function getFullPhone(): string {
    return state.fullPhone;
  }

  function onSubmitted() {
    localStorage.removeItem(STORAGE_KEY);
    reset();
  }

  function reset() {
    Object.assign(state, defaults());
    localStorage.removeItem(STORAGE_KEY);
  }

  return {
    state,
    setContactInfo,
    setPersonalInfo,
    setAddressInfo,
    setTerms,
    getApiPayload,
    getEmail,
    getFullPhone,
    onSubmitted,
    reset,
  };
}
