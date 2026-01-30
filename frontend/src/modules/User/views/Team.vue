<template>
  <v-container fluid class="fill-height align-start pa-6 bg-grey-lighten-4">
    <v-row>
      <v-col cols="12" class="d-flex justify-space-between align-center mb-6">
        <h1 class="text-h4 font-weight-bold">Team Management</h1>
        <v-btn
            color="primary"
            height="44"
            class="text-capitalize px-6"
            elevation="0"
            @click="router.push('/members/create')"
        >
          + Add Member
        </v-btn>
      </v-col>

      <v-col cols="12">
        <v-card class="rounded-xl" elevation="0" border>
            <v-data-table
                :headers="headers"
                :items="userStore.members"
                :loading="userStore.loading"
                hover
                class="pa-2"
            >
                <!-- Name Column -->
                <template v-slot:item.name="{ item }">
                    <span class="font-weight-medium">{{ item.name }}</span>
                </template>

                <!-- Role Column -->
                <template v-slot:item.role="{ item }">
                    <v-chip
                        :color="item.role === 'admin' ? 'purple-lighten-4' : 'blue-lighten-4'"
                        :text-color="item.role === 'admin' ? 'purple-darken-2' : 'blue-darken-2'"
                        class="font-weight-bold text-capitalize"
                        size="small"
                        label
                        variant="flat"
                    >
                        {{ item.role === 'admin' ? 'Admin' : 'User' }}
                    </v-chip>
                </template>

                 <!-- Wallet Access Column -->
                <template v-slot:item.wallet_access="{ item }">
                    <div class="d-flex flex-wrap gap-1">
                        <template v-if="Array.isArray(item.wallet_access)">
                             <v-chip
                                v-for="(access, i) in item.wallet_access"
                                :key="i"
                                color="grey-lighten-3"
                                class="text-grey-darken-3 font-weight-medium mr-1 my-1"
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
                    <div class="d-flex justify-end">
                        <v-btn
                            icon="mdi-pencil"
                            variant="text"
                            size="small"
                            color="primary"
                            class="mr-1"
                            @click="router.push(`/members/${item.id}/edit`)"
                        ></v-btn>
                        <!-- Existing Delete Logic -->
                        <v-btn
                            icon="mdi-delete"
                            size="small"
                            variant="text"
                            color="error"
                             @click="confirmDelete(item)"
                             v-if="item.id !== userStore.currentUser?.id"
                        ></v-btn>
                    </div>
                </template>
            </v-data-table>
        </v-card>
      </v-col>
    </v-row>

    <!-- Simple Delete Confirmation -->
    <v-dialog v-model="showDeleteDialog" max-width="400">
       <v-card class="rounded-xl">
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

  </v-container>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useUserStore } from '@/modules/User/store';
import type { User } from '@/modules/User/api'; // Import type if needed for memberToDelete

const router = useRouter();
const userStore = useUserStore();
const showDeleteDialog = ref(false);
const memberToDelete = ref<any>(null); // Using any to avoid import issues if not handy, or match store type
const deleteLoading = ref(false);

const headers = [
  { title: 'ID', key: 'id', align: 'start' as const },
  { title: 'Name', key: 'name', align: 'start' as const },
  { title: 'Email', key: 'email', align: 'start' as const },
  { title: 'Role', key: 'role', align: 'start' as const },
  { title: 'Wallet Access', key: 'wallet_access', align: 'start' as const },
  { title: 'Joined', key: 'created_at', align: 'start' as const },
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

onMounted(() => {
  userStore.fetchMembers();
  // Ensure we have current user to hide delete button on self
  if(!userStore.currentUser) {
      userStore.fetchCurrentUser();
  }
});
</script>
