<template>
  <ion-page>
    <ion-header class="ion-no-border">
      <ion-toolbar>
        <ion-buttons slot="start">
          <ion-button @click="goBack">
            <ion-icon :icon="arrowBackOutline"></ion-icon>
          </ion-button>
        </ion-buttons>
        <ion-title>Subscription Plans</ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content class="ion-padding">
      <div class="plans-container">
        <!-- Status Banner -->
        <div class="status-banner" :class="sub.status">
          <div class="status-icon">
            <ion-icon :icon="sub.status === 'trial' ? timeOutline : sub.status === 'active' ? checkmarkCircleOutline : alertCircleOutline"></ion-icon>
          </div>
          <div class="status-info">
            <p class="status-title" v-if="sub.status === 'trial'">Trial Period</p>
            <p class="status-title" v-else-if="sub.status === 'active'">Active Plan</p>
            <p class="status-title" v-else>Trial Expired</p>
            <p class="status-days" v-if="remaining > 0">{{ remaining }} day{{ remaining > 1 ? 's' : '' }} remaining</p>
            <p class="status-days" v-else>Choose a plan to continue selling</p>
          </div>
        </div>

        <!-- Plans Grid -->
        <div class="plans-grid">
          <div
            v-for="plan in plans"
            :key="plan.id"
            class="plan-card"
            :class="{ popular: plan.popular, selected: selected === plan.id }"
            @click="selected = plan.id"
          >
            <div class="plan-badge" v-if="plan.popular">Best Value</div>
            <div class="plan-header">
              <h3 class="plan-name">{{ plan.name }}</h3>
              <div class="plan-price">
                <span class="price-symbol">₱</span>
                <span class="price-value">{{ plan.price.toLocaleString() }}</span>
              </div>
              <p class="plan-per" v-if="plan.savings">{{ plan.savings }}</p>
            </div>
            <div class="plan-features">
              <div class="feature" v-for="feature in plan.features" :key="feature">
                <ion-icon :icon="checkmarkOutline"></ion-icon>
                <span>{{ feature }}</span>
              </div>
            </div>
            <ion-button expand="block" class="select-btn" :fill="plan.popular ? 'solid' : 'outline'" @click.stop="selected = plan.id">
              {{ selected === plan.id ? 'Selected' : 'Select' }}
            </ion-button>
          </div>
        </div>

        <!-- Subscribe Button -->
        <ion-button expand="block" class="subscribe-btn" :disabled="!selected" @click="handleSubscribe">
          Subscribe Now — ₱{{ selectedPlan?.price.toLocaleString() }}
        </ion-button>
        <p class="secure-note">
          <ion-icon :icon="lockClosedOutline"></ion-icon>
          Secure payment. Your info is protected.
        </p>

        <!-- Logout -->
        <div class="logout-section">
          <ion-button expand="block" fill="clear" class="logout-btn" @click="handleLogout">
            <ion-icon :icon="logOutOutline" slot="start"></ion-icon>
            Logout
          </ion-button>
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import {
  IonPage, IonHeader, IonToolbar, IonTitle, IonContent,
  IonButton, IonButtons, IonIcon, alertController, toastController
} from '@ionic/vue'
import {
  arrowBackOutline, timeOutline, checkmarkCircleOutline,
  alertCircleOutline, checkmarkOutline, lockClosedOutline, logOutOutline
} from 'ionicons/icons'
import { PLANS, getSubscription, daysLeft, refreshSubscription, subscribePlan } from '@/services/subscriptionService'

const router = useRouter()
const sub = ref(getSubscription())
const remaining = ref(daysLeft(sub.value))
const plans = PLANS
const selected = ref('')
const subscribing = ref(false)

const selectedPlan = computed(() => plans.find(p => p.id === selected.value))

const goBack = () => router.back()

// Refresh subscription from API on mount
refreshSubscription().then(s => {
  sub.value = s
  remaining.value = daysLeft(s)
})

