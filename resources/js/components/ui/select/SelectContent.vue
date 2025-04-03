<script setup lang="ts">
import { inject } from 'vue';
import { cn } from '@/lib/utils';
import type { Ref, InjectionKey } from 'vue';

const props = defineProps<{ class?: string }>();

const open = inject<Ref<boolean>>('selectOpen');
</script>

<template>
  <div
    v-if="open"
    :class="cn(
      'absolute z-50 mt-1 w-full rounded-md border bg-popover p-1 text-popover-foreground shadow-md animate-in fade-in-80', // Basic positioning and styling
      props.class
    )"
    role="listbox"
  >
    <slot />
  </div>
</template>