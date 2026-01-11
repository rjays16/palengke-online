<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="success">
        <ion-title>Profile</ion-title>
        <ion-buttons slot="end">
          <ion-button @click="editProfile">
            <ion-icon :icon="createOutline"></ion-icon>
          </ion-button>
        </ion-buttons>
      </ion-toolbar>
    </ion-header>

    <ion-content :fullscreen="true">
      <div class="profile-container">

        <!-- Profile Header -->
        <div class="profile-header">
          <div class="profile-avatar-container">
            <div class="profile-avatar">
              <img v-if="profileData.avatar" :src="profileData.avatar" alt="Profile">
              <ion-icon v-else :icon="personOutline"></ion-icon>
            </div>
            <ion-button class="change-photo-btn" fill="clear" size="small" @click="changePhoto">
              <ion-icon :icon="cameraOutline"></ion-icon>
            </ion-button>
          </div>
          
          <h2 class="profile-name">{{ profileData.name }}</h2>
          <p class="profile-type">{{ profileData.businessType }}</p>
          
          <div class="profile-stats">
            <div class="stat-item">
              <div class="stat-value">{{ profileData.totalOrders }}</div>
              <div class="stat-label">Orders</div>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-item">
              <div class="stat-value">{{ profileData.rating }}</div>
              <div class="stat-label">Rating</div>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-item">
              <div class="stat-value">{{ profileData.joinedDays }}</div>
              <div class="stat-label">Days Active</div>
            </div>
          </div>
        </div>

        <!-- Personal Information -->
        <div class="section">
          <h3 class="section-title">Personal Information</h3>
          
          <div class="info-list">
            <div class="info-item" @click="editField('name')">
              <div class="info-icon">
                <ion-icon :icon="personOutline"></ion-icon>
              </div>
              <div class="info-content">
                <p class="info-label">Full Name</p>
                <p class="info-value">{{ profileData.name }}</p>
              </div>
              <ion-icon :icon="chevronForwardOutline" class="info-arrow"></ion-icon>
            </div>

            <div class="info-item" @click="editField('phone')">
              <div class="info-icon">
                <ion-icon :icon="callOutline"></ion-icon>
              </div>
              <div class="info-content">
                <p class="info-label">Phone Number</p>
                <p class="info-value">{{ profileData.phone }}</p>
              </div>
              <ion-icon :icon="chevronForwardOutline" class="info-arrow"></ion-icon>
            </div>

            <div class="info-item" @click="editField('email')">
              <div class="info-icon">
                <ion-icon :icon="mailOutline"></ion-icon>
              </div>
              <div class="info-content">
                <p class="info-label">Email Address</p>
                <p class="info-value">{{ profileData.email }}</p>
              </div>
              <ion-icon :icon="chevronForwardOutline" class="info-arrow"></ion-icon>
            </div>

            <div class="info-item" @click="editField('address')">
              <div class="info-icon">
                <ion-icon :icon="locationOutline"></ion-icon>
              </div>
              <div class="info-content">
                <p class="info-label">Address</p>
                <p class="info-value">{{ profileData.address }}</p>
              </div>
              <ion-icon :icon="chevronForwardOutline" class="info-arrow"></ion-icon>
            </div>
          </div>
        </div>

        <!-- Shop Information -->
        <div class="section">
          <h3 class="section-title">Shop Information</h3>
          
          <div class="info-list">
            <div class="info-item" @click="editField('shopName')">
              <div class="info-icon">
                <ion-icon :icon="storefrontOutline"></ion-icon>
              </div>
              <div class="info-content">
                <p class="info-label">Shop Name</p>
                <p class="info-value">{{ profileData.shopName }}</p>
              </div>
              <ion-icon :icon="chevronForwardOutline" class="info-arrow"></ion-icon>
            </div>

            <div class="info-item" @click="editField('shopAddress')">
              <div class="info-icon">
                <ion-icon :icon="mapOutline"></ion-icon>
              </div>
              <div class="info-content">
                <p class="info-label">Shop Location</p>
                <p class="info-value">{{ profileData.shopAddress }}</p>
              </div>
              <ion-icon :icon="chevronForwardOutline" class="info-arrow"></ion-icon>
            </div>

            <div class="info-item" @click="editField('shopHours')">
              <div class="info-icon">
                <ion-icon :icon="timeOutline"></ion-icon>
              </div>
              <div class="info-content">
                <p class="info-label">Operating Hours</p>
                <p class="info-value">{{ profileData.shopHours }}</p>
              </div>
              <ion-icon :icon="chevronForwardOutline" class="info-arrow"></ion-icon>
            </div>

            <div class="info-item" @click="editField('description')">
              <div class="info-icon">
                <ion-icon :icon="documentTextOutline"></ion-icon>
              </div>
              <div class="info-content">
                <p class="info-label">Shop Description</p>
                <p class="info-value">{{ profileData.description }}</p>
              </div>
              <ion-icon :icon="chevronForwardOutline" class="info-arrow"></ion-icon>
            </div>
          </div>
        </div>

        <!-- Categories -->
        <div class="section">
          <h3 class="section-title">Product Categories</h3>
          
          <div class="categories-display">
            <div class="category-chip" v-for="category in profileData.categories" :key="category">
              {{ category }}
            </div>
            <div class="category-chip add" @click="manageCategories">
              <ion-icon :icon="addOutline"></ion-icon>
              Edit
            </div>
          </div>
        </div>

        <!-- Payment Information -->
        <div class="section">
          <h3 class="section-title">Payment Information</h3>
          
          <div class="info-list">
            <div class="info-item" @click="editField('gcash')">
              <div class="info-icon payment">
                <ion-icon :icon="walletOutline"></ion-icon>
              </div>
              <div class="info-content">
                <p class="info-label">GCash Number</p>
                <p class="info-value">{{ profileData.gcashNumber || 'Not set' }}</p>
              </div>
              <ion-icon :icon="chevronForwardOutline" class="info-arrow"></ion-icon>
            </div>

            <div class="info-item" @click="editField('bank')">
              <div class="info-icon payment">
                <ion-icon :icon="cardOutline"></ion-icon>
              </div>
              <div class="info-content">
                <p class="info-label">Bank Account</p>
                <p class="info-value">{{ profileData.bankAccount || 'Not set' }}</p>
              </div>
              <ion-icon :icon="chevronForwardOutline" class="info-arrow"></ion-icon>
            </div>
          </div>
        </div>

        <!-- Account Settings -->
        <div class="section">
          <h3 class="section-title">Account Settings</h3>
          
          <div class="settings-list">
            <div class="settings-item" @click="changePassword">
              <div class="settings-icon">
                <ion-icon :icon="lockClosedOutline"></ion-icon>
              </div>
              <div class="settings-content">
                <p class="settings-label">Change Password</p>
              </div>
              <ion-icon :icon="chevronForwardOutline" class="settings-arrow"></ion-icon>
            </div>

            <div class="settings-item" @click="notificationSettings">
              <div class="settings-icon">
                <ion-icon :icon="notificationsOutline"></ion-icon>
              </div>
              <div class="settings-content">
                <p class="settings-label">Notification Settings</p>
              </div>
              <ion-icon :icon="chevronForwardOutline" class="settings-arrow"></ion-icon>
            </div>

            <div class="settings-item">
              <div class="settings-icon">
                <ion-icon :icon="moonOutline"></ion-icon>
              </div>
              <div class="settings-content">
                <p class="settings-label">Dark Mode</p>
              </div>
              <ion-toggle v-model="darkMode" @ionChange="toggleDarkMode"></ion-toggle>
            </div>

            <div class="settings-item">
              <div class="settings-icon">
                <ion-icon :icon="languageOutline"></ion-icon>
              </div>
              <div class="settings-content">
                <p class="settings-label">Language</p>
                <p class="settings-value">English</p>
              </div>
              <ion-icon :icon="chevronForwardOutline" class="settings-arrow"></ion-icon>
            </div>
          </div>
        </div>

        <!-- Support & Legal -->
        <div class="section">
          <h3 class="section-title">Support & Legal</h3>
          
          <div class="settings-list">
            <div class="settings-item" @click="helpCenter">
              <div class="settings-icon">
                <ion-icon :icon="helpCircleOutline"></ion-icon>
              </div>
              <div class="settings-content">
                <p class="settings-label">Help Center</p>
              </div>
              <ion-icon :icon="chevronForwardOutline" class="settings-arrow"></ion-icon>
            </div>

            <div class="settings-item" @click="contactSupport">
              <div class="settings-icon">
                <ion-icon :icon="chatbubbleEllipsesOutline"></ion-icon>
              </div>
              <div class="settings-content">
                <p class="settings-label">Contact Support</p>
              </div>
              <ion-icon :icon="chevronForwardOutline" class="settings-arrow"></ion-icon>
            </div>

            <div class="settings-item" @click="showTerms">
              <div class="settings-icon">
                <ion-icon :icon="documentTextOutline"></ion-icon>
              </div>
              <div class="settings-content">
                <p class="settings-label">Terms & Conditions</p>
              </div>
              <ion-icon :icon="chevronForwardOutline" class="settings-arrow"></ion-icon>
            </div>

            <div class="settings-item" @click="showPrivacy">
              <div class="settings-icon">
                <ion-icon :icon="shieldCheckmarkOutline"></ion-icon>
              </div>
              <div class="settings-content">
                <p class="settings-label">Privacy Policy</p>
              </div>
              <ion-icon :icon="chevronForwardOutline" class="settings-arrow"></ion-icon>
            </div>

            <div class="settings-item" @click="aboutApp">
              <div class="settings-icon">
                <ion-icon :icon="informationCircleOutline"></ion-icon>
              </div>
              <div class="settings-content">
                <p class="settings-label">About</p>
                <p class="settings-value">Version 1.0.0</p>
              </div>
              <ion-icon :icon="chevronForwardOutline" class="settings-arrow"></ion-icon>
            </div>
          </div>
        </div>

        <!-- Logout Button -->
        <div class="logout-section">
          <ion-button 
            expand="block" 
            color="danger" 
            @click="handleLogout"
            class="logout-button"
          >
            <ion-icon :icon="logOutOutline" slot="start"></ion-icon>
            Logout
          </ion-button>
        </div>

        <!-- App Version -->
        <div class="app-version">
          <p>Palengke Online Seller</p>
          <p>Version 1.0.0 (Build 100)</p>
        </div>

      </div>
    </ion-content>

    <!-- Bottom Navigation -->
    <ion-footer>
      <ion-toolbar>
        <div class="bottom-nav">
          <div class="nav-item" @click="goToHome">
            <ion-icon :icon="homeOutline"></ion-icon>
            <span>Home</span>
          </div>
          <div class="nav-item" @click="goToProducts">
            <ion-icon :icon="cubeOutline"></ion-icon>
            <span>Products</span>
          </div>
          <div class="nav-item" @click="goToOrders">
            <ion-icon :icon="receiptOutline"></ion-icon>
            <span>Orders</span>
          </div>
          <div class="nav-item active" @click="goToProfile">
            <ion-icon :icon="personOutline"></ion-icon>
            <span>Profile</span>
          </div>
        </div>
      </ion-toolbar>
    </ion-footer>
  </ion-page>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import {
  IonPage,
  IonHeader,
  IonToolbar,
  IonTitle,
  IonContent,
  IonFooter,
  IonButtons,
  IonButton,
  IonBackButton,
  IonIcon,
  IonToggle,
  alertController,
  actionSheetController,
  toastController
} from '@ionic/vue';
import {
  personOutline,
  createOutline,
  cameraOutline,
  callOutline,
  mailOutline,
  locationOutline,
  storefrontOutline,
  mapOutline,
  timeOutline,
  documentTextOutline,
  addOutline,
  walletOutline,
  cardOutline,
  lockClosedOutline,
  notificationsOutline,
  moonOutline,
  languageOutline,
  helpCircleOutline,
  chatbubbleEllipsesOutline,
  shieldCheckmarkOutline,
  informationCircleOutline,
  logOutOutline,
  chevronForwardOutline,
  imagesOutline,
  trashOutline,
  closeOutline,
  homeOutline,
  cubeOutline,
  receiptOutline
} from 'ionicons/icons';

