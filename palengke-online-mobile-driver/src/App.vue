<template>
  <ion-app>
    <SplashScreen />
    <ion-router-outlet />
  </ion-app>
</template>

<script>
import { IonApp, IonRouterOutlet } from '@ionic/vue';
import SplashScreen from './components/SplashScreen.vue';

export default {
  name: 'App',
  components: {
    IonApp,
    IonRouterOutlet,
    SplashScreen
  }
};
</script>