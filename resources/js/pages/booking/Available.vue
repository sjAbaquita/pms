<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, BedDouble, CheckCircle2 } from '@lucide/vue';
import type { AvailableRoom } from '@/types/pms';
import { useCurrency } from '@/composables/useCurrency';

const props = defineProps<{
    filters: { check_in: string; check_out: string; guests: number };
    rooms: AvailableRoom[];
}>();

const { money } = useCurrency();

const form = useForm({
    room_id: props.rooms[0]?.id ?? 0,
    check_in: props.filters.check_in,
    check_out: props.filters.check_out,
    adults: Number(props.filters.guests || 1),
    children: 0,
    extra_mattresses: 0,
    coupon_code: '',
    special_requests: '',
    source: 'online',
    guest: {
        first_name: '',
        last_name: '',
        email: '',
        phone: '',
        address: '',
        notes: '',
    },
});

function selectedRoom(): AvailableRoom | undefined {
    return props.rooms.find((room) => room.id === Number(form.room_id));
}

function submit() {
    form.post('/reservations');
}
</script>

<template>
    <main class="min-h-screen bg-slate-50 text-slate-950">
        <div class="mx-auto max-w-7xl px-4 py-8 lg:px-8">
            <Link href="/booking" class="mb-6 inline-flex items-center gap-2 text-sm font-medium text-teal-700"><ArrowLeft class="size-4" /> Back to search</Link>

            <div class="mb-6 flex flex-col justify-between gap-3 md:flex-row md:items-end">
                <div>
                    <h1 class="text-3xl font-semibold">Available rooms</h1>
                    <p class="text-slate-500">{{ filters.check_in }} to {{ filters.check_out }} for {{ filters.guests }} guest(s)</p>
                </div>
                <div class="rounded-md border bg-white px-4 py-3 text-sm text-slate-600">{{ rooms.length }} rooms matched</div>
            </div>

            <div v-if="rooms.length === 0" class="rounded-md border bg-white p-8 text-center shadow-sm">
                <h2 class="text-xl font-semibold">No rooms available for those dates</h2>
                <p class="mt-2 text-slate-500">Try another date range or reduce the guest count.</p>
            </div>

            <div v-else class="grid gap-6 lg:grid-cols-[1fr_420px]">
                <div class="grid gap-4">
                    <button
                        v-for="room in rooms"
                        :key="room.id"
                        class="rounded-md border bg-white p-5 text-left shadow-sm transition hover:border-teal-500"
                        :class="Number(form.room_id) === room.id ? 'border-teal-600 ring-2 ring-teal-100' : ''"
                        type="button"
                        @click="form.room_id = room.id"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-2 text-lg font-semibold"><BedDouble class="size-5 text-teal-700" /> Room {{ room.number }}</div>
                                <p class="mt-1 text-sm text-slate-500">{{ room.room_type.name }} · up to {{ room.room_type.max_guests }} guests</p>
                            </div>
                            <div class="text-right">
                                <div class="text-xl font-semibold">{{ money(room.quote.grand_total) }}</div>
                                <div class="text-xs text-slate-500">{{ room.quote.nights }} night(s)</div>
                            </div>
                        </div>
                        <div class="mt-4 grid gap-2 text-sm text-slate-600 sm:grid-cols-3">
                            <span>Room: {{ money(room.quote.room_total) }}</span>
                            <span>Extra guests: {{ money(room.quote.extra_guest_total) }}</span>
                            <span>Mattresses: {{ money(room.quote.extra_mattress_total) }}</span>
                        </div>
                    </button>
                </div>

                <form class="rounded-md border bg-white p-5 shadow-sm" @submit.prevent="submit">
                    <div class="mb-5">
                        <h2 class="text-lg font-semibold">Guest details</h2>
                        <p class="text-sm text-slate-500">Selected: Room {{ selectedRoom()?.number }}</p>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="grid gap-2 text-sm font-medium">First name<input v-model="form.guest.first_name" class="rounded-md border px-3 py-2" required /></label>
                        <label class="grid gap-2 text-sm font-medium">Last name<input v-model="form.guest.last_name" class="rounded-md border px-3 py-2" required /></label>
                        <label class="grid gap-2 text-sm font-medium">Email<input v-model="form.guest.email" type="email" class="rounded-md border px-3 py-2" /></label>
                        <label class="grid gap-2 text-sm font-medium">Phone<input v-model="form.guest.phone" class="rounded-md border px-3 py-2" /></label>
                        <label class="grid gap-2 text-sm font-medium">Adults<input v-model.number="form.adults" type="number" min="1" class="rounded-md border px-3 py-2" /></label>
                        <label class="grid gap-2 text-sm font-medium">Children<input v-model.number="form.children" type="number" min="0" class="rounded-md border px-3 py-2" /></label>
                        <label class="grid gap-2 text-sm font-medium">Extra mattresses<input v-model.number="form.extra_mattresses" type="number" min="0" class="rounded-md border px-3 py-2" /></label>
                        <label class="grid gap-2 text-sm font-medium">Coupon<input v-model="form.coupon_code" class="rounded-md border px-3 py-2 uppercase" /></label>
                        <label class="grid gap-2 text-sm font-medium sm:col-span-2">Special requests<textarea v-model="form.special_requests" class="min-h-24 rounded-md border px-3 py-2" /></label>
                    </div>
                    <button class="mt-5 flex w-full items-center justify-center gap-2 rounded-md bg-teal-700 px-4 py-3 font-semibold text-white hover:bg-teal-800" :disabled="form.processing" type="submit">
                        <CheckCircle2 class="size-4" /> Reserve room
                    </button>
                    <p v-if="Object.keys(form.errors).length" class="mt-3 text-sm text-red-600">Please review the highlighted booking details and try again.</p>
                </form>
            </div>
        </div>
    </main>
</template>
