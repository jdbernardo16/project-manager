<script setup lang="ts">
import { Avatar, AvatarFallback } from '@/components/ui/avatar'; // Assuming Avatar components exist
import Badge from '@/components/ui/badge/Badge.vue'; // Use default import for placeholder
import { Button } from '@/components/ui/button'; // Assuming Button component exists
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { getInitials } from '@/composables/useInitials'; // Import the function directly
import { Link, router } from '@inertiajs/vue3'; // Import router for actions
import type { PropType } from 'vue';

interface Resource {
    id: number;
    name: string;
    capacity: number;
    availability_status: 'available' | 'assigned' | 'unavailable';
    current_project_title: string;
    user_id: number;
}

defineProps({
    resources: {
        type: Array as PropType<Resource[]>,
        required: true,
    },
});

const getStatusVariant = (status: Resource['availability_status']): 'default' | 'secondary' | 'destructive' | 'outline' => {
    switch (status) {
        case 'available':
            return 'default'; // Use primary color for available
        case 'assigned':
            return 'secondary';
        case 'unavailable':
            return 'outline';
        default:
            return 'secondary';
    }
};

const getStatusText = (status: Resource['availability_status']): string => {
    switch (status) {
        case 'available':
            return 'Available';
        case 'assigned':
            return 'Assigned';
        case 'unavailable':
            return 'Unavailable';
        default:
            return status;
    }
};

// Function to handle toggling availability
const toggleAvailability = (resourceId: number) => {
    router.put(
        route('resources.toggle-availability', resourceId),
        {},
        {
            preserveScroll: true, // Keep scroll position after update
            onSuccess: () => {
                // Optional: Show a success notification
            },
            onError: (errors) => {
                // Optional: Show error notification
                console.error('Error toggling availability:', errors);
            },
        },
    );
};
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle>Resource Availability</CardTitle>
        </CardHeader>
        <CardContent>
            <div v-if="resources.length > 0" class="space-y-4">
                <div v-for="resource in resources" :key="resource.id" class="flex items-center justify-between space-x-4">
                    <div class="flex items-center space-x-3">
                        <Avatar>
                            <!-- Placeholder for actual image if available -->
                            <AvatarFallback>{{ getInitials(resource.name) }}</AvatarFallback>
                        </Avatar>
                        <div class="flex-1 space-y-1">
                            <Link :href="route('resources.show', resource.id)" class="text-sm font-medium leading-none hover:underline">
                                {{ resource.name }}
                            </Link>
                            <p class="text-xs text-muted-foreground">
                                Project: {{ resource.current_project_title }} | Capacity: {{ resource.capacity }}h/day
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <Badge :variant="getStatusVariant(resource.availability_status)">
                            {{ getStatusText(resource.availability_status) }}
                        </Badge>
                        <!-- Only show toggle button if not assigned? Or always show? Let's always show for now -->
                        <Button
                            v-if="resource.availability_status !== 'assigned'"
                            @click="toggleAvailability(resource.id)"
                            size="sm"
                            variant="outline"
                            class="text-xs"
                        >
                            {{ resource.availability_status === 'available' ? 'Set Unavailable' : 'Set Available' }}
                        </Button>
                        <Button v-else size="sm" variant="outline" class="text-xs" disabled> Assigned </Button>
                    </div>
                </div>
            </div>
            <div v-else class="text-sm italic text-muted-foreground">No resources found.</div>
        </CardContent>
    </Card>
</template>
