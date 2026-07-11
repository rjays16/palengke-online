<template>
  <div :class="`${type}-inputs`">
    <input
      v-for="(digit, index) in digits"
      :key="index"
      :ref="(el: any) => (inputRefs[index] = el)"
      v-model="digits[index]"
      type="tel"
      maxlength="1"
      :class="[`${type}-box`, { filled: digits[index], error }]"
      @input="handleInput(index, $event)"
      @keydown="handleKeydown(index, $event)"
      @paste="handlePaste"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';

const props = withDefaults(
  defineProps<{
    length?: number;
    type?: 'otp' | 'pin';
    modelValue?: string;
    autoFocus?: boolean;
    error?: boolean;
  }>(),
  {
    length: 6,
    type: 'otp',
    modelValue: '',
    autoFocus: true,
    error: false,
  },
);

const emit = defineEmits<{
  (e: 'update:modelValue', value: string): void;
  (e: 'complete', value: string): void;
}>();

const digits = ref<string[]>(Array(props.length).fill(''));
const inputRefs = ref<any[]>([]);

const code = computed(() => digits.value.join(''));

const isComplete = computed(() => digits.value.every((d) => d !== ''));

watch(code, (val) => {
  emit('update:modelValue', val);
  if (isComplete.value) {
    emit('complete', val);
  }
});

watch(
  () => props.modelValue,
  (val) => {
    if (val === code.value) return;
    const newDigits = val.split('').slice(0, props.length);
    while (newDigits.length < props.length) newDigits.push('');
    digits.value = newDigits;
  },
);

const handleInput = (index: number, event: Event) => {
  const value = (event.target as HTMLInputElement).value;
  if (!/^\d*$/.test(value)) {
    digits.value[index] = '';
    return;
  }
  if (value && index < props.length - 1) {
    inputRefs.value[index + 1]?.focus();
  }
};

const handleKeydown = (index: number, event: KeyboardEvent) => {
  if (event.key === 'Backspace' && !digits.value[index] && index > 0) {
    inputRefs.value[index - 1]?.focus();
  }
  if (event.key === 'ArrowLeft' && index > 0) {
    inputRefs.value[index - 1]?.focus();
  }
  if (event.key === 'ArrowRight' && index < props.length - 1) {
    inputRefs.value[index + 1]?.focus();
  }
};

const handlePaste = (event: ClipboardEvent) => {
  event.preventDefault();
  const pastedData = event.clipboardData?.getData('text').replace(/\D/g, '').slice(0, props.length) || '';
  if (!pastedData) return;

  const pastedDigits = pastedData.split('');
  pastedDigits.forEach((digit, index) => {
    if (index < props.length) {
      digits.value[index] = digit;
    }
  });

  const nextEmpty = pastedDigits.length < props.length ? pastedDigits.length : props.length - 1;
  inputRefs.value[nextEmpty]?.focus();
};

const reset = () => {
  digits.value = Array(props.length).fill('');
  if (props.autoFocus) {
    setTimeout(() => inputRefs.value[0]?.focus(), 300);
  }
};

const focus = () => {
  setTimeout(() => inputRefs.value[0]?.focus(), 300);
};

onMounted(() => {
  if (props.autoFocus) {
    setTimeout(() => inputRefs.value[0]?.focus(), 300);
  }
});

defineExpose({ reset, focus, isComplete, code });
</script>
