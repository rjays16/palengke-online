import { reactive, watch } from 'vue';

const STORAGE_KEY = 'driverRegistrationData';

export interface RegistrationData {
  email: string;
  countryCode: string;
  phone: string;
  fullPhone: string;
  emailVerified: boolean;
  firstName: string;
  middleName: string;
  lastName: string;
  country: string;
  birthdate: string;
  birthplace: string;
  currentAddress: string;
  sameAsBirthplace: boolean;
  licenseNumber: string;
  licenseType: string;
  restrictionCode: string;
  expiryDate: string;
  vehicleType: string;
  plateNumber: string;
  vehicleModel: string;
  vehicleColor: string;
  license_front_photo: string;
  vehicle_reg_photo: string;
  nbi_clearance_photo: string;
  birth_cert_photo: string;
  selfiePhoto: string;
}

function defaults(): RegistrationData {
  return {
    email: '',
    countryCode: '',
    phone: '',
    fullPhone: '',
    emailVerified: false,
    firstName: '',
    middleName: '',
    lastName: '',
    country: '',
    birthdate: '',
    birthplace: '',
    currentAddress: '',
    sameAsBirthplace: false,
    licenseNumber: '',
    licenseType: '',
    restrictionCode: '',
    expiryDate: '',
    vehicleType: '',
    plateNumber: '',
    vehicleModel: '',
    vehicleColor: '',
    license_front_photo: '',
    vehicle_reg_photo: '',
    nbi_clearance_photo: '',
    birth_cert_photo: '',
    selfiePhoto: '',
  };
}

function load(): RegistrationData {
  try {
    const raw = localStorage.getItem(STORAGE_KEY);
    if (raw) return { ...defaults(), ...JSON.parse(raw) };
  } catch {
    /* ignore */
  }
  return defaults();
}

const state = reactive<RegistrationData>(load());

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
    country: string;
    birthdate: string;
    birthplace: string;
    currentAddress: string;
    sameAsBirthplace: boolean;
  }) {
    Object.assign(state, data);
  }

  function setLicenseInfo(data: {
    licenseNumber: string;
    licenseType: string;
    restrictionCode: string;
    expiryDate: string;
  }) {
    Object.assign(state, data);
  }

  function setVehicleInfo(data: {
    vehicleType: string;
    plateNumber: string;
    vehicleModel: string;
    vehicleColor: string;
  }) {
    Object.assign(state, data);
  }

  function setPhoto(
    key: 'license_front_photo' | 'vehicle_reg_photo' | 'nbi_clearance_photo' | 'birth_cert_photo',
    url: string,
  ) {
    state[key] = url;
  }

  function setSelfiePhoto(url: string) {
    state.selfiePhoto = url;
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
      country: state.country,
      birthdate: state.birthdate,
      birthplace: state.birthplace,
      current_address: state.currentAddress,
      license_number: state.licenseNumber,
      license_type: state.licenseType,
      restriction_code: state.restrictionCode,
      expiry_date: state.expiryDate,
      vehicle_type: state.vehicleType,
      plate_number: state.plateNumber,
      vehicle_model: state.vehicleModel,
      vehicle_color: state.vehicleColor || '',
      license_front_photo: state.license_front_photo || '',
      vehicle_reg_photo: state.vehicle_reg_photo || '',
      nbi_clearance_photo: state.nbi_clearance_photo || '',
      birth_cert_photo: state.birth_cert_photo || '',
      selfie_photo: state.selfiePhoto || '',
    };
  }

  function onSubmitted() {
    localStorage.setItem('submittedRegistrationData', JSON.stringify({ ...state }));
    reset();
  }

  function reset() {
    Object.assign(state, defaults());
    localStorage.removeItem(STORAGE_KEY);
  }

  function getRestrictionCode(): string {
    return state.restrictionCode;
  }

  function getEmail(): string {
    return state.email;
  }

  function getSubmittedData(): RegistrationData | null {
    try {
      const raw = localStorage.getItem('submittedRegistrationData');
      if (raw) return JSON.parse(raw);
    } catch {
      /* ignore */
    }
    return null;
  }

  function clearSubmittedData() {
    localStorage.removeItem('submittedRegistrationData');
  }

  return {
    state,
    setContactInfo,
    setPersonalInfo,
    setLicenseInfo,
    setVehicleInfo,
    setPhoto,
    setSelfiePhoto,
    getApiPayload,
    onSubmitted,
    reset,
    getRestrictionCode,
    getEmail,
    getSubmittedData,
    clearSubmittedData,
  };
}
