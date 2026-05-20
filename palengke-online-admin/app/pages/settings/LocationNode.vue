<template>
  <div class="node-row" @click="$emit('toggle')">
    <button class="chevron" :class="{ open: isOpen }">
      <svg
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
      >
        <polyline points="9 18 15 12 9 6" />
      </svg>
    </button>
    <label class="checkbox-label" @click.stop>
      <input
        type="checkbox"
        :checked="node.checked"
        :indeterminate.prop="node.indeterminate"
        @change="$emit('check')"
      />
      <span class="checkmark" />
      <span class="node-name">{{ node.name }}</span>
      <span :class="['node-badge', badgeClass]">{{ badgeText }}</span>
    </label>
    <span class="node-meta">{{ enabledCount }} / {{ totalCount }} enabled</span>
  </div>
</template>

<script setup lang="ts">
import type { Region, City } from "./locations-data";

interface Props {
  node: Region | City;
  isOpen: boolean;
  badgeText: string;
  badgeClass: string;
}

const props = defineProps<Props>();

defineEmits<{
  (e: "toggle"): void;
  (e: "check"): void;
}>();

const enabledCount = computed(() => {
  if ("cities" in props.node) {
    return props.node.cities.filter((c) => c.checked).length;
  }
  return props.node.barangays.filter((b) => b.checked).length;
});

const totalCount = computed(() => {
  if ("cities" in props.node) {
    return props.node.cities.length;
  }
  return props.node.barangays.length;
});
</script>

<style scoped>
.node-row {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 14px;
  cursor: pointer;
  transition: background 0.1s;
  user-select: none;
}
.node-row:hover {
  background: #f9fafb;
}
.chevron {
  width: 22px;
  height: 22px;
  border: none;
  background: none;
  color: #9ca3af;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0;
  flex-shrink: 0;
  transition: transform 0.2s;
}
.chevron svg {
  width: 14px;
  height: 14px;
}
.chevron.open {
  transform: rotate(90deg);
}
.checkbox-label {
  display: flex;
  align-items: center;
  gap: 10px;
  cursor: pointer;
  flex: 1;
  min-width: 0;
}
.checkbox-label input {
  display: none;
}
.checkmark {
  width: 18px;
  height: 18px;
  border: 1.5px solid #d1d5db;
  border-radius: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  transition: all 0.15s;
  position: relative;
}
.checkbox-label input:checked + .checkmark {
  background: #059669;
  border-color: #059669;
}
.checkbox-label input:checked + .checkmark::after {
  content: "";
  width: 5px;
  height: 9px;
  border: solid white;
  border-width: 0 2px 2px 0;
  transform: rotate(45deg);
  margin-bottom: 1px;
}
.checkbox-label input:indeterminate + .checkmark {
  background: #059669;
  border-color: #059669;
}
.checkbox-label input:indeterminate + .checkmark::after {
  content: "";
  width: 8px;
  height: 2px;
  background: white;
  border: none;
  transform: none;
  margin: 0;
}
.node-name {
  font-size: 13px;
  font-weight: 600;
  color: #111827;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.node-badge {
  font-size: 10px;
  font-weight: 600;
  padding: 2px 7px;
  border-radius: 10px;
  line-height: 1.3;
  flex-shrink: 0;
}
.node-meta {
  font-size: 11px;
  color: #9ca3af;
  flex-shrink: 0;
  margin-left: auto;
  padding-left: 8px;
}
</style>
