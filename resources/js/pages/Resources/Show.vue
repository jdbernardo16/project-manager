<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import Badge from '@/components/ui/badge/Badge.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Toaster } from '@/components/ui/toast';
import { useToast } from '@/components/ui/toast/use-toast';
import { getInitials } from '@/composables/useInitials';
import AppLayout from '@/layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { BarChart, Briefcase, CalendarDays, Clock, Edit, Mail, Trash2, User } from 'lucide-vue-next'; // Import icons
import type { PropType } from 'vue';

// Interface for project details within the resource prop
interface AssignedProject {
    id: number;
    title: string;
    status: string;
    deadline: string | null;
    assigned_hours_on_project: number;
    start_date_on_project: string | null;
    end_date_on_project: string | null;
}

// Interface for the main resource data prop from ResourceController@show
interface ResourceData {
    id: number;
    user_id: number;
    name: string;
    email: string;
    role: string;
    capacity: number;
    availability_status: 'available' | 'assigned' | 'unavailable';
    notes: string | null;
    created_at: string;
    updated_at: string;
    utilization_percentage: number; // Placeholder from controller
    projects: AssignedProject[];
}

const props = defineProps({
    resource: {
        type: Object as PropType<ResourceData>,
        required: true,
    },
});

const { toast } = useToast();

const getStatusVariant = (status: ResourceData['availability_status']): 'default' | 'secondary' | 'destructive' | 'outline' => {
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

const getStatusText = (status: ResourceData['availability_status']): string => {
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
const confirmDeleteResource = () => {
    if (confirm(`Are you sure you want to delete the resource "${props.resource.name}"? This might also delete the associated user.`)) {
        router.delete(route('resources.destroy', props.resource.id), {
            preserveScroll: true,
            onError: (errors) => {
                console.error('Resource deletion failed:', errors);
                toast({
                    title: 'Error Deleting Resource',
                    description: errors.error || 'Failed to delete resource. It might be assigned to active projects.',
                    variant: 'destructive',
                });
            },
        });
    }
};

// Function to handle toggling availability
const toggleAvailability = () => {
    router.put(
        route('resources.toggle-availability', props.resource.id),
        {},
        {
            preserveScroll: true,
            onError: (errors) => {
                console.error('Error toggling availability:', errors);
                toast({
                    title: 'Error',
                    description: 'Failed to toggle availability.',
                    variant: 'destructive',
                });
            },
        },
    );
};
</script>

<template>
    <AppLayout :title="`Resource: ${resource.name}`">
        <Toaster />
        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <Avatar class="h-12 w-12">
                        <!-- <AvatarImage src="/path/to/image.jpg" :alt="resource.name" /> -->
                        <AvatarFallback class="text-xl">{{ getInitials(resource.name) }}</AvatarFallback>
                    </Avatar>
                    <Heading :title="resource.name" :description="`Details for resource #${resource.id}`" />
                </div>
                <div class="flex items-center space-x-2">
                    <Link :href="route('resources.edit', resource.id)">
                        <Button variant="outline" size="sm"> <Edit class="mr-1 h-4 w-4" /> Edit </Button>
                    </Link>
                    <Button @click="confirmDeleteResource" variant="destructive" size="sm"> <Trash2 class="mr-1 h-4 w-4" /> Delete </Button>
                </div>
            </div>
        </template>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Left Column: Resource Details -->
            <div class="space-y-6 lg:col-span-2">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle>Resource Information</CardTitle>
                        <Badge :variant="getStatusVariant(resource.availability_status)">
                            {{ getStatusText(resource.availability_status) }}
                        </Badge>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid grid-cols-1 gap-4 text-sm sm:grid-cols-2">
                            <div class="flex items-center">
                                <Mail class="mr-2 h-4 w-4 text-muted-foreground" />
                                <span
                                    >Email: <span class="font-medium">{{ resource.email }}</span></span
                                >
                            </div>
                            <div class="flex items-center">
                                <User class="mr-2 h-4 w-4 text-muted-foreground" />
                                <span
                                    >Role: <span class="font-medium">{{ resource.role }}</span></span
                                >
                            </div>
                            <div class="flex items-center">
                                <Clock class="mr-2 h-4 w-4 text-muted-foreground" />
                                <span
                                    >Capacity: <span class="font-medium">{{ resource.capacity }} hours/day</span></span
                                >
                            </div>
                            <div class="flex items-center">
                                <BarChart class="mr-2 h-4 w-4 text-muted-foreground" />
                                <span
                                    >Utilization: <span class="font-medium">{{ resource.utilization_percentage }}%</span> (Placeholder)</span
                                >
                            </div>
                        </div>
                        <div v-if="resource.notes">
                            <h4 class="mb-1 text-sm font-medium">Notes:</h4>
                            <p class="whitespace-pre-wrap text-sm text-muted-foreground">{{ resource.notes }}</p>
                        </div>
                    </CardContent>
                    <CardFooter class="text-xs text-muted-foreground">
                        Member Since: {{ resource.created_at }} | Last Updated: {{ resource.updated_at }}
                    </CardFooter>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Project Assignments</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div v-if="resource.projects.length > 0" class="space-y-3">
                            <div
                                v-for="project in resource.projects"
                                :key="project.id"
                                class="flex items-center justify-between rounded-md border p-3"
                            >
                                <div>
                                    <Link :href="route('projects.show', project.id)" class="font-medium hover:underline">{{ project.title }}</Link>
                                    <p class="text-xs text-muted-foreground">Status: {{ project.status }}</p>
                                </div>
                                <div class="text-right text-sm">
                                    <p>Assigned: {{ project.assigned_hours_on_project }} hrs</p>
                                    <p class="text-xs text-muted-foreground">
                                        {{ project.start_date_on_project || 'N/A' }} - {{ project.end_date_on_project || 'Ongoing' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <p v-else class="text-sm italic text-muted-foreground">Currently not assigned to any projects.</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Right Column: Actions -->
            <div class="space-y-6 lg:col-span-1">
                <Card>
                    <CardHeader>
                        <CardTitle>Actions</CardTitle>
                    </CardHeader>
                    <CardContent class="flex flex-col space-y-2">
                        <Button
                            v-if="resource.availability_status !== 'assigned'"
                            @click="toggleAvailability"
                            variant="outline"
                            class="w-full justify-start"
                        >
                            <CalendarDays class="mr-2 h-4 w-4" />
                            {{ resource.availability_status === 'available' ? 'Set Unavailable' : 'Set Available' }}
                        </Button>
                        <Button v-else variant="outline" class="w-full justify-start" disabled>
                            <Briefcase class="mr-2 h-4 w-4" />
                            Currently Assigned
                        </Button>
                        <!-- Add other actions like 'View Timesheet', etc. -->
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
