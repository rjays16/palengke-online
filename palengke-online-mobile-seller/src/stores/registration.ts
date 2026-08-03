import { reactive } from 'vue'
import { defineStore } from 'pinia'

interface RegistrationData {
  email: string
  phone: string
  country: string
  countryCode: string
  fullPhone: string
  emailVerified: boolean
  firstName: string
  middleName: string
  lastName: string
  birthdate: string
  birthplace: string
  currentAddress: string
  sameAsBirthplace: boolean
  region: string
  city: string
  barangay: string
  storeName: string
  storePlace: string
  completeAddress: string
  latitude: string
  longitude: string
  verificationChoice: string
  verificationStatus: string
  verificationSubmittedAt: string
  idType: string
  idTypeName: string
  idFront: string
  idBack: string
  idFrontPhoto: string
  idBackPhoto: string
  selfiePhoto: string
  selfieWithId: string
  acceptedTOS: boolean
  acceptedDataPrivacy: boolean
}

const STORAGE_KEY = 'registrationData'

function defaults(): RegistrationData {
  return {
    email: '',
    phone: '',
    country: '',
    countryCode: '',
    fullPhone: '',
    emailVerified: false,
    firstName: '',
    middleName: '',
    lastName: '',
    birthdate: '',
    birthplace: '',
    currentAddress: '',
    sameAsBirthplace: false,
    region: '',
    city: '',
    barangay: '',
    storeName: '',
    storePlace: '',
    completeAddress: '',
    latitude: '',
    longitude: '',
    verificationChoice: '',
    verificationStatus: '',
    verificationSubmittedAt: '',
    idType: '',
    idTypeName: '',
    idFront: '',
    idBack: '',
    idFrontPhoto: '',
    idBackPhoto: '',
    selfiePhoto: '',
    selfieWithId: '',
    acceptedTOS: false,
    acceptedDataPrivacy: false,
  }
}

function loadFromStorage(): RegistrationData {
  try {
    const raw = localStorage.getItem(STORAGE_KEY)
    if (raw) return { ...defaults(), ...JSON.parse(raw) }
  } catch { /* ignore */ }
  return defaults()
}

function saveToStorage(data: RegistrationData) {
  localStorage.setItem(STORAGE_KEY, JSON.stringify(data))
}

export const useRegistrationStore = defineStore('registration', () => {
  const data = reactive<RegistrationData>(loadFromStorage())

  function load() {
    Object.assign(data, loadFromStorage())
  }

  function save() {
    saveToStorage(data)
  }

  function setField<K extends keyof RegistrationData>(key: K, value: RegistrationData[K]) {
    data[key] = value
    saveToStorage(data)
  }

  function setFields(partial: Partial<RegistrationData>) {
    Object.assign(data, partial)
    saveToStorage(data)
  }

  function getField<K extends keyof RegistrationData>(key: K): RegistrationData[K] {
    return data[key]
  }

  function clearAll() {
    const cleared = defaults()
    Object.assign(data, cleared)
    localStorage.removeItem(STORAGE_KEY)
  }

  function getAll(): RegistrationData {
    return { ...data }
  }

  return {
    data,
    load,
    save,
    setField,
    setFields,
    getField,
    clearAll,
    getAll,
  }
})