const router = useRouter();

// Profile Data
const profileData = ref({
  name: 'Juan Dela Cruz',
  phone: '+63 912 345 6789',
  email: 'juan.delacruz@email.com',
  address: 'Brgy. San Isidro, Cagayan de Oro City',
  businessType: 'Fresh Produce Seller',
  shopName: 'Juan\'s Fresh Market',
  shopAddress: 'Stall #15, CDO City Palengke',
  shopHours: 'Mon-Sun, 5:00 AM - 6:00 PM',
  description: 'Fresh vegetables, fruits, and seafood daily. Quality products at affordable prices.',
  categories: ['Fish', 'Vegetables', 'Fruits', 'Seafood'],
  gcashNumber: '0912 345 6789',
  bankAccount: 'BDO - 1234 5678 9012',
  avatar: null,
  totalOrders: 342,
  rating: 4.8,
  joinedDays: 45
});

const darkMode = ref(false);

// ✨ NEW: Loading Modal Function
const showLoadingModal = async (message = 'Loading...') => {
  const alert = await alertController.create({
    cssClass: 'loading-modal-simple',
    message: message,
    buttons: []
  });
  
  await alert.present();
  
  // Auto dismiss after 1.5 seconds
  setTimeout(() => {
    alert.dismiss();
  }, 1500);
};

