<script setup lang="ts">
defineProps<{
  class?: string;
}>();
</script>

<template>
  <th
    role="columnheader"
    :class="[
      'h-12 px-4 text-left align-middle font-medium text-muted-foreground [&:has([role=checkbox])]:pr-0',
      $props.class
    ]"
  >
    <slot />
  </th>
</template>