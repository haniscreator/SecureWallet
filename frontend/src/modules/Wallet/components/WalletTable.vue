<template>
    <v-card class="rounded-0" elevation="0" border>
        <v-data-table
            :headers="headers"
            :items="wallets"
            :loading="loading"
            :items-per-page="itemsPerPage"
            v-model:page="page"
            hover
            class="wallet-table"
        >
            <!-- ID Column -->
            <template v-slot:item.id="{ item }">
                <span class="font-weight-bold">{{ item.id }}</span>
            </template>

            <!-- Name Column -->
            <template v-slot:item.name="{ item }">
                <span class="font-weight-bold">{{ item.name }}</span>
            </template>

            <!-- Currency Column -->
            <template v-slot:item.currency="{ item }">
                <span class="font-weight-medium">{{ item.currency?.code || 'N/A' }}</span>
            </template>

                <!-- Balance Column -->
            <template v-slot:item.balance="{ item }">
                <span class="font-weight-bold text-black">
                    {{ item.currency?.symbol || '' }}{{ Number(item.balance).toLocaleString('en-US', { minimumFractionDigits: 2 }) }}
                </span>
            </template>

            <!-- Status Column -->
            <template v-slot:item.status="{ item }">
                <!-- Active: No border radius, bold -->
                <v-chip
                    v-if="item.status"
                    color="success"
                    size="small"
                    variant="tonal"
                    class="font-weight-bold rounded-0"
                >
                    Active
                </v-chip>
                <!-- Inactive: No border, bold, bg #D2E1F5 -->
                <v-chip
                    v-else
                    color="#D2E1F5"
                    size="small"
                    variant="flat"
                    class="font-weight-bold rounded-0"
                    style="color: #386DB1;"
                >
                    Inactive
                </v-chip>
            </template>

            <!-- User Access Column -->
            <template v-slot:item.users="{ item }">
                <div v-if="item.users && item.users.length > 0">
                        <div class="d-flex flex-wrap gap-1">
                        <v-chip v-for="user in item.users.slice(0, 3)" :key="user.id" size="x-small" density="comfortable">
                            {{ user.name }}
                        </v-chip>
                        <v-chip v-if="item.users.length > 3" size="x-small" density="comfortable" variant="outlined">
                            +{{ item.users.length - 3 }} more
                        </v-chip>
                        </div>
                </div>
                <span v-else class="text-grey text-caption">No users assigned</span>
            </template>

            <template v-slot:item.actions="{ item }">
                <div class="d-flex gap-2 justify-center">
                        <v-btn
                        variant="text"
                        size="small"
                        color="primary"
                        class="font-weight-bold"
                        @click="$router.push({ name: 'WalletEdit', params: { id: item.id } })"
                    >
                        EDIT <v-icon size="small" class="ml-1">mdi-pencil</v-icon>
                    </v-btn>
                </div>
            </template>
            
            <!-- No Data -->
            <template v-slot:no-data>
                <div class="pa-4 text-center text-grey">
                    No wallets found.
                </div>
            </template>
            <template v-slot:bottom>
                    <v-divider></v-divider>
                    <div class="d-flex align-center justify-space-between pa-4">
                    <div class="text-caption text-grey">Showing {{ currentCount }} of {{ totalCount }}</div>
                    <div class="d-flex align-center">
                        <v-pagination
                            v-model="page"
                            :length="totalPages"
                            total-visible="3"
                            density="compact"
                            active-color="primary"
                            variant="flat"
                            class="details-pagination"
                        ></v-pagination>
                    </div>
                </div>
            </template>
        </v-data-table>
    </v-card>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';

const props = defineProps<{
    wallets: any[];
    loading: boolean;
}>();

const page = ref(1);
const itemsPerPage = 10;

const totalCount = computed(() => props.wallets.length);
const totalPages = computed(() => Math.ceil(totalCount.value / itemsPerPage) || 1);
const currentCount = computed(() => {
    const remaining = totalCount.value - (page.value - 1) * itemsPerPage;
    return remaining > 0 ? Math.min(itemsPerPage, remaining) : 0;
});

const headers = [
    { title: 'ID', key: 'id', align: 'start' as const },
    { title: 'Name', key: 'name', align: 'start' as const },
    { title: 'Currency', key: 'currency', align: 'start' as const, sortable: false },
    { title: 'Balance', key: 'balance', align: 'end' as const },
    { title: 'Status', key: 'status', align: 'start' as const },
    { title: 'User Access', key: 'users', align: 'start' as const, sortable: false },
    { title: 'Actions', key: 'actions', sortable: false, align: 'center' as const },
];
</script>

<style scoped>
/* Hide the items-per-page dropdown in the footer */
:deep(.wallet-table .v-data-table-footer__items-per-page) {
    display: none !important;
}

.details-pagination :deep(.v-pagination__list) {
    margin-bottom: 0;
}

.details-pagination :deep(.v-pagination__item--is-active) {
    background-color: rgb(var(--v-theme-primary)) !important;
    color: white !important;
}

/* Header Background Color */
:deep(.wallet-table thead tr th) {
    background-color: #F5F6F9 !important;
}
</style>
