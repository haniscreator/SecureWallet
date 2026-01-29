<template>
  <v-container>
    <v-row class="mb-4">
      <v-col cols="12" class="d-flex justify-space-between align-center">
        <h1 class="text-h4 font-weight-bold">Team Management</h1>
        <v-btn color="primary" prepend-icon="mdi-account-plus" @click="showAddDialog = true">
          Add Member
        </v-btn>
      </v-col>
    </v-row>

    <v-card variant="outlined">
      <v-data-table
        :headers="headers"
        :items="userStore.members"
        :loading="userStore.loading"
      >
        <template v-slot:item.role="{ item }">
           <v-chip :color="item.role === 'admin' ? 'purple' : 'blue'" size="small" label class="text-uppercase">
             {{ item.role }}
           </v-chip>
        </template>
        
        <template v-slot:item.created_at="{ item }">
           {{ item.created_at ? new Date(item.created_at).toLocaleDateString() : '-' }}
        </template>

        <template v-slot:item.actions="{ item }">
           <v-btn 
             icon="mdi-delete" 
             size="small" 
             variant="text" 
             color="error"
             @click="confirmDelete(item.id)"
             v-if="item.id !== userStore.currentUser?.id"
           ></v-btn>
        </template>
      </v-data-table>
    </v-card>

    <AddMemberDialog v-model="showAddDialog" @created="userStore.fetchMembers()" />

    <!-- Simple Delete Confirmation -->
    <v-dialog v-model="showDeleteDialog" max-width="400">
       <v-card>
         <v-card-title>Confirm Delete</v-card-title>
         <v-card-text>Are you sure you want to remove this member?</v-card-text>
         <v-card-actions>
           <v-spacer></v-spacer>
           <v-btn variant="text" @click="showDeleteDialog = false">Cancel</v-btn>
           <v-btn color="error" @click="deleteUser" :loading="deleteLoading">Delete</v-btn>
         </v-card-actions>
       </v-card>
    </v-dialog>

  </v-container>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useUserStore } from '@/modules/User/store';
import AddMemberDialog from '@/modules/User/components/AddMemberDialog.vue';

const userStore = useUserStore();
const showAddDialog = ref(false);
const showDeleteDialog = ref(false);
const userToDelete = ref<number|null>(null);
const deleteLoading = ref(false);

const headers = [
  { title: 'Name', key: 'name' },
  { title: 'Email', key: 'email' },
  { title: 'Role', key: 'role' },
  { title: 'Joined', key: 'created_at' },
  { title: 'Actions', key: 'actions', align: 'end' as const, sortable: false },
];

function confirmDelete(id: number) {
    userToDelete.value = id;
    showDeleteDialog.value = true;
}

async function deleteUser() {
    if (!userToDelete.value) return;
    deleteLoading.value = true;
    try {
        await userStore.deleteMember(userToDelete.value);
        showDeleteDialog.value = false;
    } catch(e) {
        console.error(e);
    } finally {
        deleteLoading.value = false;
        userToDelete.value = null;
    }
}

onMounted(() => {
  userStore.fetchMembers();
  if(!userStore.currentUser) {
      userStore.fetchCurrentUser();
  }
});
</script>