// Success Modal Function
const showSuccessModal = async (message) => {
  const alert = await alertController.create({
    cssClass: 'success-modal',
    header: ' ',
    message: message,
    buttons: ['OK']
  });
  
  await alert.present();
  
  // Add the image after modal is presented
  setTimeout(() => {
    const alertElement = document.querySelector('.success-modal .alert-message');
    if (alertElement) {
      // Clear the text content
      const textContent = alertElement.textContent;
      alertElement.innerHTML = '';
      
      // Create wrapper for centering
      const wrapper = document.createElement('div');
      wrapper.style.display = 'flex';
      wrapper.style.flexDirection = 'column';
      wrapper.style.alignItems = 'center';
      wrapper.style.textAlign = 'center';
      
      // Create and add the image
      const img = document.createElement('img');
      img.src = '/assets/images/checkmark_success.png';
      img.style.width = '80px';
      img.style.height = '80px';
      img.style.margin = '0 auto 16px auto';
      img.style.display = 'block';
      
      // Create and add the text
      const textDiv = document.createElement('div');
      textDiv.textContent = textContent;
      textDiv.style.fontSize = '18px';
      textDiv.style.fontWeight = '600';
      textDiv.style.color = '#1a1a1a';
      textDiv.style.textAlign = 'center';
      
      // Append to wrapper
      wrapper.appendChild(img);
      wrapper.appendChild(textDiv);
      
      // Append wrapper to alert
      alertElement.appendChild(wrapper);
    }
  }, 50);
  
  // Auto dismiss after 2 seconds
  setTimeout(() => {
    alert.dismiss();
  }, 2000);
};

