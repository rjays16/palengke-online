<template>
  <div v-if="showSplash" class="splash-container">
    <div class="logo-wrapper">
      <img src="/assets/images/logo-without-circle.png" alt="Palengke Online" class="logo-image" />
      <svg class="loading-circle" viewBox="0 0 200 200">
        <circle cx="100" cy="100" r="85" fill="none" stroke="#e0e0e0" stroke-width="6" />

        <circle
          class="animated-stroke-spin"
          cx="100"
          cy="100"
          r="85"
          fill="none"
          stroke="#2c2c2c"
          stroke-width="6"
          stroke-linecap="round"
        />
      </svg>
    </div>

    <!-- Loading text -->
    <div class="loading-text">Loading...</div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';

export default {
  name: 'SplashScreen',
  setup() {
    const showSplash = ref(true);

    onMounted(() => {
      setTimeout(() => {
        showSplash.value = false;
      }, 3000);
    });

    return {
      showSplash,
    };
  },
};
</script>

<style scoped>
.splash-container {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  z-index: 9999;
  animation: fadeOut 0.6s ease-in-out 2.5s forwards;
}

.logo-wrapper {
  position: relative;
  width: 200px;
  height: 200px;
  display: flex;
  justify-content: center;
  align-items: center;
}

.logo-image {
  width: 200px;
  height: auto;
  position: relative;
  z-index: 2;
  animation: logoScaleIn 0.6s ease-out;
}

.loading-circle {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 1;
  transform: rotate(-90deg);
  animation: rotateSVG 2s linear infinite;
}

.animated-stroke-spin {
  stroke-dasharray: 400, 534;
  stroke-dashoffset: 0;
}

.loading-text {
  margin-top: 40px;
  font-size: 14px;
  font-weight: 500;
  color: #333;
  letter-spacing: 3px;
  text-transform: uppercase;
  animation: textPulse 1.8s ease-in-out infinite;
}

@keyframes logoScaleIn {
  0% {
    transform: scale(0.7);
    opacity: 0;
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}

@keyframes rotateSVG {
  0% {
    transform: rotate(-90deg);
  }
  100% {
    transform: rotate(270deg);
  }
}

@keyframes textPulse {
  0%,
  100% {
    opacity: 0.3;
  }
  50% {
    opacity: 1;
  }
}

@keyframes fadeOut {
  0% {
    opacity: 1;
  }
  100% {
    opacity: 0;
    visibility: hidden;
  }
}

@media (max-width: 576px) {
  .logo-wrapper {
    width: 180px;
    height: 180px;
  }

  .logo-image {
    width: 150px;
  }

  .loading-text {
    font-size: 12px;
    margin-top: 30px;
  }
}
</style>
