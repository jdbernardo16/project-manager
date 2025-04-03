<script setup lang="ts">
import { cn } from '@/lib/utils' // Assuming utils file exists for class merging
import type { HTMLAttributes } from 'vue'

// Basic variant prop for placeholder styling
const props = defineProps<{
  class?: HTMLAttributes['class']
  variant?: 'default' | 'secondary' | 'destructive' | 'outline'
}>()

// Placeholder styling based on variant
const variantClasses = {
  default: 'bg-primary text-primary-foreground',
  secondary: 'bg-secondary text-secondary-foreground',
  destructive: 'bg-destructive text-destructive-foreground',
  outline: 'border text-foreground',
}
</script>

<template>
  <span
    :class="cn(
      'inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2',
      variantClasses[props.variant ?? 'default'], // Apply variant class
      props.class, // Allow overriding classes
    )"
  >
    <slot />
  </span>
</template>