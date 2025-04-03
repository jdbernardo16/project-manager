<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { router, useForm } from '@inertiajs/vue3'; // Import router
import type { PropType } from 'vue';

// Interface matching data from ResourceController@edit
interface ResourceData {
    id: number;
    user_id: number;
    name: string;
    email: string;
    role: 'admin' | 'project_manager' | 'resource';
    capacity: number;
    availability_status: 'available' | 'assigned' | 'unavailable';
    notes: string | null;
}

const props = defineProps({
    resource: {
        type: Object as PropType<ResourceData>,
        required: true,
    },
});

const form = useForm({
    _method: 'PUT', // Specify PUT method
    name: props.resource.name,
    email: props.resource.email,
    password: '', // Password is optional on update
    password_confirmation: '',
    role: props.resource.role,
    capacity: props.resource.capacity,
    availability_status: props.resource.availability_status,
    notes: props.resource.notes ?? '', // Handle null notes
});

const submit = () => {
    // Use post because HTML forms don't support PUT natively
    form.post(route('resources.update', props.resource.id), {
        onError: (errors) => {
            console.error('Resource update failed:', errors);
            // Clear password fields on error
            form.reset('password', 'password_confirmation');
        },
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout :title="`Edit Resource: ${resource.name}`">
        <template #header>
            <Heading :title="`Edit Resource: ${resource.name}`" description="Update the user and resource profile details." />
        </template>

        <form @submit.prevent="submit">
            <Card>
                <CardHeader>
                    <CardTitle>User & Resource Details</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <h3 class="mb-4 border-b pb-2 text-lg font-medium">User Information</h3>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <Label for="name">Full Name</Label>
                            <Input id="name" v-model="form.name" type="text" required />
                            <InputError :message="form.errors.name" class="mt-1" />
                        </div>
                        <div>
                            <Label for="email">Email Address</Label>
                            <Input id="email" v-model="form.email" type="email" required />
                            <InputError :message="form.errors.email" class="mt-1" />
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <Label for="password">New Password (Optional)</Label>
                            <Input id="password" v-model="form.password" type="password" placeholder="Leave blank to keep current password" />
                            <InputError :message="form.errors.password" class="mt-1" />
                        </div>
                        <div>
                            <Label for="password_confirmation">Confirm New Password</Label>
                            <Input id="password_confirmation" v-model="form.password_confirmation" type="password" />
                            <InputError :message="form.errors.password_confirmation" class="mt-1" />
                        </div>
                    </div>
                    <div>
                        <Label for="role">User Role</Label>
                        <Select v-model="form.role">
                            <SelectTrigger id="role">
                                <SelectValue placeholder="Select role" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="resource">Resource</SelectItem>
                                <SelectItem value="project_manager">Project Manager</SelectItem>
                                <SelectItem value="admin">Admin</SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="form.errors.role" class="mt-1" />
                    </div>

                    <h3 class="mb-4 border-b pb-2 pt-6 text-lg font-medium">Resource Profile</h3>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <Label for="capacity">Capacity (Hours per Day)</Label>
                            <Input id="capacity" v-model.number="form.capacity" type="number" step="0.1" min="0" max="24" required />
                            <InputError :message="form.errors.capacity" class="mt-1" />
                        </div>
                        <div>
                            <Label for="availability_status">Availability</Label>
                            <Select v-model="form.availability_status">
                                <SelectTrigger id="availability_status">
                                    <SelectValue placeholder="Select status" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="available">Available</SelectItem>
                                    <SelectItem value="assigned">Assigned</SelectItem>
                                    <SelectItem value="unavailable">Unavailable</SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.availability_status" class="mt-1" />
                        </div>
                    </div>
                    <div>
                        <Label for="notes">Notes (Optional)</Label>
                        <Textarea id="notes" v-model="form.notes" />
                        <InputError :message="form.errors.notes" class="mt-1" />
                    </div>
                </CardContent>
                <CardFooter>
                    <Button :disabled="form.processing" type="submit">
                        {{ form.processing ? 'Updating...' : 'Update Resource' }}
                    </Button>
                    <Button
                        variant="ghost"
                        type="button"
                        @click="() => router.get(route('resources.index'))"
                        class="ml-2"
                        :disabled="form.processing"
                    >
                        Cancel
                    </Button>
                </CardFooter>
            </Card>
        </form>
    </AppLayout>
</template>