// Methods
const editProfile = async () => {
  const alert = await alertController.create({
    header: 'Edit Profile',
    message: 'Choose what to edit',
    buttons: [
      {
        text: 'Personal Info',
        handler: () => {
          console.log('Edit personal info');
        }
      },
      {
        text: 'Shop Info',
        handler: () => {
          console.log('Edit shop info');
        }
      },
      {
        text: 'Cancel',
        role: 'cancel'
      }
    ]
  });
  await alert.present();
};

const changePhoto = async () => {
  const actionSheet = await actionSheetController.create({
    header: 'Change Profile Photo',
    buttons: [
      {
        text: 'Take Photo',
        icon: cameraOutline,
        handler: async () => {
          await showLoadingModal('Opening camera...');
        }
      },
      {
        text: 'Choose from Gallery',
        icon: imagesOutline,
        handler: async () => {
          await showLoadingModal('Opening gallery...');
        }
      },
      {
        text: 'Remove Photo',
        icon: trashOutline,
        role: 'destructive',
        handler: () => {
          profileData.value.avatar = null;
        }
      },
      {
        text: 'Cancel',
        icon: closeOutline,
        role: 'cancel'
      }
    ]
  });
  await actionSheet.present();
};

const editField = async (field) => {
  const fieldNames = {
    name: 'Full Name',
    phone: 'Phone Number',
    email: 'Email Address',
    address: 'Address',
    shopName: 'Shop Name',
    shopAddress: 'Shop Location',
    shopHours: 'Operating Hours',
    description: 'Shop Description',
    gcash: 'GCash Number',
    bank: 'Bank Account'
  };

  const alert = await alertController.create({
    header: `Edit ${fieldNames[field]}`,
    inputs: [
      {
        name: 'value',
        type: field === 'email' ? 'email' : field === 'phone' ? 'tel' : 'text',
        placeholder: `Enter ${fieldNames[field]}`,
        value: profileData.value[field]
      }
    ],
    buttons: [
      {
        text: 'Cancel',
        role: 'cancel'
      },
      {
        text: 'Save',
        handler: async (data) => {
          if (data.value) {
            profileData.value[field] = data.value;
            await showSuccessModal('Updated successfully!');
          }
        }
      }
    ]
  });
  await alert.present();
};

