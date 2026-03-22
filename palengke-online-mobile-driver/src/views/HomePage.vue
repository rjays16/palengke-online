<template>
  <ion-page>
    <ion-header>
      <ion-toolbar color="success">
        <ion-title>Palengke Online</ion-title>
        <ion-buttons slot="end">
          <ion-button @click="logout">
            <ion-icon :icon="logOutOutline"></ion-icon>
          </ion-button>
        </ion-buttons>
      </ion-toolbar>
    </ion-header>

    <ion-content class="ion-padding">
      <div class="home-container">
        <!-- Welcome Section -->
        <div class="welcome-section">
          <div class="logo-container">
            <img src="/assets/images/logo-without-circle.png" alt="Palengke Online" class="logo">
          </div>
          <h1>Welcome to Palengke Online! <font-awesome-icon icon="carrot" /><font-awesome-icon icon="carrot" /><font-awesome-icon icon="seedling" /></h1>
          <p class="subtitle">Fresh market delivered to your door</p>
        </div>

        <!-- Quick Stats -->
        <div class="stats-grid">
          <div class="stat-card">
            <ion-icon :icon="cartOutline" class="stat-icon"></ion-icon>
            <div class="stat-value">0</div>
            <div class="stat-label">Orders</div>
          </div>
          
          <div class="stat-card">
            <ion-icon :icon="heartOutline" class="stat-icon"></ion-icon>
            <div class="stat-value">0</div>
            <div class="stat-label">Favorites</div>
          </div>
          
          <div class="stat-card">
            <ion-icon :icon="walletOutline" class="stat-icon"></ion-icon>
            <div class="stat-value">₱0</div>
            <div class="stat-label">Wallet</div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-section">
          <ion-button expand="block" class="action-btn">
            <ion-icon :icon="searchOutline" slot="start"></ion-icon>
            Browse Products
          </ion-button>
          
          <ion-button expand="block" fill="outline" class="action-btn">
            <ion-icon :icon="personOutline" slot="start"></ion-icon>
            View Profile
          </ion-button>
          
          <ion-button expand="block" color="danger" @click="logout">
            <ion-icon :icon="logOutOutline" slot="start"></ion-icon>
            Logout
          </ion-button>
        </div>

        <!-- Info Text -->
        <p class="info-text">
          You are successfully logged in! <font-awesome-icon icon="glass-cheers" />
        </p>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { useRouter } from 'vue-router';
import { 
  IonPage, 
  IonHeader, 
  IonToolbar, 
  IonTitle, 
  IonContent, 
  IonButton,
  IonButtons,
  IonIcon,
  alertController
} from '@ionic/vue';
import { 
  logOutOutline,
  cartOutline,
  heartOutline,
  walletOutline,
  searchOutline,
  personOutline
} from 'ionicons/icons';

const router = useRouter();

const logout = async () => {
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
        handler: () => {
          // Clear authentication
          localStorage.removeItem('isAuthenticated');
          localStorage.removeItem('token');
          
          // Navigate to login
          router.push('/login');
        }
      }
    ]
  });
  
  await alert.present();
};
</script>

<style scoped>
.home-container {
  max-width: 600px;
  margin: 0 auto;
  padding: 20px 0;
}

/* Welcome Section */
.welcome-section {
  text-align: center;
  margin-bottom: 40px;
}

.logo-container {
  margin-bottom: 20px;
}

.logo {
  width: 120px;
  height: auto;
  animation: float 3s ease-in-out infinite;
}

h1 {
  font-size: 28px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 8px 0;
}

.subtitle {
  font-size: 16px;
  color: #666;
  margin: 0;
}

/* Stats Grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 12px;
  margin-bottom: 32px;
}

.stat-card {
  background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
  border-radius: 16px;
  padding: 20px 12px;
  text-align: center;
  color: white;
  box-shadow: 0 4px 12px rgba(76, 175, 80, 0.2);
}

.stat-icon {
  font-size: 32px;
  margin-bottom: 8px;
}

.stat-value {
  font-size: 24px;
  font-weight: 700;
  margin-bottom: 4px;
}

.stat-label {
  font-size: 12px;
  opacity: 0.9;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

/* Action Buttons */
.action-section {
  margin-bottom: 32px;
}

.action-btn {
  --border-radius: 14px;
  margin-bottom: 12px;
  height: 52px;
  font-weight: 600;
  text-transform: none;
}

.action-btn::part(native) {
  font-size: 16px;
}

/* Info Text */
.info-text {
  text-align: center;
  color: #666;
  font-size: 14px;
  margin-top: 40px;
}

/* Animations */
@keyframes float {
  0%, 100% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-10px);
  }
}

/* Responsive */
@media (max-width: 360px) {
  h1 {
    font-size: 24px;
  }
  
  .stats-grid {
    gap: 8px;
  }
  
  .stat-card {
    padding: 16px 8px;
  }
  
  .stat-icon {
    font-size: 28px;
  }
  
  .stat-value {
    font-size: 20px;
  }
}
</style>