<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'; // Assuming Select components
import Textarea from '@/components/ui/textarea/Textarea.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { router, useForm } from '@inertiajs/vue3'; // Import router
import { computed, onMounted, ref, type PropType } from 'vue'; // Import ref and onMounted

// Interface for available resources passed from controller
interface AvailableResource {
    id: number;
    name: string;
    capacity: number;
    availability_status: string;
}

// Interface for the resource allocation part of the form
interface AllocatedResource {
    id: number;
    name: string; // Keep name for display
    assigned_hours: number | undefined;
    start_date: string;
    end_date: string | undefined;
}

// Interface for the project data passed from controller
interface ProjectData {
    id: number;
    title: string;
    description: string | null;
    estimate_hours: number;
    hours_remaining: number | null; // Added
    deadline: string | null;
    status: 'upcoming' | 'ongoing' | 'completed';
    // assigned_resources is keyed by ID in the controller for easier lookup
    assigned_resources: { [key: number]: Omit<AllocatedResource, 'name'> };
}

const props = defineProps({
    project: {
        type: Object as PropType<ProjectData>,
        required: true,
    },
    availableResources: {
        type: Array as PropType<AvailableResource[]>,
        required: true,
    },
});

// Use a local ref for UI manipulation
const allocatedResourcesUI = ref<AllocatedResource[]>([]);

// Initialize the UI ref from the project prop when the component mounts
onMounted(() => {
    allocatedResourcesUI.value = Object.entries(props.project.assigned_resources).map(([idStr, details]) => {
        const id = parseInt(idStr, 10);
        const resourceInfo = props.availableResources.find((r) => r.id === id);
        return {
            id: id,
            name: resourceInfo ? resourceInfo.name : `Resource ${id}`, // Find name or use placeholder
            assigned_hours: details.assigned_hours,
            start_date: details.start_date,
            end_date: details.end_date,
        };
    });
});

const form = useForm({
    _method: 'PUT', // Specify PUT method for update
    title: props.project.title,
    description: props.project.description ?? '', // Handle null description
    estimate_hours: props.project.estimate_hours.toString(),
    hours_remaining: props.project.hours_remaining?.toString() ?? '', // Initialize with string, handle null
    deadline: props.project.deadline ?? '',
    status: props.project.status,
    resources: [] as any[], // Will be populated from allocatedResourcesUI on submit
});

// --- Resource Allocation Logic ---
const addResource = (resource: AvailableResource) => {
    if (!allocatedResourcesUI.value.some((r) => r.id === resource.id)) {
        allocatedResourcesUI.value.push({
            id: resource.id,
            name: resource.name,
            assigned_hours: undefined,
            start_date: new Date().toISOString().split('T')[0],
            end_date: undefined,
        });
    }
};

const removeResource = (resourceId: number) => {
    allocatedResourcesUI.value = allocatedResourcesUI.value.filter((r) => r.id !== resourceId);
};

// Filter available resources that haven't been added yet
const unallocatedResources = computed(() => {
    const allocatedIds = allocatedResourcesUI.value.map((r) => r.id);
    return props.availableResources.filter((r) => !allocatedIds.includes(r.id));
});
// --- End Resource Allocation Logic ---

// Helper to safely access nested resource errors
const getResourceFieldError = (index: number, field: 'assigned_hours' | 'start_date' | 'end_date'): string | undefined => {
    const key = `resources.${index}.${field}` as keyof typeof form.errors;
    return form.errors[key];
};

const submit = () => {
    // Assign the UI state to the form object right before submitting
    form.resources = allocatedResourcesUI.value.map((r) => ({
        // Ensure only necessary fields are sent
        id: r.id,
        assigned_hours: r.assigned_hours,
        start_date: r.start_date,
        end_date: r.end_date,
    }));

    form.post(route('projects.update', props.project.id), {
        // Use post because HTML forms don't support PUT natively, Laravel handles _method
        onError: (errors) => {
            console.error('Project update failed:', errors);
        },
        preserveScroll: true, // Keep scroll position on error
    });
};
</script>

