<template>
    <AppLayout title="Dashboard">
        <template #header>
            <Heading title="Dashboard" />
        </template>

        <!-- Statistics Cards -->
        <div class="mb-6 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <StatsCard title="Total Projects" :value="statistics.totalProjects" icon="FolderKanban" />
            <StatsCard title="Available Resources" :value="statistics.availableResources" icon="Users" />
            <StatsCard title="Projects Due Soon" :value="statistics.projectsDueSoon" icon="Clock" />
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Projects List Section -->
            <div class="space-y-6">
                <ProjectsList title="Ongoing Projects" :projects="ongoingProjects" />
                <ProjectsList title="Upcoming Projects" :projects="upcomingProjects" />
            </div>

            <!-- Resource Availability Section -->
            <ResourceAvailability :resources="resources" />
        </div>

        <!-- All Projects Calendar -->
        <div class="mt-6">
            <ProjectsCalendar :projects="allProjectsCalendarData" />
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import ProjectsCalendar from '@/components/Dashboard/ProjectsCalendar.vue'; // Import new calendar component
import ProjectsList from '@/components/Dashboard/ProjectsList.vue'; // Will be created
import ResourceAvailability from '@/components/Dashboard/ResourceAvailability.vue'; // Will be created
import StatsCard from '@/components/Dashboard/StatsCard.vue'; // Will be created
import Heading from '@/components/Heading.vue'; // Assuming Heading component exists
import AppLayout from '@/layouts/AppLayout.vue'; // Adjusted layout path
import type { PropType } from 'vue';

// Define interfaces for props for better type safety
interface StatisticProps {
    totalProjects: number;
    availableResources: number;
    projectsDueSoon: number;
}

interface Project {
    id: number;
    title: string;
    deadline: string | null;
    resources_count: number;
    progress: number; // Updated: Now required
    is_overdue: boolean; // Added
    deadline_near: boolean; // Added
    assigned_resources?: string; // Optional string list
}

interface Resource {
    id: number;
    name: string;
    capacity: number;
    availability_status: 'available' | 'assigned' | 'unavailable';
    current_project_title: string;
    user_id: number;
}

const props = defineProps({
    statistics: {
        type: Object as PropType<StatisticProps>,
        required: true,
    },
    ongoingProjects: {
        type: Array as PropType<Project[]>,
        required: true,
    },
    upcomingProjects: {
        type: Array as PropType<Project[]>,
        required: true,
    },
    resources: {
        type: Array as PropType<Resource[]>,
        required: true,
    },
    allProjectsCalendarData: {
        // Define prop for calendar data
        type: Array as PropType<{ id: number; title: string; start: string | null; end: string | null }[]>,
        required: true,
    },
});
</script>
