<script setup lang="ts">
import { provide, ref, watch } from 'vue';
import { useVModel } from '@vueuse/core';
import type { InjectionKey, Ref } from 'vue';

const props = defineProps<{
  modelValue?: string | number;
  defaultValue?: string | number;
}>();

const emits = defineEmits<{
  (e: 'update:modelValue', payload: string | number | undefined): void;
}>();

const modelValue = useVModel(props, 'modelValue', emits, {
  passive: true,
  defaultValue: props.defaultValue,
});

// Provide the modelValue and update function to children
provide<Ref<string | number | undefined>>('selectModelValue', modelValue);
provide<(value: string | number) => void>('updateSelectModelValue', (value) => {
  modelValue.value = value;
});

// Basic open state for placeholder
const open = ref(false);
provide<Ref<boolean>>('selectOpen', open);
provide<(value: boolean) => void>('updateSelectOpen', (value) => {
  open.value = value;
});

</script>

<template>
  <div class="relative">
    <slot />
  </div>
</template>