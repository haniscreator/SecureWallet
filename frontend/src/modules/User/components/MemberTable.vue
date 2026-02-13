<template>
    <v-card class="rounded-0" elevation="0" border>
        <v-data-table
            :headers="headers"
            :items="members"
            :loading="loading"
            :items-per-page="itemsPerPage"
            v-model:page="page"
            hover
            class="member-table"
        >
            <!-- Name Column -->
            <template v-slot:item.name="{ item }">
                <span class="font-weight-medium">{{ item.name }}</span>
            </template>

            <!-- Role Column -->
            <template v-slot:item.role="{ item }">
                <v-chip
                    :color="item.role === 'admin' ? '#EBEBEF' : (item.role === 'manager' ? '#FFF3E0' : '#D6E3F7')"
                    :text-color="item.role === 'admin' ? '#333' : (item.role === 'manager' ? '#E65100' : '#1976D2')"
                    class="font-weight-bold text-capitalize"
                    size="small"
                    label
                    variant="flat"
                    style="color: inherit"
                >
                    {{ item.role === 'admin' ? 'Admin' : (item.role === 'manager' ? 'Manager' : 'User') }}
                </v-chip>
            </template>

                <!-- Wallet Access Column -->
            <template v-slot:item.wallet_access="{ item }">
                <div class="d-flex flex-wrap gap-1">
                    <template v-if="Array.isArray(item.wallet_access)">
                            <v-chip
                            v-for="(access, i) in item.wallet_access"
                            :key="i"
                            color="#DBE8E3"
                            class="text-grey-darken-3 font-weight-bold mr-1 my-1"
                            size="small"
                            label
                            variant="flat"
                        >
                            {{ access }}
                        </v-chip>
                            <span v-if="item.wallet_access.length === 0" class="text-caption text-grey">None</span>
                    </template>
                    <span v-else class="text-caption text-grey">-</span>
                </div>
            </template>

            <!-- Joined Column -->
            <template v-slot:item.created_at="{ item }">
                <span class="text-grey-darken-1">
                    {{ item.created_at ? new Date(item.created_at).toLocaleDateString() : '-' }}
                </span>
            </template>

            <!-- Actions Column -->
            <template v-slot:item.actions="{ item }">
                <div class="d-flex justify-end gap-2">
                        <v-btn
                            variant="text"
                            size="small"
                            :color="isAdmin ? 'primary' : 'info'"
                            class="font-weight-bold"
                            @click="router.push(`/members/${item.id}/edit`)"
                        >
                            {{ isAdmin ? 'EDIT' : 'VIEW' }} <v-icon size="small" class="ml-1">{{ isAdmin ? 'mdi-pencil' : 'mdi-eye' }}</v-icon>
                        </v-btn>
                        
                    <!-- Delete (Admin only) -->
                        <v-btn
                            v-if="isAdmin"
                            size="small"
                            variant="text"
                            color="error"
                            class="font-weight-bold"
                            @click="confirmDelete(item)"
                        >
                            DELETE <v-icon size="small" class="ml-1">mdi-delete</v-icon>
                        </v-btn>
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

     <!-- Simple Delete Confirmation -->
     <v-dialog v-model="showDeleteDialog" max-width="400">
        <v-card class="rounded-0">
          <v-card-title class="pa-4 text-h6">Confirm Delete</v-card-title>
          <v-card-text class="pa-4 pt-0">
              Are you sure you want to remove <strong>{{ memberToDelete?.name }}</strong>?
          </v-card-text>
          <v-card-actions class="pa-4 pt-0">
            <v-spacer></v-spacer>
            <v-btn variant="text" class="text-capitalize" @click="showDeleteDialog = false">Cancel</v-btn>
            <v-btn color="error" class="text-capitalize" variant="flat" @click="deleteUser" :loading="deleteLoading">Delete</v-btn>
          </v-card-actions>
        </v-card>
     </v-dialog>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useUserStore } from '@/modules/User/store';

const props = defineProps<{
    members: any[];
    loading: boolean;
}>();

const router = useRouter();
const userStore = useUserStore();

const page = ref(1);
const itemsPerPage = 10;
const showDeleteDialog = ref(false);
const memberToDelete = ref<any>(null);
const deleteLoading = ref(false);

const isAdmin = computed(() => userStore.currentUser?.role === 'admin');

const totalCount = computed(() => props.members.length);
const totalPages = computed(() => Math.ceil(totalCount.value / itemsPerPage) || 1);
const currentCount = computed(() => {
    const remaining = totalCount.value - (page.value - 1) * itemsPerPage;
    return remaining > 0 ? Math.min(itemsPerPage, remaining) : 0;
});

const headers = [
  { title: 'ID', key: 'id', align: 'start' as const },
  { title: 'Name', key: 'name', align: 'start' as const },
  { title: 'Email', key: 'email', align: 'start' as const },
  { title: 'Role', key: 'role', align: 'start' as const },
  { title: 'Wallet Access', key: 'wallet_access', align: 'start' as const },
  { title: 'Actions', key: 'actions', align: 'end' as const, sortable: false },
];

function confirmDelete(member: any) {
    memberToDelete.value = member;
    showDeleteDialog.value = true;
}

async function deleteUser() {
    if (!memberToDelete.value) return;
    deleteLoading.value = true;
    try {
        await userStore.deleteMember(memberToDelete.value.id);
        showDeleteDialog.value = false;
    } catch(e) {
        console.error(e);
    } finally {
        deleteLoading.value = false;
        memberToDelete.value = null;
    }
}
</script>

<style scoped>
/* Hide the items-per-page dropdown in the footer */
:deep(.member-table .v-data-table-footer__items-per-page) {
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
:deep(.member-table thead tr th) {
    background-color: #F5F6F9 !important;
}
</style>
