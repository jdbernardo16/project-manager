<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import Badge from '@/components/ui/badge/Badge.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import Progress from '@/components/ui/progress/Progress.vue'; // Use default import
import { Toaster } from '@/components/ui/toast';
import { useToast } from '@/components/ui/toast/use-toast';
import AppLayout from '@/layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { Calendar, Clock, Edit, Hourglass, Trash2, Users } from 'lucide-vue-next'; // Import icons
import { computed, ref, type PropType } from 'vue';

// Interface for assigned resource details within the project prop
interface AssignedResource {
    id: number;
    name: string;
    capacity: number;
    assigned_hours: number;
    start_date: string | null;
    end_date: string | null;
}

// Interface for the main project data prop
interface ProjectData {
    id: number;
    title: string;
    description: string | null;
    estimate_hours: number;
    hours_remaining: number | null; // Added
    deadline: string | null;
    status: 'upcoming' | 'ongoing' | 'completed';
    progress: number; // Added
    is_overdue: boolean; // Added
    created_at: string;
    updated_at: string;
    estimated_end_date: string | null; // Calculated in controller
    resources: AssignedResource[];
}

const props = defineProps({
    project: {
        type: Object as PropType<ProjectData>,
        required: true,
    },
});

const { toast } = useToast();

const isDeleteDialogOpen = ref(false);

// Computed property for dynamic progress bar class
const progressBarClass = computed(() => {
    if (props.project.is_overdue) {
        return 'bg-red-500'; // Red if overdue
    }
    const progress = props.project.progress;
    if (progress < 50) {
        return 'bg-red-500'; // Red if below 50%
    } else if (progress < 90) {
        return 'bg-yellow-500'; // Yellow if 50% to 89%
    } else {
        return 'bg-green-500'; // Green if 90% or more
    }
});

const getStatusVariant = (status: ProjectData['status']): 'default' | 'secondary' | 'destructive' | 'outline' => {
    if (props.project.is_overdue) {
        return 'destructive'; // Destructive if overdue
    }
    switch (status) {
        case 'ongoing':
            return 'default';
        case 'upcoming':
            return 'secondary';
        case 'completed':
            return 'outline'; // Maybe a success variant if available
        default:
            return 'secondary';
    }
};

const getStatusText = (status: ProjectData['status']): string => {
    if (props.project.is_overdue) {
        return 'Overdue';
    }
    return status.charAt(0).toUpperCase() + status.slice(1);
};

// Function to open delete confirmation dialog
const confirmDeleteProject = () => {
    isDeleteDialogOpen.value = true;
};

// Function to execute project deletion
const executeDeleteProject = () => {
    router.delete(route('projects.destroy', props.project.id), {
        preserveScroll: true,
        onSuccess: () => {
            isDeleteDialogOpen.value = false;
            // Redirect to projects index or show success toast.
            // For now, just a toast and close. User will be redirected if controller does so.
            toast({
                title: 'Project Deleted',
                description: `Project "${props.project.title}" has been deleted.`,
            });
            // router.visit(route('projects.index')); // Optional: redirect after delete
        },
        onError: (errors) => {
            isDeleteDialogOpen.value = false;
            console.error('Project deletion failed:', errors);
            toast({
                title: 'Error Deleting Project',
                description: errors.error || 'Failed to delete project. Check console for details.',
                variant: 'destructive',
            });
        },
    });
};
</script>

