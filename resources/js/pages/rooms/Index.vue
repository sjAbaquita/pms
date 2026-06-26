<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { BedDouble, Users } from '@lucide/vue';
import { useCurrency } from '@/composables/useCurrency';
import type { RoomType } from '@/types/pms';

const props = defineProps<{ roomTypes: RoomType[] }>();
const { money } = useCurrency();

defineOptions({ layout: { breadcrumbs: [{ title: 'Rooms', href: '/rooms' }] } });
</script>

<template>
    <Head title="Rooms" />
    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">Room inventory</h1>
            <p class="text-sm text-muted-foreground">Manage accommodation categories first, then individual rooms.</p>
        </div>
        <div class="grid gap-4 lg:grid-cols-2">
            <article v-for="type in props.roomTypes" :key="type.id" class="rounded-md border bg-card p-5 shadow-sm">
                <div class="mb-4 flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold">{{ type.name }}</h2>
                        <p class="mt-1 text-sm text-muted-foreground">{{ type.description }}</p>
                    </div>
                    <BedDouble class="size-5 text-teal-700" />
                </div>
                <div class="mb-4 grid gap-2 text-sm text-muted-foreground sm:grid-cols-3">
                    <span>{{ money(type.base_rate) }} base</span>
                    <span class="flex items-center gap-1"><Users class="size-4" /> {{ type.base_capacity }}-{{ type.max_guests }} guests</span>
                    <span>{{ type.rooms_count ?? type.rooms?.length ?? 0 }} rooms</span>
                </div>
                <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                    <div v-for="room in type.rooms" :key="room.id" class="rounded-md border p-3">
                        <div class="font-medium">Room {{ room.number }}</div>
                        <div class="text-xs capitalize text-muted-foreground">{{ room.status }}</div>
                    </div>
                </div>
            </article>
        </div>
    </div>
</template>
