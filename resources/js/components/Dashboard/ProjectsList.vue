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
                        <Badge v-if="project.deadline" variant="outline" class="text-xs"> Due: {{ project.deadline }} </Badge>
                        <Badge v-else variant="secondary" class="text-xs"> No Deadline </Badge>
                    </div>
                </div>
            </div>
            <div v-else class="text-sm italic text-muted-foreground">No {{ title.toLowerCase() }} found.</div>
        </CardContent>
    </Card>
</template>
