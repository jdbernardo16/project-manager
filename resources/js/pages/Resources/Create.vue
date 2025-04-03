<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'; // Use placeholder
import Textarea from '@/components/ui/textarea/Textarea.vue'; // Use placeholder
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'resource', // Default role
    capacity: 7.0, // Default capacity
    availability_status: 'available', // Default status
    notes: '',
});

const submit = () => {
    form.post(route('resources.store'), {
        onError: (errors) => {
            console.error('Resource creation failed:', errors);
            // Clear password fields on error for security
            form.reset('password', 'password_confirmation');
        },
        onSuccess: () => {
            // Form reset might happen automatically on success depending on Inertia setup
            // form.reset(); // Optionally reset form explicitly
        },
    });
};
</script>

<template>
    <AppLayout title="Add Resource">
        <template #header>
            <Heading title="Add New Resource" description="Create a new user and their resource profile." />
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
                            <Label for="password">Password</Label>
                            <Input id="password" v-model="form.password" type="password" required />
                            <InputError :message="form.errors.password" class="mt-1" />
                        </div>
                        <div>
                            <Label for="password_confirmation">Confirm Password</Label>
                            <Input id="password_confirmation" v-model="form.password_confirmation" type="password" required />
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
                            <Label for="availability_status">Initial Availability</Label>
                            <Select v-model="form.availability_status">
                                <SelectTrigger id="availability_status">
                                    <SelectValue placeholder="Select status" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="available">Available</SelectItem>
                                    <SelectItem value="unavailable">Unavailable</SelectItem>
                                    <!-- 'Assigned' is typically set automatically -->
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
                        {{ form.processing ? 'Creating...' : 'Create Resource' }}
                    </Button>
                    <Button variant="ghost" type="button" @click="form.reset()" class="ml-2" :disabled="form.processing"> Reset Form </Button>
                </CardFooter>
            </Card>
        </form>
    </AppLayout>
</template>
