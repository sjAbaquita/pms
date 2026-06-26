<script setup lang="ts">
import { Link, router, useForm } from '@inertiajs/vue3';
import { CalendarDays, Hotel, Search, ShieldCheck, Users } from '@lucide/vue';
import type { RoomType } from '@/types/pms';
import { useCurrency } from '@/composables/useCurrency';

const props = defineProps<{ roomTypes: RoomType[] }>();
const { money } = useCurrency();

const form = useForm({
    check_in: '',
    check_out: '',
    guests: 2,
});

function searchAvailability() {
    router.get('/booking/available', form.data(), {
        preserveState: true,
        preserveScroll: true,
    });
}
</script>

<template>
    <main class="min-h-screen bg-slate-50 text-slate-950">
        <section class="border-b bg-white">
            <div class="mx-auto grid max-w-7xl gap-8 px-4 py-8 lg:grid-cols-[1.1fr_0.9fr] lg:px-8">
                <div class="flex min-h-[430px] flex-col justify-between rounded-md bg-[linear-gradient(135deg,#0f766e,#155e75)] p-6 text-white md:p-8">
                    <div class="flex items-center justify-between gap-4">
                        <div class="text-lg font-semibold tracking-wide">Bugnaw Si-e Beach Resort</div>
                        <Link href="/login" class="rounded-md bg-white/15 px-3 py-2 text-sm font-medium hover:bg-white/25">Staff login</Link>
                    </div>
                    <div class="max-w-2xl">
                        <p class="mb-3 text-sm font-medium uppercase tracking-wide text-teal-100">Online reservations</p>
                        <h1 class="text-4xl font-semibold leading-tight md:text-6xl">Book a clear, calm beach stay.</h1>
                        <p class="mt-5 max-w-xl text-base leading-7 text-teal-50">Check live room availability, compare resort room types, and reserve with database-backed rates for guests, mattresses, coupons, and seasonal pricing.</p>
                    </div>
                </div>

                <form class="self-end rounded-md border bg-white p-5 shadow-sm" @submit.prevent="searchAvailability">
                    <div class="mb-5 flex items-center gap-3">
                        <div class="rounded-md bg-teal-50 p-2 text-teal-700"><Search class="size-5" /></div>
                        <div>
                            <h2 class="text-lg font-semibold">Find available rooms</h2>
                            <p class="text-sm text-slate-500">Rates update from room rules and seasons.</p>
                        </div>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="grid gap-2 text-sm font-medium">Check-in<input v-model="form.check_in" type="date" class="rounded-md border px-3 py-2" required /></label>
                        <label class="grid gap-2 text-sm font-medium">Check-out<input v-model="form.check_out" type="date" class="rounded-md border px-3 py-2" required /></label>
                        <label class="grid gap-2 text-sm font-medium sm:col-span-2">Guests<input v-model.number="form.guests" type="number" min="1" max="12" class="rounded-md border px-3 py-2" required /></label>
                    </div>
                    <button class="mt-5 flex w-full items-center justify-center gap-2 rounded-md bg-teal-700 px-4 py-3 font-semibold text-white hover:bg-teal-800" type="submit">
                        <Search class="size-4" /> Search availability
                    </button>
                </form>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 py-8 lg:px-8">
            <div class="mb-5 flex items-end justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-semibold">Accommodation types</h2>
                    <p class="text-sm text-slate-500">Pricing and capacity rules are stored in the database.</p>
                </div>
            </div>
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <article v-for="type in props.roomTypes" :key="type.id" class="rounded-md border bg-white p-5 shadow-sm">
                    <div class="mb-4 flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold">{{ type.name }}</h3>
                            <p class="mt-1 text-sm leading-6 text-slate-500">{{ type.description }}</p>
                        </div>
                        <Hotel class="mt-1 size-5 text-teal-700" />
                    </div>
                    <div class="grid gap-2 text-sm text-slate-600">
                        <div class="flex items-center gap-2"><Users class="size-4" /> {{ type.base_capacity }} base guests, up to {{ type.max_guests }}</div>
                        <div class="flex items-center gap-2"><CalendarDays class="size-4" /> {{ money(type.base_rate) }} per night</div>
                        <div class="flex items-center gap-2"><ShieldCheck class="size-4" /> {{ type.max_extra_mattresses }} extra mattresses allowed</div>
                    </div>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <span v-for="amenity in type.amenities" :key="amenity.id" class="rounded-md bg-slate-100 px-2 py-1 text-xs font-medium text-slate-700">{{ amenity.name }}</span>
                    </div>
                </article>
            </div>
        </section>
    </main>
</template>
