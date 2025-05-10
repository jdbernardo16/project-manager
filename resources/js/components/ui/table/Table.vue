<script setup lang="ts">
defineProps<{
  class?: string;
}>();
</script>

<template>
  <div class="w-full overflow-auto">
    <div role="table" :class="['w-full', $props.class]">
      <slot />
    </div>
  </div>
</template>