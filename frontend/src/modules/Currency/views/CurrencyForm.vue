<template>
  <v-container fluid class="fill-height align-start pa-6">
    <v-row justify="center">
      <v-col cols="12" md="8" lg="6">
        <v-card class="rounded-xl" elevation="0" border>
          <v-card-title class="pa-6 pb-0 text-h5 font-weight-bold">
            {{ !isAdmin ? 'View Currency' : (isEdit ? 'Edit Currency' : 'New Currency') }}
          </v-card-title>
          
          <v-card-text class="pa-6">
             <v-alert v-if="error" type="error" class="mb-6" closable @click:close="error = null">{{ error }}</v-alert>

            <v-form ref="form" @submit.prevent="save" validate-on="submit lazy">
                <v-row>
                    <v-col cols="12">
                        <div class="text-subtitle-2 font-weight-bold mb-2">Currency Name <span v-if="isAdmin" class="text-error">*</span></div>
                        <v-text-field
                            v-model="formData.name"
                            placeholder="Enter currency name"
                            variant="outlined"
                            density="compact"
                            :readonly="!isAdmin"
                            :rules="isAdmin ? [v => !!v || 'Name is required'] : []"
                        ></v-text-field>
                    </v-col>

                    <v-col cols="12" md="6">
                        <div class="text-subtitle-2 font-weight-bold mb-2">Code <span v-if="isAdmin" class="text-error">*</span></div>
                        <v-text-field
                            v-model="formData.code"
                            placeholder="e.g. USD"
                            variant="outlined"
                            density="compact"
                            :readonly="!isAdmin"
                            :rules="isAdmin ? [v => !!v || 'Code is required'] : []"
                        ></v-text-field>
                    </v-col>

                    <v-col cols="12" md="6">
                        <div class="text-subtitle-2 font-weight-bold mb-2">Symbol <span v-if="isAdmin" class="text-error">*</span></div>
                        <v-text-field
                            v-model="formData.symbol"
                            placeholder="e.g. $"
                            variant="outlined"
                            density="compact"
                            :readonly="!isAdmin"
                            :rules="isAdmin ? [v => !!v || 'Symbol is required'] : []"
                        ></v-text-field>
                    </v-col>

                    <!-- Status (All Modes) -->
                    <v-col cols="12">
                        <div class="text-subtitle-2 font-weight-bold mb-2">Status <span v-if="isAdmin" class="text-error">*</span></div>
                        <div class="d-flex align-center">
                            <v-switch
                                v-model="formData.status"
                                color="primary"
                                hide-details
                                density="compact"
                                class="ma-0"
                                :readonly="!isAdmin"
                                :disabled="!isAdmin"
                            ></v-switch>
                            <span class="ml-2 pt-1 font-weight-medium">{{ formData.status ? 'Active' : 'Inactive' }}</span>
                        </div>
                    </v-col>
                </v-row>

                <div class="d-flex justify-end gap-2 mt-6">
                    <v-btn
                        variant="text"
                        color="grey-darken-1"
                        :to="{ name: 'Currencies' }"
                        class="px-6"
                    >
                         {{ isAdmin ? 'Cancel' : 'Back' }}
                    </v-btn>
                    <v-btn
                        v-if="isAdmin"
                        color="primary"
                        type="submit"
                        variant="elevated"
                        class="px-6"
                        :loading="loading"
                    >
                        Save
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
import { useCurrencyStore } from '../store';
import { useUserStore } from '@/modules/User/store';

const route = useRoute();
const router = useRouter();
const currencyStore = useCurrencyStore();
const userStore = useUserStore();

const isEdit = computed(() => !!route.params.id);
const isAdmin = computed(() => userStore.currentUser?.role === 'admin');
const loading = ref(false);
const error = ref<string | null>(null);

const formData = ref({
    name: '',
    code: '',
    symbol: '',
    status: true,
    users: []
});

onMounted(async () => {
    // Ensure user loaded
    await userStore.fetchCurrentUser();

    if (isEdit.value) {
        const id = Number(route.params.id);
        loading.value = true;
        try {
            await currencyStore.fetchCurrency(id);
            const c = currencyStore.currentCurrency;
            if (c) {
                formData.value = {
                    name: c.name,
                    code: c.code,
                    symbol: c.symbol,
                    status: Boolean(c.status),
                    users: []
                };
            }
        } catch (e) {
            error.value = 'Failed to load currency details';
        } finally {
            loading.value = false;
        }
    }
});

async function save() {
    if (!isAdmin.value) return; 

    // Basic validation
    if (!formData.value.name || !formData.value.code || !formData.value.symbol) return;

    loading.value = true;
    error.value = null;
    
    const payload = {
        name: formData.value.name,
        code: formData.value.code,
        symbol: formData.value.symbol,
        status: formData.value.status 
    };

    try {
        if (isEdit.value) {
            await currencyStore.updateCurrency(Number(route.params.id), payload);
        } else {
            await currencyStore.createCurrency(payload);
        }
        router.push({ name: 'Currencies' });
    } catch (e: any) {
        // Store handles notification
    } finally {
        loading.value = false;
    }
}
</script>
