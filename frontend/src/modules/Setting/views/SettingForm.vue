<template>
  <v-container fluid class="fill-height align-start pa-6">
    <v-row justify="center">
      <v-col cols="12" md="8" lg="6">
        <v-card class="mx-auto" elevation="0" rounded="0" border>
            <v-card-title class="text-h6 font-weight-bold pa-4 border-b">
                System Settings
            </v-card-title>
            
            <v-card-text class="pa-6">
                <v-form @submit.prevent="submit" :disabled="settingStore.loading">
                    <div v-if="settingStore.loading && !form.length" class="d-flex justify-center pa-4">
                        <v-progress-circular indeterminate color="primary"></v-progress-circular>
                    </div>

                    <template v-else>
                        <v-text-field
                            v-for="(item, index) in form"
                            :key="item.key"
                            v-model="item.value"
                            :label="humanizeKey(item.key)"
                            :hint="item.description"
                            persistent-hint
                            variant="outlined"
                            density="comfortable"
                            class="mb-4"
                        ></v-text-field>
                    </template>

                    <v-alert v-if="settingStore.error" type="error" variant="tonal" class="mb-4">
                        {{ settingStore.error }}
                    </v-alert>

                    <div class="d-flex justify-end mt-4">
                        <v-btn
                            color="primary"
                            type="submit"
                            :loading="settingStore.loading"
                            elevation="2"
                            class="text-capitalize px-6"
                        >
                            Save Changes
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
import { ref, onMounted, watch } from 'vue';
import { useSettingStore } from '../store';
import { storeToRefs } from 'pinia';

const settingStore = useSettingStore();
const { settings } = storeToRefs(settingStore);

const form = ref<{ key: string; value: string; description?: string }[]>([]);

// Convert snake_case key to Human Readable
function humanizeKey(key: string) {
    return key
        .split('_')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
}

// Sync store data to local form
watch(settings, (newSettings) => {
    form.value = newSettings.map(s => ({
        key: s.key,
        value: s.value,
        description: s.description
    }));
}, { immediate: true });

async function submit() {
    await settingStore.updateSettings(form.value);
}

onMounted(() => {
    settingStore.fetchSettings();
});
</script>
