<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import Badge from '@/components/ui/badge/Badge.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { getInitials } from '@/composables/useInitials'; // Import getInitials
import AppLayout from '@/layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { Edit, Eye, Trash2, UserPlus } from 'lucide-vue-next'; // Import icons
import type { PropType } from 'vue';

// Interface matching data from ResourceController@index
interface Resource {
    id: number;
    user_id: number;
    name: string;
    email: string;
    capacity: number;
    availability_status: 'available' | 'assigned' | 'unavailable';
    notes: string | null;
    created_at: string;
    current_projects_count: number;
    current_projects_list: string;
}

const props = defineProps({
    resources: {
        type: Array as PropType<Resource[]>,
        required: true,
    },
});

const getStatusVariant = (status: Resource['availability_status']): 'default' | 'secondary' | 'destructive' | 'outline' => {
    switch (status) {
        case 'available':
            return 'default';
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

// Function to confirm and trigger resource deletion
const confirmDeleteResource = (resource: Resource) => {
    if (confirm(`Are you sure you want to delete the resource "${resource.name}"? This might also delete the associated user.`)) {
        router.delete(route('resources.destroy', resource.id), {
            preserveScroll: true,
            onError: (errors) => {
                console.error('Resource deletion failed:', errors);
                // Show error notification from controller flash message or default
                alert(errors.error || 'Failed to delete resource. It might be assigned to active projects.');
            },
        });
    }
};

// Function to handle toggling availability
const toggleAvailability = (resourceId: number) => {
    router.put(
        route('resources.toggle-availability', resourceId),
        {},
        {
            preserveScroll: true,
            onError: (errors) => {
                console.error('Error toggling availability:', errors);
                alert('Failed to toggle availability.');
            },
        },
    );
};
</script>

<template>
    <AppLayout title="Resources">
        <template #header>
            <div class="flex items-center justify-between">
                <Heading title="Resources" description="Manage all team resources." />
                <Link :href="route('resources.create')">
                    <Button> <UserPlus class="mr-2 h-4 w-4" /> Add Resource </Button>
                </Link>
            </div>
        </template>

        <Card>
            <CardHeader>
                <CardTitle>Resource List</CardTitle>
                <CardDescription>Overview of all available resources and their status.</CardDescription>
            </CardHeader>
            <CardContent>
                <div v-if="resources.length > 0" class="space-y-4">
                    <div
                        v-for="resource in resources"
                        :key="resource.id"
                        class="flex flex-col gap-3 rounded-md border p-4 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <div class="flex items-center space-x-4">
                            <Avatar>
                                <!-- <AvatarImage src="/path/to/image.jpg" :alt="resource.name" /> -->
                                <AvatarFallback>{{ getInitials(resource.name) }}</AvatarFallback>
                            </Avatar>
                            <div class="flex-1">
                                <Link :href="route('resources.show', resource.id)" class="text-base font-semibold hover:underline">
                                    {{ resource.name }}
                                </Link>
                                <p class="text-sm text-muted-foreground">{{ resource.email }}</p>
                                <p class="text-xs text-muted-foreground">Capacity: {{ resource.capacity }}h/day</p>
                            </div>
                        </div>

                        <div class="flex flex-col items-start gap-2 sm:items-end">
                            <Badge :variant="getStatusVariant(resource.availability_status)" class="mb-1 sm:mb-0">
                                {{ getStatusText(resource.availability_status) }}
                            </Badge>
                            <p class="truncate text-xs text-muted-foreground" :title="resource.current_projects_list || 'No active projects'">
                                Projects: {{ resource.current_projects_count }}
                                {{ resource.current_projects_count > 0 ? `(${resource.current_projects_list})` : '' }}
                            </p>
                        </div>

                        <div class="flex items-center justify-start space-x-2 border-t pt-2 sm:justify-end sm:border-t-0 sm:pt-0">
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
                            <Link :href="route('resources.show', resource.id)">
                                <Button variant="ghost" size="icon">
                                    <Eye class="h-4 w-4" />
                                </Button>
                            </Link>
                            <Link :href="route('resources.edit', resource.id)">
                                <Button variant="ghost" size="icon">
                                    <Edit class="h-4 w-4" />
                                </Button>
                            </Link>
                            <Button
                                @click="confirmDeleteResource(resource)"
                                variant="ghost"
                                size="icon"
                                class="text-destructive hover:text-destructive"
                            >
                                <Trash2 class="h-4 w-4" />
                            </Button>
                        </div>
                    </div>
                </div>
                <div v-else class="py-12 text-center">
                    <p class="text-muted-foreground">No resources found.</p>
                    <Link :href="route('resources.create')" class="mt-4 inline-block">
                        <Button>Add Your First Resource</Button>
                    </Link>
                </div>
            </CardContent>
        </Card>
    </AppLayout>
</template>
