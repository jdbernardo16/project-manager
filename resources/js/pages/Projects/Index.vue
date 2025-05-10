<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import Badge from '@/components/ui/badge/Badge.vue'; // Use placeholder Badge
import { Button } from '@/components/ui/button'; // Assuming Button component
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card'; // Assuming Card components
import Progress from '@/components/ui/progress/Progress.vue'; // Use default import for placeholder
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import type { PropType } from 'vue';
import { computed, ref } from 'vue';

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
    deadline_approaching: boolean;
    assigned_resources: string;
}

const props = defineProps({
    projects: {
        type: Array as PropType<Project[]>,
        required: true,
    },
});

const viewMode = ref<'grid' | 'table'>('grid');
const sortBy = ref<keyof Project>('title');
const sortDirection = ref<'asc' | 'desc'>('asc');

const toggleViewMode = () => {
    viewMode.value = viewMode.value === 'grid' ? 'table' : 'grid';
};

const toggleSort = (field: keyof Project) => {
    if (sortBy.value === field) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortBy.value = field;
        sortDirection.value = 'asc';
    }
};

const sortedProjects = computed(() => {
    return [...props.projects].sort((a, b) => {
        const aValue = a[sortBy.value];
        const bValue = b[sortBy.value];

        // Handle null/undefined values
        if (aValue == null) return sortDirection.value === 'asc' ? 1 : -1;
        if (bValue == null) return sortDirection.value === 'asc' ? -1 : 1;

        // Numeric comparison
        if (typeof aValue === 'number' && typeof bValue === 'number') {
            return sortDirection.value === 'asc' ? aValue - bValue : bValue - aValue;
        }

        // Date comparison for deadline field
        if (sortBy.value === 'deadline') {
            const dateA = typeof aValue === 'string' ? new Date(aValue) : null;
            const dateB = typeof bValue === 'string' ? new Date(bValue) : null;

            if (!dateA && !dateB) return 0;
            if (!dateA) return sortDirection.value === 'asc' ? 1 : -1;
            if (!dateB) return sortDirection.value === 'asc' ? -1 : 1;

            return sortDirection.value === 'asc' ? dateA.getTime() - dateB.getTime() : dateB.getTime() - dateA.getTime();
        }

        // String comparison for other fields
        return sortDirection.value === 'asc' ? String(aValue).localeCompare(String(bValue)) : String(bValue).localeCompare(String(aValue));
    });
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

const getDeadlineVariant = (
    deadlineNear: boolean, // Keep for compatibility but logic uses deadline string
    deadline: string | null,
    deadlineApproaching: boolean, // Keep for compatibility but logic uses deadline string
): 'destructive' | 'warning' | 'outline' | 'secondary' => {
    if (!deadline) return 'secondary';

    const today = new Date();
    const deadlineDate = new Date(deadline);

    // Set time to midnight for accurate day comparison
    today.setHours(0, 0, 0, 0);
    deadlineDate.setHours(0, 0, 0, 0);

    if (deadlineDate < today) {
        // Past deadline (red/destructive)
        return 'destructive';
    } else {
        // Calculate difference in days for future deadlines
        const diffTime = deadlineDate.getTime() - today.getTime();
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

        if (diffDays <= 7) {
            // Within 7 days (yellow/warning)
            return 'warning';
        } else {
            // More than 7 days away (outline)
            return 'outline';
        }
    }
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
                <div class="flex items-center gap-2">
                    <Button variant="outline" size="sm" @click="toggleViewMode">
                        {{ viewMode === 'grid' ? 'Table View' : 'Grid View' }}
                    </Button>
                    <Link :href="route('projects.create')">
                        <Button>Create Project</Button>
                    </Link>
                </div>
            </div>
        </template>

        <div v-if="projects.length > 0">
            <div v-if="viewMode === 'grid'" class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                <Card v-for="project in sortedProjects" :key="project.id">
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
                        <Badge
                            :variant="getDeadlineVariant(project.deadline_near, project.deadline, project.deadline_approaching)"
                            class="text-xs"
                            ref="deadlineBadge"
                        >
                            {{ getDeadlineText(project.deadline) }}
                        </Badge>
                        <Link :href="route('projects.show', project.id)">
                            <Button variant="outline" size="sm">View Details</Button>
                        </Link>
                    </CardFooter>
                </Card>
            </div>

            <div v-else class="overflow-hidden rounded-lg border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="cursor-pointer hover:bg-accent" @click="toggleSort('title')">
                                Title
                                <span v-if="sortBy === 'title'">
                                    {{ sortDirection === 'asc' ? '↑' : '↓' }}
                                </span>
                            </TableHead>
                            <TableHead class="cursor-pointer hover:bg-accent" @click="toggleSort('status')">
                                Status
                                <span v-if="sortBy === 'status'">
                                    {{ sortDirection === 'asc' ? '↑' : '↓' }}
                                </span>
                            </TableHead>
                            <TableHead class="cursor-pointer hover:bg-accent" @click="toggleSort('resources_count')">
                                Resources
                                <span v-if="sortBy === 'resources_count'">
                                    {{ sortDirection === 'asc' ? '↑' : '↓' }}
                                </span>
                            </TableHead>
                            <TableHead class="cursor-pointer hover:bg-accent" @click="toggleSort('estimate_hours')">
                                Est. Hours
                                <span v-if="sortBy === 'estimate_hours'">
                                    {{ sortDirection === 'asc' ? '↑' : '↓' }}
                                </span>
                            </TableHead>
                            <TableHead class="cursor-pointer hover:bg-accent" @click="toggleSort('progress')">
                                Progress
                                <span v-if="sortBy === 'progress'">
                                    {{ sortDirection === 'asc' ? '↑' : '↓' }}
                                </span>
                            </TableHead>
                            <TableHead class="cursor-pointer hover:bg-accent" @click="toggleSort('deadline')">
                                Deadline
                                <span v-if="sortBy === 'deadline'">
                                    {{ sortDirection === 'asc' ? '↑' : '↓' }}
                                </span>
                            </TableHead>
                            <TableHead>Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="project in sortedProjects" :key="project.id">
                            <TableCell class="font-medium">{{ project.title }}</TableCell>
                            <TableCell>
                                <Badge :variant="getStatusVariant(project.status)">
                                    {{ getStatusText(project.status) }}
                                </Badge>
                            </TableCell>
                            <TableCell>
                                {{ project.resources_count }}
                                <span v-if="project.assigned_resources"> ({{ project.assigned_resources }})</span>
                            </TableCell>
                            <TableCell>{{ project.estimate_hours }}</TableCell>
                            <TableCell :class="'w-1/4'">
                                <div class="flex w-full items-center gap-2">
                                    <Progress :model-value="project.progress" class="h-2 w-10/12" />
                                    <span class="w-2/12 text-sm text-muted-foreground">{{ project.progress }}%</span>
                                </div>
                            </TableCell>
                            <TableCell>
                                <Badge
                                    :variant="getDeadlineVariant(project.deadline_near, project.deadline, project.deadline_approaching)"
                                    class="text-xs"
                                    ref="deadlineBadge"
                                >
                                    {{ getDeadlineText(project.deadline) }}
                                    <span v-if="$refs.deadlineBadge" class="hidden">{{ ($refs.deadlineBadge as HTMLElement)?.className }}</span>
                                </Badge>
                            </TableCell>
                            <TableCell>
                                <Link :href="route('projects.show', project.id)">
                                    <Button variant="outline" size="sm">View</Button>
                                </Link>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
        <div v-else class="py-12 text-center">
            <p class="text-muted-foreground">No projects found.</p>
            <Link :href="route('projects.create')" class="mt-4 inline-block">
                <Button>Create Your First Project</Button>
            </Link>
        </div>
    </AppLayout>
</template>
