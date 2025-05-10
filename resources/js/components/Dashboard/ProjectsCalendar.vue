<script setup lang="ts">
import { Button } from '@/components/ui/button'; // Import Button
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';
import { computed, ref, type PropType } from 'vue';

// Interface for project data received from the backend
interface ProjectCalendarEvent {
    id: number;
    title: string;
    start: string | null; // YYYY-MM-DD
    end: string | null; // YYYY-MM-DD
    // Color will be assigned automatically
}

const props = defineProps({
    projects: {
        type: Array as PropType<ProjectCalendarEvent[]>,
        required: true,
    },
});

// --- Basic Calendar Logic ---
const currentDate = ref(new Date()); // Start with current month
const daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

const currentMonthName = computed(() => currentDate.value.toLocaleString('default', { month: 'long' }));
const currentYear = computed(() => currentDate.value.getFullYear());

// Interface for calendar day objects
interface CalendarDay {
    date: string;
    day: number;
    currentMonth: boolean;
    events: ProjectCalendarEvent[];
}

const calendarDays = computed((): CalendarDay[] => {
    const year = currentDate.value.getFullYear();
    const month = currentDate.value.getMonth();
    const firstDayOfMonth = new Date(year, month, 1);
    const lastDayOfMonth = new Date(year, month + 1, 0);
    const firstDayWeekday = firstDayOfMonth.getDay(); // 0 = Sun, 1 = Mon, ...
    const lastDayDate = lastDayOfMonth.getDate();

    const days: CalendarDay[] = [];

    // Days from previous month
    const prevMonthLastDay = new Date(year, month, 0).getDate();
    for (let i = firstDayWeekday - 1; i >= 0; i--) {
        const prevMonth = month === 0 ? 11 : month - 1;
        const prevYear = month === 0 ? year - 1 : year;
        const dateStr = `${prevYear}-${String(prevMonth + 1).padStart(2, '0')}-${String(prevMonthLastDay - i).padStart(2, '0')}`;
        days.push({ date: dateStr, day: prevMonthLastDay - i, currentMonth: false, events: [] });
    }

    // Days of current month
    for (let day = 1; day <= lastDayDate; day++) {
        // Construct date string and Date object using UTC to avoid timezone issues
        const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`; // YYYY-MM-DD UTC
        const currentDayDateUTC = new Date(Date.UTC(year, month, day)); // Date object representing midnight UTC

        // Filter projects that are active on this day (using UTC comparison)
        const eventsOnDay = props.projects.filter((project) => {
            if (!project.start || !project.end) return false; // Require both start and end dates

            // Use the UTC date string for comparison
            const currentDateStr = dateStr;

            // Use getUTCDay() for weekend check (0=Sun, 6=Sat)
            const dayOfWeekUTC = currentDayDateUTC.getUTCDay();

            // Check if the current UTC date string is within the project's range
            // and if the current UTC day is not a weekend
            return currentDateStr >= project.start && currentDateStr <= project.end && dayOfWeekUTC !== 0 && dayOfWeekUTC !== 6;
        });

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
    while (days.length < 42) {
        const lastDayInGrid = days[days.length - 1];
        const parts = lastDayInGrid.date.split('-').map(Number);
        const lastDateObj = new Date(Date.UTC(parts[0], parts[1] - 1, parts[2]));

        lastDateObj.setUTCDate(lastDateObj.getUTCDate() + 1);

        const nextDay = lastDateObj.getUTCDate();
        const nextMonth = lastDateObj.getUTCMonth() + 1;
        const nextYear = lastDateObj.getUTCFullYear();
        const nextDateStr = `${nextYear}-${String(nextMonth).padStart(2, '0')}-${String(nextDay).padStart(2, '0')}`;

        const newDayInfo: CalendarDay = { date: nextDateStr, day: nextDay, currentMonth: false, events: [] };
        days.push(newDayInfo);
    }

    return days;
});

const previousMonth = () => {
    currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() - 1, 1);
};

const nextMonth = () => {
    currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() + 1, 1);
};

// Rainbow color palette
const rainbowColors = [
    'red',
    'orange',
    'amber',
    'yellow',
    'lime',
    'green',
    'emerald',
    'teal',
    'cyan',
    'sky',
    'blue',
    'indigo',
    'violet',
    'purple',
    'fuchsia',
    'pink',
    'rose',
];

// Get color class based on project ID
const getProjectColorClass = (id: number) => {
    const colorIndex = id % rainbowColors.length;
    const color = rainbowColors[colorIndex];
    return `bg-${color}-200 text-${color}-800`;
};
</script>

<template>
    <Card>
        <CardHeader>
            <div class="flex items-center justify-between">
                <CardTitle>Project Calendar</CardTitle>
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
                    <div class="hide-scrollbar mt-1 h-full overflow-y-auto">
                        <div class="space-y-0.5">
                            <div
                                v-for="event in dateInfo.events"
                                :key="event.id"
                                class="truncate rounded px-1 text-xs"
                                :class="getProjectColorClass(event.id)"
                                :title="event.title"
                            >
                                {{ event.title }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </CardContent>
    </Card>
</template>
