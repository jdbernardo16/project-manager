<script setup lang="ts">
defineProps<{
  class?: string;
}>();
</script>

<template>
  <tbody role="rowgroup" :class="['[&_tr:last-child]:border-0 w-full', $props.class]">
    <slot />
  </tbody>
</template>