const manageCategories = async () => {
  const alert = await alertController.create({
    header: 'Manage Categories',
    message: 'Select product categories',
    inputs: [
      { type: 'checkbox', label: 'Fish', value: 'Fish', checked: profileData.value.categories.includes('Fish') },
      { type: 'checkbox', label: 'Shellfish', value: 'Shellfish', checked: profileData.value.categories.includes('Shellfish') },
      { type: 'checkbox', label: 'Squid & Octopus', value: 'Squid', checked: profileData.value.categories.includes('Squid') },
      { type: 'checkbox', label: 'Pork', value: 'Pork', checked: profileData.value.categories.includes('Pork') },
      { type: 'checkbox', label: 'Chicken', value: 'Chicken', checked: profileData.value.categories.includes('Chicken') },
      { type: 'checkbox', label: 'Vegetables', value: 'Vegetables', checked: profileData.value.categories.includes('Vegetables') },
      { type: 'checkbox', label: 'Fruits', value: 'Fruits', checked: profileData.value.categories.includes('Fruits') },
      { type: 'checkbox', label: 'Condiments', value: 'Condiments', checked: profileData.value.categories.includes('Condiments') },
      { type: 'checkbox', label: 'Dairy & Goods', value: 'Dairy', checked: profileData.value.categories.includes('Dairy') },
      { type: 'checkbox', label: 'Rice & Grains', value: 'Rice', checked: profileData.value.categories.includes('Rice') }
    ],
    buttons: [
      { text: 'Cancel', role: 'cancel' },
      {
        text: 'Save',
        handler: async (data) => {
          profileData.value.categories = data;
          await showSuccessModal('Categories updated!');
        }
      }
    ]
  });
  await alert.present();
};

const changePassword = async () => {
  const alert = await alertController.create({
    header: 'Change Password',
    inputs: [
      {
        name: 'currentPassword',
        type: 'password',
        placeholder: 'Current Password'
      },
      {
        name: 'newPassword',
        type: 'password',
        placeholder: 'New Password'
      },
      {
        name: 'confirmPassword',
        type: 'password',
        placeholder: 'Confirm New Password'
      }
    ],
    buttons: [
      { text: 'Cancel', role: 'cancel' },
      {
        text: 'Update',
        handler: async (data) => {
          if (data.newPassword === data.confirmPassword) {
            await showSuccessModal('Password updated successfully!');
          } else {
            const toast = await toastController.create({
              message: '❌ Passwords do not match',
              duration: 2000,
              color: 'danger',
              position: 'top'
            });
            await toast.present();
            return false;
          }
        }
      }
    ]
  });
  await alert.present();
};

const notificationSettings = () => {
  router.push('/notifications');
};

const toggleDarkMode = () => {
  console.log('Dark mode:', darkMode.value);
  // TODO: Implement dark mode
};

const helpCenter = async () => {
  await showLoadingModal('Opening help center...');
};

const contactSupport = async () => {
  const actionSheet = await actionSheetController.create({
    header: 'Contact Support',
    buttons: [
      {
        text: 'Chat Support',
        handler: async () => {
          await showLoadingModal('Opening chat...');
        }
      },
      {
        text: 'Email Support',
        handler: async () => {
          await showLoadingModal('Opening email...');
        }
      },
      {
        text: 'Call Support',
        handler: async () => {
          await showLoadingModal('Connecting call...');
        }
      },
      {
        text: 'Cancel',
        role: 'cancel'
      }
    ]
  });
  await actionSheet.present();
};

const showTerms = async () => {
  const alert = await alertController.create({
    header: 'Terms & Conditions',
    message: 'Terms and conditions content will be displayed here.',
    buttons: ['Close']
  });
  await alert.present();
};

const showPrivacy = async () => {
  const alert = await alertController.create({
    header: 'Privacy Policy',
    message: 'Privacy policy content will be displayed here.',
    buttons: ['Close']
  });
  await alert.present();
};

