<script setup lang="ts">
import Badge from '@/components/ui/badge/Badge.vue'; // Use default import from .vue file
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import Progress from '@/components/ui/progress/Progress.vue'; // Use default import from .vue file
import { Link } from '@inertiajs/vue3';
import type { PropType } from 'vue';

interface Project {
    id: number;
    title: string;
    deadline: string | null;
    resources_count: number;
    progress?: number; // Optional progress
    assigned_resources?: string; // Optional string list
    is_overdue?: boolean;
    deadline_near?: boolean;
    deadline_approaching?: boolean;
}

defineProps({
    title: {
        type: String,
        required: true,
    },
    projects: {
        type: Array as PropType<Project[]>,
        required: true,
    },
});

const getDeadlineVariant = (
    deadlineNear: boolean | undefined,
    deadline: string | null,
    deadlineApproaching: boolean | undefined,
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
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle>{{ title }}</CardTitle>
            <!-- Optional: Add description or action button here -->
        </CardHeader>
        <CardContent>
            <div v-if="projects.length > 0" class="space-y-4">
                <div v-for="project in projects" :key="project.id" class="flex items-center justify-between space-x-4">
                    <div class="flex-1 space-y-1">
                        <Link :href="route('projects.show', project.id)" class="text-sm font-medium leading-none hover:underline">
                            {{ project.title }}
                        </Link>
                        <p v-if="project.assigned_resources" class="truncate text-xs text-muted-foreground">
                            {{ project.assigned_resources }} ({{ project.resources_count }} resource{{ project.resources_count !== 1 ? 's' : '' }})
                        </p>
                        <p v-else class="text-xs italic text-muted-foreground">No resources assigned</p>
                        <Progress v-if="project.progress !== undefined" :model-value="project.progress" class="mt-1 h-2" />
                    </div>
                    <div class="text-right">
                        <Badge
                            v-if="project.deadline"
                            :variant="getDeadlineVariant(project.deadline_near, project.deadline, project.deadline_approaching)"
                            class="text-xs"
                        >
                            {{ getDeadlineText(project.deadline) }}
                        </Badge>
                        <Badge v-else variant="secondary" class="text-xs">No Deadline</Badge>
                    </div>
                </div>
            </div>
            <div v-else class="text-sm italic text-muted-foreground">No {{ title.toLowerCase() }} found.</div>
        </CardContent>
    </Card>
</template>
