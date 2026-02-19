<template>
    <v-container fluid class="fill-height align-start pa-6">
        <v-row justify="center">
            <v-col cols="12" md="8" lg="6">
                
                <v-card class="rounded-0" border elevation="0">
                    <v-card-title class="pa-6 pb-4 text-h5 font-weight-bold">
                        {{ !isAdmin ? 'View Member' : (isEditMode ? 'Edit Member' : 'Add New Member') }}
                    </v-card-title>
                    <v-divider></v-divider>

                    <v-card-text class="pa-6">
                        <v-form ref="form" @submit.prevent="submit">
                            <v-row>
                                <!-- Name -->
                                <v-col cols="12">
                                    <div class="text-subtitle-2 font-weight-bold mb-2">Full Name <span v-if="isAdmin" class="text-red ml-1">*</span></div>
                                    <v-text-field
                                        v-model="formData.name"
                                        placeholder="Enter full name"
                                        variant="outlined"
                                        density="compact"
                                        :readonly="!isAdmin"
                                        :rules="isAdmin ? [rules.required] : []"
                                        :bg-color="!isAdmin ? 'grey-lighten-4' : undefined"
                                    ></v-text-field>
                                </v-col>

                                <!-- Email -->
                                <v-col cols="12">
                                    <div class="text-subtitle-2 font-weight-bold mb-2">Email Address <span v-if="isAdmin" class="text-red ml-1">*</span></div>
                                    <v-text-field
                                        v-model="formData.email"
                                        placeholder="Enter email address"
                                        variant="outlined"
                                        density="compact"
                                        type="email"
                                        :rules="isAdmin ? [rules.required, rules.email] : []"
                                        :disabled="isEditMode || !isAdmin" 
                                        :bg-color="(!isAdmin || isEditMode) ? 'grey-lighten-4' : undefined"
                                    ></v-text-field>
                                    <div v-if="isEditMode && isAdmin" class="text-caption text-grey mt-1">
                                        Email cannot be changed after creation.
                                    </div>
                                </v-col>

                                <!-- Password (Create Only & Admin Only) -->
                                <v-col cols="12" v-if="!isEditMode && isAdmin">
                                    <div class="text-subtitle-2 font-weight-bold mb-2">Password <span class="text-red ml-1">*</span></div>
                                    <v-text-field
                                        v-model="formData.password"
                                        placeholder="Enter password"
                                        variant="outlined"
                                        density="compact"
                                        type="password"
                                        :rules="[rules.required, rules.minLength]"
                                    ></v-text-field>
                                </v-col>

                                <!-- Role -->
                                <v-col cols="12">
                                    <div class="text-subtitle-2 font-weight-bold mb-2">Role <span v-if="isAdmin" class="text-red ml-1">*</span></div>
                                    <v-select
                                        v-model="formData.role"
                                        :items="roles"
                                        item-title="title"
                                        item-value="value"
                                        placeholder="Select role"
                                        variant="outlined"
                                        density="compact"
                                        :readonly="!isAdmin"
                                        :rules="isAdmin ? [rules.required] : []"
                                        :bg-color="!isAdmin ? 'grey-lighten-4' : undefined"
                                    ></v-select>
                                </v-col>

                                 <!-- Wallet Access -->
                                <v-col cols="12">
                                    <div class="text-subtitle-2 font-weight-bold mb-2">Wallet Access</div>
                                    <v-select
                                        v-model="formData.wallet_ids"
                                        :items="availableWallets"
                                        item-title="name"
                                        item-value="id"
                                        placeholder="Select wallets"
                                        variant="outlined"
                                        density="compact"
                                        multiple
                                        chips
                                        closable-chips
                                        :readonly="!isAdmin"
                                        :bg-color="!isAdmin ? 'grey-lighten-4' : undefined"
                                        :loading="loadingWallets"
                                    ></v-select>
                                </v-col>

                                <!-- Status -->
                                <v-col cols="12">
                                    <v-switch
                                        v-model="formData.status"
                                        color="primary"
                                        label="Active Status"
                                        hide-details
                                        :readonly="!isAdmin"
                                        :disabled="!isAdmin"
                                    ></v-switch>
                                </v-col>
                            </v-row>
                            
                            <v-divider class="mt-6 mb-6"></v-divider>

                            <!-- Actions -->
                            <div class="d-flex justify-end pt-4">
                                <v-btn
                                    variant="text"
                                    color="grey-darken-1"
                                    class="mr-4 text-capitalize"
                                    size="large"
                                    @click="router.back()"
                                >
                                     {{ isAdmin ? 'Cancel' : 'Back' }}
                                </v-btn>
                                <v-btn
                                    v-if="isAdmin"
                                    color="primary"
                                    type="submit"
                                    class="text-capitalize"
                                    size="large"
                                    elevation="0"
                                    :loading="loading"
                                >
                                    {{ isEditMode ? 'Save Changes' : 'Create Member' }}
                                </v-btn>
                            </div>
                        </v-form>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { userApi } from '../api';