const aboutApp = async () => {
  const alert = await alertController.create({
    header: 'About Palengke Online',
    message: 'Palengke Online Seller\nVersion 1.0.0 (Build 100)\n\nEmpowering Filipino sellers with digital solutions.\n\n© 2024 Palengke Online\nAll rights reserved.',
    buttons: ['Close']
  });
  await alert.present();
};

const handleLogout = async () => {
  const alert = await alertController.create({
    header: 'Logout',
    message: 'Are you sure you want to logout?',
    buttons: [
      {
        text: 'Cancel',
        role: 'cancel'
      },
      {
        text: 'Logout',
        role: 'destructive',
        handler: () => {
          localStorage.removeItem('isAuthenticated');
          showSuccessLogout();
          setTimeout(() => {
            router.push('/login');
          }, 1500);
        }
      }
    ]
  });
  await alert.present();
};

const showSuccessLogout = async () => {
  const alert = await alertController.create({
    cssClass: 'success-modal',
    header: ' ',
    message: 'Logged out successfully',
    buttons: ['OK']
  });
  
  await alert.present();
  
  // Add the image after modal is presented
  setTimeout(() => {
    const alertElement = document.querySelector('.success-modal .alert-message');
    if (alertElement) {
      // Clear the text content
      const textContent = alertElement.textContent;
      alertElement.innerHTML = '';
      
      // Create wrapper for centering
      const wrapper = document.createElement('div');
      wrapper.style.display = 'flex';
      wrapper.style.flexDirection = 'column';
      wrapper.style.alignItems = 'center';
      wrapper.style.textAlign = 'center';
      
      // Create and add the image
      const img = document.createElement('img');
      img.src = '/assets/images/checkmark_success.png';
      img.style.width = '80px';
      img.style.height = '80px';
      img.style.margin = '0 auto 16px auto';
      img.style.display = 'block';
      
      // Create and add the text
      const textDiv = document.createElement('div');
      textDiv.textContent = textContent;
      textDiv.style.fontSize = '18px';
      textDiv.style.fontWeight = '600';
      textDiv.style.color = '#1a1a1a';
      textDiv.style.textAlign = 'center';
      
      // Append to wrapper
      wrapper.appendChild(img);
      wrapper.appendChild(textDiv);
      
      // Append wrapper to alert
      alertElement.appendChild(wrapper);
    }
  }, 50);
  
  setTimeout(() => {
    alert.dismiss();
  }, 1500);
};

// Bottom Navigation
const goToHome = () => {
  router.push('/home');
};

const goToProducts = () => {
  router.push('/products');
};

const goToOrders = () => {
  router.push('/orders');
};

const goToProfile = () => {
  // Already on profile page
};
</script>

<style scoped>
.profile-container {
  background: #f8f9fa;
  min-height: 100%;
  padding-bottom: 40px;
}

/* Profile Header */
.profile-header {
  background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
  padding: 40px 20px 32px 20px;
  text-align: center;
  color: white;
}

.profile-avatar-container {
  position: relative;
  display: inline-block;
  margin-bottom: 16px;
}

