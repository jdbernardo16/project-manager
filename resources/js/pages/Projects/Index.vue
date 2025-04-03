<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import Badge from '@/components/ui/badge/Badge.vue'; // Use placeholder Badge
import { Button } from '@/components/ui/button'; // Assuming Button component
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card'; // Assuming Card components
import Progress from '@/components/ui/progress/Progress.vue'; // Use default import for placeholder
import AppLayout from '@/layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import type { PropType } from 'vue';

// Interface matching the data structure from ProjectController@index
interface Project {
    id: number;
    title: string;
    description: string | null;
    estimate_hours: number;
    deadline: string | null;
    status: 'upcoming' | 'ongoing' | 'completed';
    created_at: string;
    resources_count: number;
    progress: number;
    deadline_near: boolean;
    assigned_resources: string;
}

const props = defineProps({
    projects: {
        type: Array as PropType<Project[]>,
        required: true,
    },
});

const getStatusVariant = (status: Project['status']): 'default' | 'secondary' | 'destructive' | 'outline' => {
    switch (status) {
        case 'ongoing':
            return 'default'; // Blue/Primary
        case 'upcoming':
            return 'secondary'; // Gray
        case 'completed':
            return 'outline'; // Greenish/Outline?
        default:
            return 'secondary';
    }
};

const getDeadlineVariant = (deadlineNear: boolean, deadline: string | null): 'destructive' | 'outline' | 'secondary' => {
    if (!deadline) return 'secondary';
    if (deadlineNear) return 'destructive'; // Red if near/past due
    return 'outline'; // Normal outline otherwise
};

const getDeadlineText = (deadline: string | null): string => {
    return deadline ? `Due: ${deadline}` : 'No Deadline';
};

const getStatusText = (status: Project['status']): string => {
    return status.charAt(0).toUpperCase() + status.slice(1); // Capitalize
};
</script>

<template>
    <AppLayout title="Projects">
        <template #header>
            <div class="flex items-center justify-between">
                <Heading title="Projects" description="Manage all your projects." />
                <Link :href="route('projects.create')">
                    <Button>Create Project</Button>
                </Link>
            </div>
        </template>

        <div v-if="projects.length > 0" class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            <Card v-for="project in projects" :key="project.id">
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <CardTitle class="text-lg">{{ project.title }}</CardTitle>
                        <Badge :variant="getStatusVariant(project.status)">
                            {{ getStatusText(project.status) }}
                        </Badge>
                    </div>
                    <CardDescription v-if="project.description" class="line-clamp-2 pt-1">
                        {{ project.description }}
                    </CardDescription>
                    <CardDescription v-else class="pt-1 italic"> No description provided. </CardDescription>
                </CardHeader>
                <CardContent class="space-y-3">
                    <div class="text-sm text-muted-foreground">
                        <span class="font-medium">Resources:</span> {{ project.resources_count }}
                        <span v-if="project.assigned_resources"> ({{ project.assigned_resources }})</span>
                    </div>
                    <div class="text-sm text-muted-foreground"><span class="font-medium">Est. Hours:</span> {{ project.estimate_hours }}</div>
                    <div>
                        <span class="text-sm font-medium text-muted-foreground">Progress:</span>
                        <Progress :model-value="project.progress" class="mt-1 h-2" />
                    </div>
                </CardContent>
                <CardFooter class="flex items-center justify-between">
                    <Badge :variant="getDeadlineVariant(project.deadline_near, project.deadline)" class="text-xs">
                        {{ getDeadlineText(project.deadline) }}
                    </Badge>
                    <Link :href="route('projects.show', project.id)">
                        <Button variant="outline" size="sm">View Details</Button>
                    </Link>
                </CardFooter>
            </Card>
        </div>
        <div v-else class="py-12 text-center">
            <p class="text-muted-foreground">No projects found.</p>
            <Link :href="route('projects.create')" class="mt-4 inline-block">
                <Button>Create Your First Project</Button>
            </Link>
        </div>
    </AppLayout>
</template>
