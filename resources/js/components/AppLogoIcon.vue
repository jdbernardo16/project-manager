<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <div class="aspect-square overflow-hidden rounded">
        <img class="h-full w-full object-contain" src="/logo.svg" alt="Project Management" />
    </div>
</template>
