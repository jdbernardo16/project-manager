<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';
import { computed, ref, type PropType } from 'vue';

// Interface matching data from ProjectController@calendar
interface CalendarEvent {
    id: string;
    title: string;
    start: string; // YYYY-MM-DD
    end: string; // YYYY-MM-DD
    resourceId: number;
    color?: string; // Optional color from controller
    allDay?: boolean;
}

interface ProjectResource {
    id: number;
    name: string;
    color?: string; // Optional color from controller
    assigned_hours: number;
    start_date: string | null;
    end_date: string | null;
}

interface ProjectData {
    id: number;
    title: string;
    deadline: string | null;
}

const props = defineProps({
    project: {
        type: Object as PropType<ProjectData>,
        required: true,
    },
    projectResources: {
        type: Array as PropType<ProjectResource[]>,
        required: true,
    },
    calendarEvents: {
        type: Array as PropType<CalendarEvent[]>,
        required: true,
    },
});

// --- Basic Calendar Logic (Placeholder) ---
const currentDate = ref(new Date()); // Start with current month
const daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

const currentMonthName = computed(() => currentDate.value.toLocaleString('default', { month: 'long' }));
const currentYear = computed(() => currentDate.value.getFullYear());

// Interface for calendar day objects
interface CalendarDay {
    date: string;
    day: number;
    currentMonth: boolean;
    events: CalendarEvent[];
}

