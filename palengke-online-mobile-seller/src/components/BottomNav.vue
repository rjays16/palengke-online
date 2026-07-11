<template>
  <ion-footer>
    <ion-toolbar>
      <div class="bottom-nav">
        <div class="nav-item" :class="{ active: active === 'home' }" @click="navigate('/home')">
          <ion-icon :icon="homeOutline"></ion-icon>
          <span>Home</span>
        </div>
        <div class="nav-item" :class="{ active: active === 'products' }" @click="navigate('/products')">
          <ion-icon :icon="cubeOutline"></ion-icon>
          <span>Products</span>
        </div>
        <div class="nav-item" :class="{ active: active === 'orders' }" @click="navigate('/orders')">
          <ion-icon :icon="receiptOutline"></ion-icon>
          <span>Orders</span>
        </div>
        <div class="nav-item" :class="{ active: active === 'profile' }" @click="navigate('/profile')">
          <ion-icon :icon="personOutline"></ion-icon>
          <span>Profile</span>
        </div>
      </div>
    </ion-toolbar>
  </ion-footer>
</template>

<script setup lang="ts">
import { IonFooter, IonToolbar, IonIcon } from '@ionic/vue';
import { homeOutline, cubeOutline, receiptOutline, personOutline } from 'ionicons/icons';
import { useRouter } from 'vue-router';

defineProps<{
  active: 'home' | 'products' | 'orders' | 'profile';
}>();

const router = useRouter();

const navigate = (path: string) => {
  if (router.currentRoute.value.path !== path) {
    router.push(path);
  }
};
</script>

<style scoped>
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
  color: #4caf50;
}

.nav-item:active {
  transform: scale(0.95);
}
</style>
