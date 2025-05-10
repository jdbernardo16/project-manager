<script setup lang="ts">
defineProps<{
  class?: string;
}>();
</script>

<template>
  <td
    role="cell"
    :class="['p-4 align-middle [&:has([role=checkbox])]:pr-0', $props.class]"
  >
    <slot />
  </td>
</template>