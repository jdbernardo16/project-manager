<script setup lang="ts">
import { inject } from 'vue';
import { cn } from '@/lib/utils';
import type { Ref, InjectionKey } from 'vue';
import { ChevronDown } from 'lucide-vue-next'; // Assuming icon exists

const props = defineProps<{ class?: string; id?: string }>();

const open = inject<Ref<boolean>>('selectOpen');
const updateOpen = inject<(value: boolean) => void>('updateSelectOpen');

const toggleOpen = () => {
  if (updateOpen) {
    updateOpen(!open?.value);
  }
};
</script>

<template>
  <button
    type="button"
    :id="props.id"
    :class="cn(
      'flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50',
      props.class
    )"
    @click="toggleOpen"
    aria-haspopup="listbox"
    :aria-expanded="open"
  >
    <slot /> <!-- This will contain SelectValue -->
    <ChevronDown class="ml-2 h-4 w-4 opacity-50" />
  </button>
</template>