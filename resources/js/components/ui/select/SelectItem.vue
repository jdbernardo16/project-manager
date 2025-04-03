<script setup lang="ts">
import { inject, computed } from 'vue';
import { cn } from '@/lib/utils';
import type { Ref, InjectionKey } from 'vue';

const props = defineProps<{
  value: string | number;
  class?: string;
}>();

const modelValue = inject<Ref<string | number | undefined>>('selectModelValue');
const updateValue = inject<(value: string | number) => void>('updateSelectModelValue');
const updateOpen = inject<(value: boolean) => void>('updateSelectOpen');

const isSelected = computed(() => modelValue?.value === props.value);

const handleClick = () => {
  if (updateValue) {
    updateValue(props.value);
  }
  if (updateOpen) {
    updateOpen(false); // Close dropdown on selection
  }
};
</script>

<template>
  <div
    :class="cn(
      'relative flex w-full cursor-default select-none items-center rounded-sm py-1.5 pl-8 pr-2 text-sm outline-none focus:bg-accent focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50',
      { 'bg-accent': isSelected }, // Highlight selected item (basic)
      props.class
    )"
    @click="handleClick"
    role="option"
    :aria-selected="isSelected"
  >
    <!-- Basic checkmark placeholder for selected item -->
    <span v-if="isSelected" class="absolute left-2 flex h-3.5 w-3.5 items-center justify-center">
      ✓
    </span>
    <slot />
  </div>
</template>