<template>
    <AppLayout :title="`Project: ${project.title}`">
        <Toaster />
        <template #header>
            <div class="flex items-center justify-between">
                <Heading :title="project.title" :description="`Details for project #${project.id}`" />
                <div class="flex items-center space-x-2">
                    <Link :href="route('projects.edit', project.id)">
                        <Button variant="outline" size="sm"> <Edit class="mr-1 h-4 w-4" /> Edit </Button>
                    </Link>
                    <Button @click="confirmDeleteProject" variant="destructive" size="sm"> <Trash2 class="mr-1 h-4 w-4" /> Delete </Button>
                </div>
            </div>
        </template>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Left Column: Project Details -->
            <div class="space-y-6 lg:col-span-2">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle>Project Overview</CardTitle>
                        <Badge :variant="getStatusVariant(project.status)">
                            {{ getStatusText(project.status) }}
                        </Badge>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <p v-if="project.description" class="text-muted-foreground">{{ project.description }}</p>
                        <p v-else class="italic text-muted-foreground">No description provided.</p>

                        <div class="grid grid-cols-1 gap-4 text-sm sm:grid-cols-2">
                            <div class="flex items-center">
                                <Hourglass class="mr-2 h-4 w-4 text-muted-foreground" />
                                <span
                                    >Est. Hours: <span class="font-medium">{{ project.estimate_hours }}</span></span
                                >
                            </div>
                            <div class="flex items-center">
                                <Clock class="mr-2 h-4 w-4 text-muted-foreground" />
                                <span
                                    >Hours Remaining: <span class="font-medium">{{ project.hours_remaining ?? 'N/A' }}</span></span
                                >
                            </div>
                            <div class="flex items-center">
                                <Calendar class="mr-2 h-4 w-4 text-muted-foreground" />
                                <span
                                    >Deadline: <span class="font-medium">{{ project.deadline || 'N/A' }}</span></span
                                >
                            </div>
                            <div class="flex items-center">
                                <Clock class="mr-2 h-4 w-4 text-muted-foreground" />
                                <span
                                    >Est. End Date: <span class="font-medium">{{ project.estimated_end_date || 'N/A' }}</span></span
                                >
                            </div>
                            <div class="flex items-center">
                                <Users class="mr-2 h-4 w-4 text-muted-foreground" />
                                <span
                                    >Resources Assigned: <span class="font-medium">{{ project.resources.length }}</span></span
                                >
                            </div>
                        </div>
                        <div>
                            <div class="mb-1 flex justify-between text-sm">
                                <Label>Progress</Label>
                                <span :class="['font-semibold', project.is_overdue ? 'text-red-600' : 'text-muted-foreground']">
                                    {{ project.progress }}%
                                    <span v-if="project.is_overdue">(Overdue)</span>
                                </span>
                            </div>
                            <Progress :model-value="project.progress" class="h-2" :indicator-class="progressBarClass" />
                        </div>
                    </CardContent>
                    <CardFooter class="text-xs text-muted-foreground">
                        Created: {{ project.created_at }} | Last Updated: {{ project.updated_at }}
                    </CardFooter>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Assigned Resources</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div v-if="project.resources.length > 0" class="space-y-3">
                            <div
                                v-for="resource in project.resources"
                                :key="resource.id"
                                class="flex items-center justify-between rounded-md border p-3"
                            >
                                <div>
                                    <Link :href="route('resources.show', resource.id)" class="font-medium hover:underline">{{ resource.name }}</Link>
                                    <p class="text-xs text-muted-foreground">Capacity: {{ resource.capacity }}h/day</p>
                                </div>
                                <div class="text-right text-sm">
                                    <p>Assigned: {{ resource.assigned_hours }} hrs</p>
                                    <p class="text-xs text-muted-foreground">
                                        {{ resource.start_date || 'N/A' }} - {{ resource.end_date || 'Ongoing' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <p v-else class="text-sm italic text-muted-foreground">No resources assigned to this project.</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Right Column: Actions / Related Info (Optional) -->
            <div class="space-y-6 lg:col-span-1">
                <Card>
                    <CardHeader>
                        <CardTitle>Actions</CardTitle>
                    </CardHeader>
                    <CardContent class="flex flex-col space-y-2">
                        <Link :href="route('projects.calendar', project.id)">
                            <Button variant="outline" class="w-full justify-start"> <Calendar class="mr-2 h-4 w-4" /> View Calendar/Timeline </Button>
                        </Link>
                        <!-- Add other actions like 'Mark as Complete', 'Add Task', etc. -->
                    </CardContent>
                </Card>
                <!-- Placeholder for related tasks or comments -->
            </div>
        </div>

        <!-- Delete Confirmation Dialog -->
        <Dialog v-model:open="isDeleteDialogOpen">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Confirm Deletion</DialogTitle>
                    <DialogDescription>
                        Are you sure you want to delete the project "{{ project.title }}"? This action cannot be undone.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" @click="isDeleteDialogOpen = false"> Cancel </Button>
                    <Button variant="destructive" @click="executeDeleteProject"> Delete </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