import { walletApi, type Wallet } from '@/modules/Wallet/api';
import { useNotificationStore } from '@/shared/stores/notification';
import { useUserStore } from '@/modules/User/store';

const router = useRouter();
const route = useRoute();
const notification = useNotificationStore();
const userStore = useUserStore();

const form = ref<any>(null);
const loading = ref(false);
const loadingWallets = ref(false);
const isEditMode = computed(() => route.params.id !== undefined);
const isAdmin = computed(() => userStore.currentUser?.role === 'admin');
const availableWallets = ref<Wallet[]>([]);

const formData = ref({
    name: '',
    email: '',
    password: '',
    role: null as 'admin' | 'user' | 'manager' | null, 
    status: true,
    wallet_ids: [] as number[],
});

const roles = [
    { title: 'Administrator', value: 'admin' },
    { title: 'Manager', value: 'manager' },
    { title: 'User', value: 'user' },
];

const rules = {
    required: (v: any) => !!v || 'Field is required',
    email: (v: string) => /.+@.+\..+/.test(v) || 'Invalid email address',
    minLength: (v: string) => (v && v.length >= 8) || 'Password must be at least 8 characters',
};

onMounted(async () => {
    // Ensure user loaded to check role
    await userStore.fetchCurrentUser();

    try {
        loadingWallets.value = true;
        const { data } = await walletApi.getWallets();
        availableWallets.value = (data as any).data || data;
    } catch (e) {
        console.error('Failed to fetch wallets', e);
    } finally {
        loadingWallets.value = false;
    }

    if (isEditMode.value) {
        await loadMember();
    }
});

async function loadMember() {
    try {
        loading.value = true;
        const id = Number(route.params.id);
        const { data } = await userApi.getMember(id);
        
        const user = data.user || (data as any).data || data;

        if (user) {
             formData.value.name = user.name;
             formData.value.email = user.email;
             formData.value.role = user.role as any;
             formData.value.status = user.status ?? true;
             formData.value.wallet_ids = user.wallet_ids || [];
        }

    } catch (error) {
        console.error(error);
        notification.notify('Failed to load member details', 'error');
        router.push('/members');
    } finally {
        loading.value = false;
    }
}

async function submit() {
    if (!isAdmin.value) return;

    const { valid } = await form.value.validate();
    if (!valid) return;

    loading.value = true;
    try {
        if (isEditMode.value) {
             await userApi.updateMember(Number(route.params.id), {
                 name: formData.value.name,
                 role: formData.value.role || undefined, 
                 status: formData.value.status,
                 wallet_ids: formData.value.wallet_ids,
             });
             notification.notify('Member updated successfully', 'success');
        } else {
             await userApi.createMember({
                 name: formData.value.name,
                 email: formData.value.email,
                 password: formData.value.password,
                 role: formData.value.role || undefined,
                 status: formData.value.status,
                 wallet_ids: formData.value.wallet_ids,
             });
             notification.notify('Member created successfully', 'success');
        }
        router.push('/members');
    } catch (error: any) {
        console.error(error);
        notification.notify(error.message || 'Operation failed', 'error');
    } finally {
        loading.value = false;
    }
}
</script>