<template>
    <AppLayout title="Edit Project">
        <template #header>
            <Heading :title="`Edit Project: ${project.title}`" description="Update the project details." />
        </template>

        <form @submit.prevent="submit">
            <Card>
                <CardHeader>
                    <CardTitle>Project Details</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div>
                        <Label for="title">Project Title</Label>
                        <Input id="title" v-model="form.title" type="text" required />
                        <InputError :message="form.errors.title" class="mt-1" />
                    </div>
                    <div>
                        <Label for="description">Description</Label>
                        <Textarea id="description" v-model="form.description" />
                        <InputError :message="form.errors.description" class="mt-1" />
                    </div>
                    <div>
                        <Label for="status">Status</Label>
                        <Select v-model="form.status">
                            <SelectTrigger id="status">
                                <SelectValue placeholder="Select status" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="upcoming">Upcoming</SelectItem>
                                <SelectItem value="ongoing">Ongoing</SelectItem>
                                <SelectItem value="completed">Completed</SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="form.errors.status" class="mt-1" />
                    </div>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div>
                            <Label for="estimate_hours">Estimated Hours</Label>
                            <Input id="estimate_hours" v-model="form.estimate_hours" type="number" step="0.1" min="0.1" required />
                            <InputError :message="form.errors.estimate_hours" class="mt-1" />
                        </div>
                        <div>
                            <Label for="hours_remaining">Hours Remaining</Label>
                            <Input
                                id="hours_remaining"
                                v-model="form.hours_remaining"
                                type="number"
                                step="0.1"
                                min="0"
                                :max="form.estimate_hours"
                                required
                            />
                            <InputError :message="form.errors.hours_remaining" class="mt-1" />
                        </div>
                        <div>
                            <Label for="deadline">Deadline</Label>
                            <Input id="deadline" v-model="form.deadline" type="date" required />
                            <InputError :message="form.errors.deadline" class="mt-1" />
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Resource Allocation Section -->
            <Card class="mt-6">
                <CardHeader>
                    <CardTitle>Resource Allocation</CardTitle>
                    <CardDescription>Update resource assignments, hours, and timelines.</CardDescription>
                </CardHeader>
                <CardContent>
                    <!-- List of Allocated Resources -->
                    <div v-if="allocatedResourcesUI.length > 0" class="mb-6 space-y-4 border-b pb-4">
                        <h4 class="mb-2 text-sm font-medium">Assigned Resources:</h4>
                        <div
                            v-for="(resource, index) in allocatedResourcesUI"
                            :key="resource.id"
                            class="flex flex-col gap-3 rounded-md border p-3 sm:flex-row sm:items-end"
                        >
                            <div class="flex-1 font-medium">{{ resource.name }}</div>
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3 sm:gap-2">
                                <div>
                                    <Label :for="`assigned_hours_${resource.id}`" class="text-xs">Assigned Hours</Label>
                                    <Input
                                        :id="`assigned_hours_${resource.id}`"
                                        v-model.number="resource.assigned_hours"
                                        type="number"
                                        step="0.1"
                                        min="0.1"
                                        required
                                        placeholder="Hours"
                                    />
                                    <InputError :message="getResourceFieldError(index, 'assigned_hours')" class="mt-1" />
                                </div>
                                <div>
                                    <Label :for="`start_date_${resource.id}`" class="text-xs">Start Date</Label>
                                    <Input :id="`start_date_${resource.id}`" v-model="resource.start_date" type="date" required />
                                    <InputError :message="getResourceFieldError(index, 'start_date')" class="mt-1" />
                                </div>
                                <div>
                                    <Label :for="`end_date_${resource.id}`" class="text-xs">End Date (Optional)</Label>
                                    <Input :id="`end_date_${resource.id}`" v-model="resource.end_date" type="date" :min="resource.start_date" />
                                    <InputError :message="getResourceFieldError(index, 'end_date')" class="mt-1" />
                                </div>
                            </div>
                            <Button
                                @click="removeResource(resource.id)"
                                variant="destructive"
                                size="sm"
                                type="button"
                                class="mt-2 sm:mt-0 sm:self-end"
                                >Remove</Button
                            >
                        </div>
                        <InputError :message="form.errors.resources" class="mt-1" />
                        <!-- General resource error -->
                    </div>

                    <!-- List of Available Resources to Add -->
                    <div>
                        <h4 class="mb-2 text-sm font-medium">Available Resources:</h4>
                        <div v-if="unallocatedResources.length > 0" class="grid grid-cols-1 gap-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                            <div
                                v-for="resource in unallocatedResources"
                                :key="resource.id"
                                class="flex items-center justify-between rounded-md border p-2 text-sm"
                            >
                                <span>{{ resource.name }} ({{ resource.availability_status }})</span>
                                <Button @click="addResource(resource)" size="sm" variant="outline" type="button">+</Button>
                            </div>
                        </div>
                        <p v-else class="text-sm italic text-muted-foreground">No more resources available to assign.</p>
                    </div>
                </CardContent>
                <CardFooter>
                    <Button :disabled="form.processing" type="submit">
                        {{ form.processing ? 'Updating...' : 'Update Project' }}
                    </Button>
                    <Button variant="ghost" type="button" @click="() => router.get(route('projects.index'))" class="ml-2" :disabled="form.processing">
                        Cancel
                    </Button>
                </CardFooter>
            </Card>
        </form>
    </AppLayout>
</template>
