<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import type { Reservation, Room } from '@/types/pms';

const props = defineProps<{ rooms: Room[]; reservations: Reservation[] }>();

const days = Array.from({ length: 14 }, (_, index) => {
    const date = new Date();
    date.setDate(date.getDate() + index);
    return date.toISOString().slice(0, 10);
});

function reservationFor(roomId: number, date: string): Reservation | undefined {
    return props.reservations.find((reservation) => {
        const rooms = reservation.reservationRooms ?? reservation.reservation_rooms ?? [];
        return rooms.some((item) => item.room.id === roomId) && reservation.check_in <= date && reservation.check_out > date;
    });
}

function statusClass(status: string): string {
    if (status === 'confirmed' || status === 'checked_in') return 'bg-blue-100 text-blue-800';
    if (status === 'pending') return 'bg-orange-100 text-orange-800';
    return 'bg-slate-100 text-slate-700';
}

defineOptions({ layout: { breadcrumbs: [{ title: 'Calendar', href: '/calendar' }] } });
</script>

<template>
    <Head title="Calendar" />
    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">Calendar</h1>
            <p class="text-sm text-muted-foreground">Fourteen-day room calendar for occupancy scanning.</p>
        </div>
        <section class="overflow-x-auto rounded-md border bg-card shadow-sm">
            <div class="min-w-[980px]">
                <div class="grid border-b" :style="{ gridTemplateColumns: `160px repeat(${days.length}, minmax(88px, 1fr))` }">
                    <div class="p-3 text-sm font-medium">Room</div>
                    <div v-for="day in days" :key="day" class="border-l p-3 text-xs font-medium text-muted-foreground">{{ day.slice(5) }}</div>
                </div>
                <div v-for="room in props.rooms" :key="room.id" class="grid border-b last:border-0" :style="{ gridTemplateColumns: `160px repeat(${days.length}, minmax(88px, 1fr))` }">
                    <div class="p-3">
                        <div class="font-medium">{{ room.number }}</div>
                        <div class="text-xs text-muted-foreground">{{ room.roomType?.name ?? room.room_type?.name }}</div>
                    </div>
                    <div v-for="day in days" :key="`${room.id}-${day}`" class="min-h-16 border-l p-2">
                        <template v-if="reservationFor(room.id, day)">
                            <div class="rounded-md px-2 py-1 text-xs font-medium" :class="statusClass(reservationFor(room.id, day)?.status ?? '')">
                                {{ reservationFor(room.id, day)?.code }}
                            </div>
                        </template>
                        <div v-else-if="room.status === 'maintenance'" class="rounded-md bg-slate-200 px-2 py-1 text-xs text-slate-700">Maintenance</div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