const handleSubscribe = async () => {
  if (!selected.value) return

  const plan = selectedPlan.value

  const alert = await alertController.create({
    header: 'Confirm Subscription',
    subHeader: `${plan.name} — ₱${plan.price.toLocaleString()}`,
    message: 'This is a mock payment. No real charge will be made.',
    buttons: [
      { text: 'Cancel', role: 'cancel', cssClass: 'alert-button-cancel' },
      {
        text: 'Confirm',
        cssClass: 'alert-button-confirm',
        handler: async () => {
          subscribing.value = true
          try {
            const s = await subscribePlan(selected.value)
            sub.value = s
            remaining.value = daysLeft(s)
            const toast = await toastController.create({
              message: `Subscribed to ${plan.name} plan!`,
              duration: 2000, color: 'success', position: 'top',
            })
            await toast.present()
            setTimeout(() => router.push('/home'), 500)
          } catch {
            const toast = await toastController.create({
              message: 'Subscription failed. Please try again.',
              duration: 2000, color: 'danger', position: 'top',
            })
            await toast.present()
          } finally {
            subscribing.value = false
          }
        }
      }
    ]
  })
  await alert.present()
}

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
        handler: async () => {
          const email = localStorage.getItem('userEmail')
          try {
            await fetch('http://localhost:8000/api/seller/devices/logout-current', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ email }),
            })
          } catch { /* silent */ }
          ;['isAuthenticated', 'token', 'pendingAuth'].forEach(k => localStorage.removeItem(k))
          const toast = await toastController.create({
            message: 'Logged out successfully.',
            duration: 1500,
            color: 'success',
            position: 'top',
          })
          await toast.present()
          setTimeout(() => router.push('/login'), 500)
        }
      }
    ]
  })
  await alert.present()
}
</script>

<style scoped>
ion-toolbar { --background: transparent; --border-width: 0; }
ion-toolbar ion-title { font-size: 18px; font-weight: 700; }
ion-toolbar ion-button { --color: var(--color-text-secondary); }

.plans-container {
  max-width: 500px;
  margin: 0 auto;
  padding-bottom: 40px;
}

/* Status Banner */
.status-banner {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 16px;
  border-radius: 14px;
  margin-bottom: 24px;
}

.status-banner.trial { background: #E3F2FD; color: #1565C0; }
.status-banner.active { background: #E8F5E9; color: #2E7D32; }
.status-banner.expired { background: #FFEBEE; color: #c62828; }

.status-icon ion-icon { font-size: 28px; }
.status-title { font-size: 15px; font-weight: 700; margin: 0 0 2px; }
.status-days { font-size: 13px; margin: 0; opacity: 0.85; }

/* Plans Grid */
.plans-grid { display: flex; flex-direction: column; gap: 14px; margin-bottom: 24px; }

.plan-card {
  position: relative;
  background: var(--color-card-bg, #fff);
  border: 2px solid var(--color-border, #e0e0e0);
  border-radius: 16px;
  padding: 20px;
  cursor: pointer;
  transition: all 0.2s;
}

.plan-card.popular { border-color: #4CAF50; }
.plan-card.selected { border-color: #4CAF50; background: #f0fdf4; }
.plan-card:active { transform: scale(0.98); }

.plan-badge {
  position: absolute;
  top: -10px;
  left: 50%;
  transform: translateX(-50%);
  background: #4CAF50;
  color: #fff;
  font-size: 11px;
  font-weight: 700;
  padding: 4px 14px;
  border-radius: 20px;
  white-space: nowrap;
}

.plan-header { text-align: center; margin-bottom: 16px; padding-top: 4px; }

.plan-name {
  font-size: 16px;
  font-weight: 600;
  color: var(--color-text-primary, #1a1a1a);
  margin: 0 0 8px;
}

.plan-price { display: flex; align-items: baseline; justify-content: center; gap: 2px; }
.price-symbol { font-size: 18px; font-weight: 600; color: var(--color-text-primary, #1a1a1a); }
.price-value { font-size: 32px; font-weight: 800; font-family: 'Plus Jakarta Sans', sans-serif; color: var(--color-text-primary, #1a1a1a); }
.plan-per { font-size: 12px; color: #4CAF50; font-weight: 500; margin: 4px 0 0; }

.plan-features { display: flex; flex-direction: column; gap: 8px; margin-bottom: 16px; }

.feature {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  color: var(--color-text-secondary, #666);
}

.feature ion-icon { font-size: 16px; color: #4CAF50; flex-shrink: 0; }

.select-btn {
  --border-radius: 10px;
  font-size: 13px;
  font-weight: 600;
  text-transform: none;
  height: 42px;
}

.subscribe-btn {
  --border-radius: 14px;
  height: 54px;
  font-size: 16px;
  font-weight: 700;
  text-transform: none;
  --background: #4CAF50;
  margin-bottom: 12px;
}

.secure-note {
  text-align: center;
  font-size: 12px;
  color: var(--color-text-tertiary, #999);
  margin: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
}

.secure-note ion-icon { font-size: 14px; }

/* Dark mode overrides */
.logout-section { margin-top: 32px; border-top: 1px solid var(--color-border, #e0e0e0); padding-top: 20px; }
.logout-btn {
  --color: var(--ion-color-danger, #c62828);
  font-size: 14px;
  font-weight: 600;
  text-transform: none;
}

@media (prefers-color-scheme: dark) {
  .plan-card { background: #2d2d2d; border-color: #404040; }
  .plan-card.selected { background: rgba(76, 175, 80, 0.1); border-color: #66BB6A; }
  .plan-badge { background: #66BB6A; }
}
</style>
