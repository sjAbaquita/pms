<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { CircleCheckBig } from '@lucide/vue';
import type { Reservation } from '@/types/pms';
import { useCurrency } from '@/composables/useCurrency';

const props = defineProps<{ reservation: Reservation }>();
const { money } = useCurrency();
const rooms = props.reservation.reservationRooms ?? props.reservation.reservation_rooms ?? [];
</script>

<template>
    <main class="min-h-screen bg-slate-50 px-4 py-10 text-slate-950">
        <section class="mx-auto max-w-3xl rounded-md border bg-white p-6 shadow-sm md:p-8">
            <CircleCheckBig class="mb-4 size-12 text-teal-700" />
            <h1 class="text-3xl font-semibold">Reservation received</h1>
            <p class="mt-2 text-slate-500">Your booking is pending staff confirmation. Please keep this reference number.</p>

            <div class="mt-6 rounded-md bg-slate-50 p-4">
                <div class="text-sm text-slate-500">Reference</div>
                <div class="text-2xl font-semibold">{{ reservation.code }}</div>
            </div>

            <dl class="mt-6 grid gap-4 sm:grid-cols-2">
                <div><dt class="text-sm text-slate-500">Guest</dt><dd class="font-medium">{{ reservation.guest.first_name }} {{ reservation.guest.last_name }}</dd></div>
                <div><dt class="text-sm text-slate-500">Status</dt><dd class="font-medium capitalize">{{ reservation.status.replace('_', ' ') }}</dd></div>
                <div><dt class="text-sm text-slate-500">Dates</dt><dd class="font-medium">{{ reservation.check_in }} to {{ reservation.check_out }}</dd></div>
                <div><dt class="text-sm text-slate-500">Total</dt><dd class="font-medium">{{ money(reservation.grand_total) }}</dd></div>
                <div v-for="item in rooms" :key="item.id" class="sm:col-span-2"><dt class="text-sm text-slate-500">Room</dt><dd class="font-medium">Room {{ item.room.number }} · {{ item.room.room_type?.name ?? item.room.roomType?.name }}</dd></div>
            </dl>

            <div class="mt-8 flex flex-wrap gap-3">
                <Link href="/booking" class="rounded-md bg-teal-700 px-4 py-2 font-semibold text-white hover:bg-teal-800">Book another stay</Link>
                <Link href="/login" class="rounded-md border px-4 py-2 font-semibold hover:bg-slate-50">Staff login</Link>
            </div>
        </section>
    </main>
</template>