.profile-avatar {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.2);
  border: 4px solid white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 48px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.profile-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.change-photo-btn {
  position: absolute;
  bottom: 0;
  right: 0;
  --background: white;
  --color: #4CAF50;
  --border-radius: 50%;
  width: 36px;
  height: 36px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.profile-name {
  font-size: 24px;
  font-weight: 700;
  margin: 0 0 4px 0;
}

.profile-type {
  font-size: 14px;
  margin: 0 0 24px 0;
  opacity: 0.9;
}

.profile-stats {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 24px;
}

.stat-item {
  text-align: center;
}

.stat-value {
  font-size: 24px;
  font-weight: 700;
  margin-bottom: 4px;
}

.stat-label {
  font-size: 12px;
  opacity: 0.9;
}

.stat-divider {
  width: 1px;
  height: 40px;
  background: rgba(255, 255, 255, 0.3);
}

/* Sections */
.section {
  background: white;
  margin: 16px 16px 0 16px;
  border-radius: 16px;
  padding: 20px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
}

.section-title {
  font-size: 16px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 16px 0;
}

/* Info List */
.info-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.info-item {
  display: flex;
  align-items: center;
  gap: 12px;
  cursor: pointer;
  padding: 12px;
  margin: -12px;
  border-radius: 12px;
  transition: background 0.2s;
}

.info-item:active {
  background: #f8f9fa;
}

.info-icon {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  background: #E8F5E9;
  color: #4CAF50;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 22px;
  flex-shrink: 0;
}

.info-icon.payment {
  background: #FFF3E0;
  color: #FF9800;
}

.info-content {
  flex: 1;
  min-width: 0;
}

.info-label {
  font-size: 12px;
  color: #999;
  margin: 0 0 4px 0;
}

.info-value {
  font-size: 15px;
  color: #1a1a1a;
  font-weight: 500;
  margin: 0;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.info-arrow {
  font-size: 20px;
  color: #ccc;
}

/* Categories */
.categories-display {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.category-chip {
  background: #f0f0f0;
  padding: 8px 16px;
  border-radius: 20px;
  font-size: 13px;
  color: #333;
  font-weight: 500;
}

.category-chip.add {
  background: #E8F5E9;
  color: #4CAF50;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 4px;
}

/* Settings List */
.settings-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.settings-item {
  display: flex;
  align-items: center;
  gap: 12px;
  cursor: pointer;
  padding: 12px;
  margin: -12px;
  border-radius: 12px;
  transition: background 0.2s;
}

.settings-item:active {
  background: #f8f9fa;
}

.settings-icon {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  background: #f0f0f0;
  color: #666;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 22px;
  flex-shrink: 0;
}

.settings-content {
  flex: 1;
  min-width: 0;
}

.settings-label {
  font-size: 15px;
  color: #1a1a1a;
  font-weight: 500;
  margin: 0;
}

.settings-value {
  font-size: 13px;
  color: #999;
  margin: 4px 0 0 0;
}

.settings-arrow {
  font-size: 20px;
  color: #ccc;
}

/* Logout Section */
.logout-section {
  padding: 0 16px;
  margin-top: 24px;
}

.logout-button {
  --border-radius: 12px;
  height: 48px;
  font-weight: 600;
  text-transform: none;
  font-size: 15px;
}

/* App Version */
.app-version {
  text-align: center;
  padding: 24px 16px;
}

.app-version p {
  font-size: 12px;
  color: #999;
  margin: 4px 0;
}

/* Responsive */
@media (max-width: 360px) {
  .profile-avatar {
    width: 80px;
    height: 80px;
  }
  
  .profile-name {
    font-size: 20px;
  }
  
  .stat-value {
    font-size: 20px;
  }
}

/* Bottom Navigation */
.bottom-nav {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  height: 60px;
}

.nav-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 4px;
  cursor: pointer;
  color: #999;
  transition: color 0.2s;
}

.nav-item ion-icon {
  font-size: 24px;
}

.nav-item span {
  font-size: 11px;
  font-weight: 500;
}

.nav-item.active {
  color: #4CAF50;
}

.nav-item:active {
  transform: scale(0.95);
}

/* Success Modal Styling */
:deep(.success-modal .alert-wrapper) {
  border-radius: 20px;
  text-align: center;
  max-width: 320px;
  padding: 30px 20px;
}

:deep(.success-modal .alert-head) {
  padding: 0;
  min-height: 0;
}

:deep(.success-modal .alert-message) {
  font-size: 18px;
  font-weight: 600;
  color: #1a1a1a;
  padding: 20px 10px;
}

:deep(.success-modal .alert-button-group) {
  padding: 10px 0 0 0;
}

:deep(.success-modal button) {
  background: #4CAF50;
  color: white;
  font-weight: 600;
  border-radius: 12px;
  height: 44px;
}

/* ✨ NEW: Loading Modal Styling */
:deep(.loading-modal-simple .alert-wrapper) {
  border-radius: 20px;
  text-align: center;
  max-width: 280px;
  padding: 50px 30px;
  background: white;
}

:deep(.loading-modal-simple .alert-head) {
  display: none;
}

:deep(.loading-modal-simple .alert-message) {
  padding: 0;
  position: relative;
  padding-top: 70px;
  font-size: 16px;
  font-weight: 600;
  color: #1a1a1a;
}

:deep(.loading-modal-simple .alert-message::before) {
  content: '';
  position: absolute;
  top: 0;
  left: 50%;
  margin-left: -25px;
  width: 50px;
  height: 50px;
  border: 5px solid #f3f3f3;
  border-top: 5px solid #1877F2;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

:deep(.loading-modal-simple .alert-button-group) {
  display: none;
}
</style>