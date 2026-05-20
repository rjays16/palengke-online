<template>
  <div class="location-tree">
    <div
      v-for="region in modelValue"
      :key="region.id"
      class="tree-node region-node"
    >
      <LocationNode
        :node="region"
        :is-open="isOpen(region.id)"
        badge-text="Region"
        badge-class="region-badge"
        @toggle="toggleRegion(region.id)"
        @check="toggleRegionCheck(region)"
      />

      <div v-show="isOpen(region.id)" class="children cities">
        <div
          v-for="city in region.cities"
          :key="city.id"
          class="tree-node city-node"
        >
          <LocationNode
            :node="city"
            :is-open="isOpen(city.id)"
            badge-text="City"
            badge-class="city-badge"
            @toggle="toggleCity(city.id)"
            @check="toggleCityCheck(region, city)"
          />

          <div v-show="isOpen(city.id)" class="children barangays">
            <div
              v-for="barangay in city.barangays"
              :key="barangay.id"
              class="tree-node barangay-node"
            >
              <div class="node-row barangay-row">
                <label class="checkbox-label" @click.stop>
                  <input
                    type="checkbox"
                    v-model="barangay.checked"
                    @change="onBarangayChange(region, city)"
                  />
                  <span class="checkmark" />
                  <span class="node-name">{{ barangay.name }}</span>
                  <span class="node-badge barangay-badge">Barangay</span>
                </label>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Region, City } from "./locations-data";
import LocationNode from "./LocationNode.vue";

const props = defineProps<{
  modelValue: Region[];
  openIds: Set<string>;
}>();

const emit = defineEmits<{
  (e: "update:openIds", value: Set<string>): void;
  (e: "update:modelValue", value: Region[]): void;
}>();

function isOpen(id: string): boolean {
  return props.openIds.has(id);
}

function toggleRegion(id: string) {
  const newSet = new Set(props.openIds);
  if (newSet.has(id)) newSet.delete(id);
  else newSet.add(id);
  emit("update:openIds", newSet);
}

function toggleCity(id: string) {
  const newSet = new Set(props.openIds);
  if (newSet.has(id)) newSet.delete(id);
  else newSet.add(id);
  emit("update:openIds", newSet);
}

function updateCityState(city: City): void {
  const checked = city.barangays.filter((b) => b.checked).length;
  const total = city.barangays.length;
  if (checked === 0) {
    city.checked = false;
    city.indeterminate = false;
  } else if (checked === total) {
    city.checked = true;
    city.indeterminate = false;
  } else {
    city.checked = false;
    city.indeterminate = true;
  }
}

function updateRegionState(region: Region): void {
  const checked = region.cities.filter((c) => c.checked).length;
  const indet = region.cities.filter((c) => c.indeterminate).length;
  const total = region.cities.length;
  if (checked === 0 && indet === 0) {
    region.checked = false;
    region.indeterminate = false;
  } else if (checked === total && indet === 0) {
    region.checked = true;
    region.indeterminate = false;
  } else {
    region.checked = false;
    region.indeterminate = true;
  }
}

function onBarangayChange(region: Region, city: City): void {
  updateCityState(city);
  updateRegionState(region);
  emit("update:modelValue", [...props.modelValue]);
}

function toggleCityCheck(region: Region, city: City): void {
  const val = !city.checked;
  city.checked = val;
  city.indeterminate = false;
  city.barangays.forEach((b) => (b.checked = val));
  updateRegionState(region);
  emit("update:modelValue", [...props.modelValue]);
}

function toggleRegionCheck(region: Region): void {
  const val = !region.checked;
  region.checked = val;
  region.indeterminate = false;
  region.cities.forEach((city) => {
    city.checked = val;
    city.indeterminate = false;
    city.barangays.forEach((b) => (b.checked = val));
  });
  emit("update:modelValue", [...props.modelValue]);
}
</script>

<style scoped>
.location-tree {
  display: flex;
  flex-direction: column;
  gap: 6px;
}
.tree-node {
  border: 1px solid #f3f4f6;
  border-radius: 10px;
  overflow: hidden;
}
.region-node {
  border-color: #e5e7eb;
}
.city-node {
  margin-left: 28px;
  margin-top: 4px;
}
.barangay-node {
  margin-left: 28px;
  border: none;
}
.children {
  display: flex;
  flex-direction: column;
}
.children.cities {
  padding: 0 0 8px;
}
.children.barangays {
  padding: 0 0 4px;
}

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
.barangay-row {
  padding: 8px 14px;
  cursor: default;
}
.barangay-row:hover {
  background: transparent;
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
.region-badge {
  background: #fef3c7;
  color: #b45309;
}
.city-badge {
  background: #e0e7ff;
  color: #4338ca;
}
.barangay-badge {
  background: #f3f4f6;
  color: #6b7280;
}
</style>
