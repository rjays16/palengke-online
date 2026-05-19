<template>
  <div class="pagination-root" v-if="totalPages > 1">
    <div class="pagination-inner">
      <button
        class="pg-btn"
        :disabled="modelValue <= 1"
        @click="goTo(modelValue - 1)"
      >
        <svg
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
        >
          <polyline points="15 18 9 12 15 6" />
        </svg>
        Prev
      </button>
      <button
        v-for="p in pages"
        :key="p"
        class="pg-btn"
        :class="{ active: p === modelValue }"
        @click="goTo(p)"
      >
        {{ p }}
      </button>
      <button
        class="pg-btn"
        :disabled="modelValue >= totalPages"
        @click="goTo(modelValue + 1)"
      >
        Next
        <svg
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
        >
          <polyline points="9 18 15 12 9 6" />
        </svg>
      </button>
    </div>
    <span class="pg-info">{{ start }}–{{ end }} of {{ total }}</span>
  </div>
</template>

<script setup>
const props = defineProps({
  modelValue: { type: Number, required: true },
  total: { type: Number, required: true },
  perPage: { type: Number, default: 10 },
});
const emit = defineEmits(["update:modelValue"]);

const totalPages = computed(() =>
  Math.max(1, Math.ceil(props.total / props.perPage)),
);

const start = computed(() => (props.modelValue - 1) * props.perPage + 1);
const end = computed(() =>
  Math.min(props.modelValue * props.perPage, props.total),
);

const pages = computed(() => {
  const tp = totalPages.value;
  const cp = props.modelValue;
  if (tp <= 7) return Array.from({ length: tp }, (_, i) => i + 1);
  const p = [];
  p.push(1);
  if (cp > 3) p.push("...");
  for (let i = Math.max(2, cp - 1); i <= Math.min(tp - 1, cp + 1); i++)
    p.push(i);
  if (cp < tp - 2) p.push("...");
  p.push(tp);
  return p;
});

function goTo(p) {
  if (p < 1 || p > totalPages.value || p === props.modelValue) return;
  emit("update:modelValue", p);
}
</script>

<style scoped>
.pagination-root {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 16px;
  border-top: 1px solid #f3f4f6;
  flex-wrap: wrap;
  gap: 12px;
}
.pagination-inner {
  display: flex;
  align-items: center;
  gap: 4px;
}
.pg-btn {
  display: flex;
  align-items: center;
  gap: 4px;
  padding: 6px 12px;
  border: 1px solid #e5e7eb;
  background: #fff;
  border-radius: 6px;
  font-size: 12px;
  font-family: "Inter", sans-serif;
  color: #6b7280;
  cursor: pointer;
  transition: all 0.15s;
  min-width: 32px;
  justify-content: center;
}
.pg-btn:hover:not(:disabled):not(.active) {
  border-color: #059669;
  color: #059669;
}
.pg-btn.active {
  background: #059669;
  border-color: #059669;
  color: #fff;
  font-weight: 600;
}
.pg-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}
.pg-btn svg {
  width: 14px;
  height: 14px;
}
.pg-info {
  font-size: 12px;
  color: #9ca3af;
  white-space: nowrap;
}
</style>
