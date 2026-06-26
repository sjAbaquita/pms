<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { useCurrency } from '@/composables/useCurrency';
import type { Reservation } from '@/types/pms';

type Paginated<T> = {
    data: T[];
    links: { url: string | null; label: string; active: boolean }[];
};

const props = defineProps<{ reservations: Paginated<Reservation> }>();
const { money } = useCurrency();

defineOptions({ layout: { breadcrumbs: [{ title: 'Reservations', href: '/reservations' }] } });
</script>

<template>
    <Head title="Reservations" />
    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">Reservations</h1>
            <p class="text-sm text-muted-foreground">Track pending, confirmed, checked-in, checked-out, cancelled, and no-show bookings.</p>
        </div>
        <section class="rounded-md border bg-card shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b text-left text-muted-foreground">
                        <tr><th class="p-4">Code</th><th>Guest</th><th>Room</th><th>Dates</th><th>Status</th><th class="text-right">Total</th></tr>
                    </thead>
                    <tbody>
                        <tr v-for="reservation in props.reservations.data" :key="reservation.id" class="border-b last:border-0">
                            <td class="p-4 font-medium">{{ reservation.code }}</td>
                            <td>{{ reservation.guest.first_name }} {{ reservation.guest.last_name }}</td>
                            <td>{{ (reservation.reservationRooms ?? reservation.reservation_rooms ?? [])[0]?.room.number }}</td>
                            <td>{{ reservation.check_in }} to {{ reservation.check_out }}</td>
                            <td><span class="rounded-md bg-slate-100 px-2 py-1 text-xs font-medium capitalize">{{ reservation.status.replace('_', ' ') }}</span></td>
                            <td class="text-right font-medium">{{ money(reservation.grand_total) }}</td>
                        </tr>
                        <tr v-if="props.reservations.data.length === 0"><td colspan="6" class="p-8 text-center text-muted-foreground">No reservations yet.</td></tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</template>
