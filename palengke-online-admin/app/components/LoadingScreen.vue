<template>
  <div v-if="visible" class="loading-overlay" :class="{ 'fade-out': fading }">
    <div class="loader-content">
      <div class="logo-wrapper">
        <img
          src="/assets/images/logo-without-circle.png"
          alt="Palengke Online"
          class="logo-img"
        />
        <svg class="loading-ring" viewBox="0 0 200 200">
          <circle
            cx="100"
            cy="100"
            r="85"
            fill="none"
            stroke="#e5e7eb"
            stroke-width="6"
          />
          <circle
            class="ring-spin"
            cx="100"
            cy="100"
            r="85"
            fill="none"
            stroke="#1a1a1a"
            stroke-width="6"
            stroke-linecap="round"
          />
        </svg>
      </div>
      <div class="loading-text">Loading</div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from "vue";
import { useRoute } from "vue-router";

const route = useRoute();
const visible = ref(true);
const fading = ref(false);

let timeout;

function show() {
  clearTimeout(timeout);
  visible.value = true;
  fading.value = false;
}

function hide(delay = 400) {
  timeout = setTimeout(() => {
    fading.value = true;
    setTimeout(() => {
      visible.value = false;
      fading.value = false;
    }, 400);
  }, delay);
}

// Initial load: show for 2.5s
hide(2500);

// Every page navigation: show briefly
watch(
  () => route.path,
  () => {
    show();
    hide(500);
  },
);
</script>

<style scoped>
.loading-overlay {
  position: fixed;
  inset: 0;
  background: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  transition: opacity 0.4s ease;
}

.loading-overlay.fade-out {
  opacity: 0;
  pointer-events: none;
}

.loader-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 36px;
}

.logo-wrapper {
  position: relative;
  width: 180px;
  height: 180px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.logo-img {
  width: 180px;
  height: auto;
  position: relative;
  z-index: 2;
  animation: logoIn 0.5s ease-out;
}

.loading-ring {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  z-index: 1;
  transform: rotate(-90deg);
  animation: ringSpin 1.5s linear infinite;
}

.ring-spin {
  stroke-dasharray: 400, 534;
  stroke-dashoffset: 0;
}

.loading-text {
  font-size: 13px;
  font-weight: 600;
  color: #6b7280;
  letter-spacing: 4px;
  text-transform: uppercase;
  animation: pulse 1.5s ease-in-out infinite;
}

@keyframes logoIn {
  0% {
    transform: scale(0.7);
    opacity: 0;
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}

@keyframes ringSpin {
  0% {
    transform: rotate(-90deg);
  }
  100% {
    transform: rotate(270deg);
  }
}

@keyframes pulse {
  0%,
  100% {
    opacity: 0.3;
  }
  50% {
    opacity: 1;
  }
}
</style>
