<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'; // Assuming shadcn-vue card components
import { Skeleton } from '@/components/ui/skeleton'; // For loading state
import type { FunctionalComponent, HTMLAttributes, VNodeProps } from 'vue';
import { defineAsyncComponent } from 'vue';

interface Props {
    title: string;
    value: number | string;
    icon: string; // Name of the lucide icon
    description?: string;
}

const props = defineProps<Props>();

// Dynamically import lucide icons
const LucideIcon = defineAsyncComponent({
    loader: () => import('lucide-vue-next').then((mod) => mod[props.icon as keyof typeof mod] as FunctionalComponent<HTMLAttributes & VNodeProps>),
    loadingComponent: Skeleton, // Optional: show skeleton while loading icon
    // errorComponent: ErrorComponent, // Optional: show error component if icon fails
    delay: 200,
    timeout: 3000,
});
</script>

<template>
    <Card>
        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle class="text-sm font-medium">
                {{ title }}
            </CardTitle>
            <component :is="LucideIcon" v-if="LucideIcon" class="h-4 w-4 text-muted-foreground" />
            <Skeleton v-else class="h-4 w-4" />
            <!-- Fallback if icon loading fails or is delayed -->
        </CardHeader>
        <CardContent>
            <div class="text-2xl font-bold">{{ value }}</div>
            <p v-if="description" class="text-xs text-muted-foreground">
                {{ description }}
            </p>
        </CardContent>
    </Card>
</template>
