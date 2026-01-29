<template>
  <v-dialog v-model="dialog" max-width="500">
    <v-card>
      <v-card-title>Add New Member</v-card-title>
      <v-card-text>
        <v-alert v-if="error" type="error" class="mb-4" closable @click:close="error = null">{{ error }}</v-alert>
        
        <v-form @submit.prevent="submit" ref="form">
          <v-text-field
            v-model="name"
            label="Full Name"
            variant="outlined"
            :rules="[v => !!v || 'Name is required']"
            required
          ></v-text-field>

          <v-text-field
            v-model="email"
            label="Email Address"
            variant="outlined"
            type="email"
            :rules="[v => !!v || 'Email is required', v => /.+@.+\..+/.test(v) || 'Invalid email']"
            required
          ></v-text-field>

          <v-text-field
            v-model="password"
            label="Password"
            variant="outlined"
            type="password"
            :rules="[v => !!v || 'Password is required', v => v.length >= 8 || 'Min 8 characters']"
            required
          ></v-text-field>

          <v-select
            v-model="role"
            label="Role"
            variant="outlined"
            :items="['user', 'admin']"
            required
          ></v-select>
        </v-form>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="grey" variant="text" @click="dialog = false">Cancel</v-btn>
        <v-btn color="primary" @click="submit" :loading="loading">Add Member</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { useUserStore } from '@/modules/User/store';

const props = defineProps<{
  modelValue: boolean;
}>();

const emit = defineEmits<{
  (e: 'update:modelValue', value: boolean): void;
  (e: 'created'): void;
}>();

const userStore = useUserStore();
const name = ref('');
const email = ref('');
const password = ref('');
const role = ref<'admin'|'user'>('user');
const loading = ref(false);
const error = ref<string | null>(null);
const form = ref<any>(null);

const dialog = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val),
});

async function submit() {
  const { valid } = await form.value.validate();
  if (!valid) return;

  loading.value = true;
  error.value = null;
  try {
    await userStore.createMember({
      name: name.value,
      email: email.value,
      password: password.value,
      role: role.value,
    });
    emit('created');
    dialog.value = false;
    // Reset form
    name.value = '';
    email.value = '';
    password.value = '';
    role.value = 'user';
  } catch (e: any) {
    error.value = e.response?.data?.message || 'Failed to create member';
  } finally {
    loading.value = false;
  }
}
</script>
