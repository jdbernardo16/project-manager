<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue'; // Assuming InputError component
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input'; // Assuming Input component
import { Label } from '@/components/ui/label'; // Assuming Label component
import Textarea from '@/components/ui/textarea/Textarea.vue'; // Use default import
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { computed, ref, type PropType } from 'vue'; // Import ref

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
    assigned_hours: number | undefined; // Use undefined for v-model compatibility
    start_date: string;
    end_date: string | undefined; // Use undefined for v-model compatibility
}

const props = defineProps({
    availableResources: {
        type: Array as PropType<AvailableResource[]>,
        required: true,
    },
});

const form = useForm({
    title: '',
    description: '',
    estimate_hours: '', // Initialize number input with empty string
    deadline: '',
    resources: [] as any[], // Use any[] to bypass strict FormDataConvertible check for this field in useForm
});

// --- Resource Allocation Logic ---
// Use a local ref for UI manipulation to avoid direct mutation issues with form object
const allocatedResourcesUI = ref<AllocatedResource[]>([]);
const addResource = (resource: AvailableResource) => {
    // Check if resource is already added
    // Check if resource is already added in the UI ref
    if (!allocatedResourcesUI.value.some((r) => r.id === resource.id)) {
        allocatedResourcesUI.value.push({
            id: resource.id,
            name: resource.name, // Store name for display in the form
            assigned_hours: undefined, // Use undefined
            start_date: new Date().toISOString().split('T')[0], // Default to today
            end_date: undefined, // Use undefined
        });
    }
};

const removeResource = (resourceId: number) => {
    allocatedResourcesUI.value = allocatedResourcesUI.value.filter((r) => r.id !== resourceId);
};

// Filter available resources that haven't been added yet
const unallocatedResources = computed(() => {
    // Use the UI ref for checking allocated IDs
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
    form.resources = allocatedResourcesUI.value;
    form.post(route('projects.store'), {
        onError: (errors) => {
            console.error('Project creation failed:', errors);
            // Handle specific errors if needed
        },
        onSuccess: () => {
            // Optionally reset form or show success message handled by controller redirect
        },
    });
};
</script>

<template>
    <AppLayout title="Create Project">
        <template #header>
            <Heading title="Create New Project" description="Fill in the details for the new project." />
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
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <Label for="estimate_hours">Estimated Hours</Label>
                            <Input id="estimate_hours" v-model.number="form.estimate_hours" type="number" step="0.1" min="0.1" required />
                            <InputError :message="form.errors.estimate_hours" class="mt-1" />
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
                    <CardDescription>Assign resources and specify their hours and timeline for this project.</CardDescription>
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
                        {{ form.processing ? 'Creating...' : 'Create Project' }}
                    </Button>
                    <Button variant="ghost" type="button" @click="form.reset()" class="ml-2" :disabled="form.processing"> Reset Form </Button>
                </CardFooter>
            </Card>
        </form>
    </AppLayout>
</template>
