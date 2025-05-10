<script setup lang="ts">
defineProps<{
  class?: string;
}>();
</script>

<template>
  <thead role="rowgroup" :class="['[&_tr]:border-b w-full', $props.class]">
    <slot />
  </thead>
</template>