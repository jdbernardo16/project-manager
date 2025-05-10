<script setup lang="ts">
defineProps<{
  class?: string;
}>();
</script>

<template>
  <tr
    role="row"
    :class="['border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted', $props.class]"
  >
    <slot />
  </tr>
</template>