const calendarDays = computed((): CalendarDay[] => {
    // Add return type
    const year = currentDate.value.getFullYear();
    const month = currentDate.value.getMonth();
    const firstDayOfMonth = new Date(year, month, 1);
    const lastDayOfMonth = new Date(year, month + 1, 0);
    const firstDayWeekday = firstDayOfMonth.getDay(); // 0 = Sun, 1 = Mon, ...
    const lastDayDate = lastDayOfMonth.getDate();

    const days: CalendarDay[] = []; // Explicitly type the array

    // Days from previous month
    const prevMonthLastDay = new Date(year, month, 0).getDate();
    for (let i = firstDayWeekday - 1; i >= 0; i--) {
        // Ensure month is 0-based for Date constructor if needed, but string format should be correct
        const prevMonth = month === 0 ? 11 : month - 1;
        const prevYear = month === 0 ? year - 1 : year;
        const dateStr = `${prevYear}-${String(prevMonth + 1).padStart(2, '0')}-${String(prevMonthLastDay - i).padStart(2, '0')}`;
        days.push({ date: dateStr, day: prevMonthLastDay - i, currentMonth: false, events: [] });
    }

    // Days of current month
    for (let day = 1; day <= lastDayDate; day++) {
        const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const eventsOnDay = props.calendarEvents.filter((event) => event.start === dateStr); // Simple match for full-day events
        days.push({ date: dateStr, day: day, currentMonth: true, events: eventsOnDay });
    }

    // Days from next month
    const lastDayWeekday = lastDayOfMonth.getDay();
    const daysToAdd = 6 - lastDayWeekday;
    for (let i = 1; i <= daysToAdd; i++) {
        const nextMonth = month === 11 ? 0 : month + 1;
        const nextYear = month === 11 ? year + 1 : year;
        const dateStr = `${nextYear}-${String(nextMonth + 1).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
        days.push({ date: dateStr, day: i, currentMonth: false, events: [] });
    }

    // Ensure 6 rows (42 days) for consistent grid height
    // Ensure 6 rows (42 days) for consistent grid height
    let lastDayInGrid = days[days.length - 1];
    while (days.length < 42) {
        // Parse the last date string correctly (YYYY-MM-DD)
        const parts = lastDayInGrid.date.split('-').map(Number);
        // Month is 0-indexed in Date constructor
        const lastDateObj = new Date(Date.UTC(parts[0], parts[1] - 1, parts[2]));

        lastDateObj.setUTCDate(lastDateObj.getUTCDate() + 1); // Increment day using UTC to avoid timezone issues

        const nextDay = lastDateObj.getUTCDate();
        const nextMonth = lastDateObj.getUTCMonth() + 1; // Month is 1-indexed for string format
        const nextYear = lastDateObj.getUTCFullYear();
        const nextDateStr = `${nextYear}-${String(nextMonth).padStart(2, '0')}-${String(nextDay).padStart(2, '0')}`;

        const newDayInfo: CalendarDay = { date: nextDateStr, day: nextDay, currentMonth: false, events: [] };
        days.push(newDayInfo);
        lastDayInGrid = newDayInfo; // Update last day for next iteration
    }

    return days;
});

const previousMonth = () => {
    currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() - 1, 1);
};

const nextMonth = () => {
    currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() + 1, 1);
};
// --- End Basic Calendar Logic ---

// --- Basic Timeline Logic (Placeholder) ---
const timelineDays = computed(() => {
    const days = [];
    const startDate = new Date(); // Start timeline from today
    const numDays = 30; // Show next 30 days

    for (let i = 0; i < numDays; i++) {
        const date = new Date(startDate);
        date.setDate(startDate.getDate() + i);
        days.push({
            date: date.toISOString().split('T')[0], // YYYY-MM-DD
            day: date.getDate(),
            month: date.getMonth() + 1,
            isWeekend: date.getDay() === 0 || date.getDay() === 6,
        });
    }
    return days;
});

// Check if a resource is allocated on a specific date based on calendarEvents
const isAllocated = (resourceId: number, date: string): boolean => {
    return props.calendarEvents.some((event) => event.resourceId === resourceId && event.start === date);
};
// --- End Basic Timeline Logic ---
</script>

<template>
    <AppLayout :title="`Calendar: ${project.title}`">
        <template #header>
            <Heading :title="`Project Calendar: ${project.title}`" description="Visualize project timeline and resource allocation." />
        </template>

        <Card>
            <CardHeader>
                <div class="flex items-center justify-between">
                    <CardTitle>Month View</CardTitle>
                    <div class="flex items-center space-x-2">
                        <Button @click="previousMonth" variant="outline" size="icon">
                            <ChevronLeft class="h-4 w-4" />
                        </Button>
                        <span class="w-32 text-center font-medium">{{ currentMonthName }} {{ currentYear }}</span>
                        <Button @click="nextMonth" variant="outline" size="icon">
                            <ChevronRight class="h-4 w-4" />
                        </Button>
                    </div>
                </div>
            </CardHeader>
            <CardContent>
                <!-- Calendar Grid -->
                <div class="grid grid-cols-7 border-l border-t">
                    <!-- Days of week headers -->
                    <div v-for="day in daysOfWeek" :key="day" class="border-b border-r bg-muted/50 py-2 text-center text-sm font-medium">
                        {{ day }}
                    </div>

                    <!-- Calendar days -->
                    <div
                        v-for="(dateInfo, index) in calendarDays"
                        :key="index"
                        class="relative h-28 overflow-hidden border-b border-r p-1 text-sm"
                        :class="{ 'bg-muted/30': !dateInfo.currentMonth }"
                    >
                        <div class="font-medium" :class="{ 'text-muted-foreground': !dateInfo.currentMonth }">{{ dateInfo.day }}</div>
                        <div class="mt-1 space-y-0.5">
                            <div
                                v-for="event in dateInfo.events"
                                :key="event.id"
                                class="truncate rounded px-1 text-xs"
                                :style="{ backgroundColor: event.color || '#a5d8ff' }"
                                :title="event.title"
                            >
                                {{ event.title }}
                            </div>
                        </div>
                    </div>
                </div>
            </CardContent>
        </Card>

        <!-- Timeline Visualization -->
        <Card class="mt-6">
            <CardHeader>
                <CardTitle>Resource Timeline (Next {{ timelineDays.length }} Days)</CardTitle>
            </CardHeader>
            <CardContent class="overflow-x-auto">
                <div class="min-w-[800px]">
                    <!-- Ensure minimum width for scrolling -->
                    <!-- Timeline Header -->
                    <div
                        class="sticky top-0 z-10 grid grid-cols-[150px_repeat(30,_minmax(30px,_1fr))] gap-px bg-background bg-border text-center text-xs font-medium"
                    >
                        <div class="border-b border-r bg-muted/50 px-1 py-2">Resource</div>
                        <div
                            v-for="day in timelineDays"
                            :key="day.date"
                            class="border-b border-r px-1 py-2"
                            :class="{ 'bg-muted/30': day.isWeekend }"
                        >
                            {{ day.day }}
                        </div>
                    </div>

                    <!-- Timeline Rows -->
                    <div class="grid grid-cols-[150px_repeat(30,_minmax(30px,_1fr))] gap-px bg-border">
                        <template v-for="resource in projectResources" :key="resource.id">
                            <div class="truncate border-r bg-muted/50 px-1 py-2 text-sm" :title="resource.name">{{ resource.name }}</div>
                            <div
                                v-for="day in timelineDays"
                                :key="`${resource.id}-${day.date}`"
                                class="h-8 border-r"
                                :class="{
                                    'bg-muted/30': day.isWeekend && !isAllocated(resource.id, day.date),
                                    '!bg-primary/20': isAllocated(resource.id, day.date), // Use resource color if available
                                }"
                                :style="isAllocated(resource.id, day.date) ? { backgroundColor: resource.color || '#3b82f6' } : {}"
                                :title="isAllocated(resource.id, day.date) ? `${resource.name} allocated` : ''"
                            ></div>
                        </template>
                    </div>
                </div>
            </CardContent>
        </Card>
    </AppLayout>
</template>
