<script setup lang="ts">
import { computed } from 'vue'
import { cn } from '@/lib/utils'
import type { HTMLAttributes } from 'vue'

const props = withDefaults(defineProps<{
  class?: HTMLAttributes['class']
  modelValue?: number // Use modelValue for v-model compatibility
  max?: number
}>(), {
  modelValue: 0,
  max: 100,
})

const percentage = computed(() =>
  props.modelValue == null ? 0 : Math.max(0, Math.min(100, (props.modelValue / props.max) * 100)),
)
</script>

<template>
  <div
    :class="cn('relative h-4 w-full overflow-hidden rounded-full bg-secondary', props.class)"
    role="progressbar"
    :aria-valuenow="percentage"
    aria-valuemin="0"
    aria-valuemax="100"
  >
    <div
      class="h-full w-full flex-1 bg-primary transition-all"
      :style="{ transform: `translateX(-${100 - percentage}%)` }"
    />
  </div>
</template>