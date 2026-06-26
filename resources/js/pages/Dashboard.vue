<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Banknote, BedDouble, CalendarCheck, CalendarClock, DoorOpen, Hotel, UsersRound } from '@lucide/vue';
import { useCurrency } from '@/composables/useCurrency';
import type { Reservation } from '@/types/pms';

const props = defineProps<{
    metrics: {
        arrivalsToday: number;
        departuresToday: number;
        occupiedRooms: number;
        availableRooms: number;
        pendingReservations: number;
        revenueToday: number;
        revenueMonth: number;
    };
    upcomingReservations: Reservation[];
    monthlyBookings: { month: string; total: number }[];
}>();

const { money } = useCurrency();

const cards = [
    { label: 'Arrivals today', value: props.metrics.arrivalsToday, icon: CalendarCheck },
    { label: 'Departures today', value: props.metrics.departuresToday, icon: DoorOpen },
    { label: 'Occupied rooms', value: props.metrics.occupiedRooms, icon: BedDouble },
    { label: 'Available rooms', value: props.metrics.availableRooms, icon: Hotel },
    { label: 'Pending reservations', value: props.metrics.pendingReservations, icon: CalendarClock },
    { label: 'Revenue today', value: money(props.metrics.revenueToday), icon: Banknote },
    { label: 'Revenue this month', value: money(props.metrics.revenueMonth), icon: Banknote },
];

defineOptions({
    layout: { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }] },
});
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">Resort dashboard</h1>
            <p class="text-sm text-muted-foreground">Daily operations snapshot for Bugnaw Si-e Beach Resort.</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <article v-for="card in cards" :key="card.label" class="rounded-md border bg-card p-4 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm text-muted-foreground">{{ card.label }}</p>
                        <p class="mt-2 text-2xl font-semibold">{{ card.value }}</p>
                    </div>
                    <component :is="card.icon" class="size-5 text-teal-700" />
                </div>
            </article>
        </div>

        <div class="grid gap-4 xl:grid-cols-[1fr_360px]">
            <section class="rounded-md border bg-card p-4 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="font-semibold">Upcoming reservations</h2>
                    <UsersRound class="size-5 text-teal-700" />
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="border-b text-left text-muted-foreground">
                            <tr><th class="py-2">Code</th><th>Guest</th><th>Room</th><th>Check-in</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                            <tr v-for="reservation in upcomingReservations" :key="reservation.id" class="border-b last:border-0">
                                <td class="py-3 font-medium">{{ reservation.code }}</td>
                                <td>{{ reservation.guest.first_name }} {{ reservation.guest.last_name }}</td>
                                <td>{{ (reservation.reservationRooms ?? reservation.reservation_rooms ?? [])[0]?.room.number }}</td>
                                <td>{{ reservation.check_in }}</td>
                                <td class="capitalize">{{ reservation.status.replace('_', ' ') }}</td>
                            </tr>
                            <tr v-if="upcomingReservations.length === 0"><td colspan="5" class="py-8 text-center text-muted-foreground">No upcoming reservations yet.</td></tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="rounded-md border bg-card p-4 shadow-sm">
                <h2 class="mb-4 font-semibold">Monthly bookings</h2>
                <div class="grid gap-3">
                    <div v-for="item in monthlyBookings" :key="item.month">
                        <div class="mb-1 flex justify-between text-sm"><span>{{ item.month }}</span><span>{{ item.total }}</span></div>
                        <div class="h-2 rounded bg-muted"><div class="h-2 rounded bg-teal-700" :style="{ width: `${Math.min(100, item.total * 12)}%` }" /></div>
                    </div>
                    <p v-if="monthlyBookings.length === 0" class="text-sm text-muted-foreground">No booking history yet.</p>
                </div>
            </section>
        </div>
    </div>
</template>
