<template>
  <v-card class="rounded-0 mb-6" elevation="0" border>
    <!-- Inputs Section with Gray Background -->
    <div style="background-color: #F5F6F9;" class="pa-4">
      <v-row>
        <!-- Reference -->
        <v-col cols="12" md="4">
          <div class="text-subtitle-2 font-weight-bold mb-2">Reference</div>
          <v-text-field
            v-model="filters.reference"
            placeholder="Search Reference..."
            variant="outlined"
            density="compact"
            hide-details
            bg-color="white"
            append-inner-icon="mdi-magnify"
            @keyup.enter="applyFilters"
          ></v-text-field>
        </v-col>

        <!-- Date From -->
        <v-col cols="12" md="4">
          <div class="text-subtitle-2 font-weight-bold mb-2">Date From</div>
          <v-menu
            v-model="menuFrom"
            :close-on-content-click="false"
            transition="scale-transition"
            offset-y
            min-width="auto"
          >
            <template v-slot:activator="{ props }">
              <v-text-field
                v-bind="props"
                :model-value="filters.from_date"
                placeholder="Select Date"
                variant="outlined"
                density="compact"
                hide-details
                bg-color="white"
                append-inner-icon="mdi-calendar"
                readonly
              ></v-text-field>
            </template>
            <v-date-picker
              color="primary"
              :model-value="filters.from_date ? new Date(filters.from_date) : null"
              @update:model-value="(date) => updateDate('from_date', date)"
            ></v-date-picker>
          </v-menu>
        </v-col>

        <!-- Date To -->
        <v-col cols="12" md="4">
          <div class="text-subtitle-2 font-weight-bold mb-2">Date To</div>
          <v-menu
            v-model="menuTo"
            :close-on-content-click="false"
            transition="scale-transition"
            offset-y
            min-width="auto"
          >
            <template v-slot:activator="{ props }">
              <v-text-field
                v-bind="props"
                :model-value="filters.to_date"
                placeholder="Select Date"
                variant="outlined"
                density="compact"
                hide-details
                bg-color="white"
                append-inner-icon="mdi-calendar"
                readonly
              ></v-text-field>
            </template>
            <v-date-picker
              color="primary"
              :model-value="filters.to_date ? new Date(filters.to_date) : null"
              @update:model-value="(date) => updateDate('to_date', date)"
            ></v-date-picker>
          </v-menu>
        </v-col>

        <!-- Type -->
        <!-- Type -->
        <!-- <v-col cols="12" md="3">
          <div class="text-subtitle-2 font-weight-bold mb-2">Type</div>
          <v-select
            v-model="filters.type"
            :items="types"
            item-title="title"
            item-value="value"
            placeholder="All"
            variant="outlined"
            density="compact"
            hide-details
            bg-color="white"
          ></v-select>
        </v-col> -->
      </v-row>
    </div>

    <v-divider></v-divider>

    <!-- Buttons Section with White Background -->
    <div class="pa-4 bg-white d-flex justify-end">
      <v-btn
        variant="outlined"
        color="grey-darken-1"
        class="mr-2 text-capitalize"
        width="100"
        @click="clearFilters"
      >
        Clear
      </v-btn>
      <v-btn
        color="primary"
        elevation="0"
        class="text-capitalize"
        width="100"
        @click="applyFilters"
      >
        Filter
      </v-btn>
    </div>
  </v-card>
</template>

<script setup lang="ts">
import { ref } from 'vue';

const emit = defineEmits(['apply-filter']);

const types = [
  { title: 'All', value: null },
  { title: 'Credit', value: 'credit' },
  { title: 'Debit', value: 'debit' },
];

const filters = ref<{
  type: 'credit' | 'debit' | null;
  from_date: string | null;
  to_date: string | null;
  reference: string;
  timezone: string;
}>({
  type: null,
  from_date: null,
  to_date: null,
  reference: '',
  timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
});

const menuFrom = ref(false);
const menuTo = ref(false);

function updateDate(field: 'from_date' | 'to_date', date: any) {
  if (date) {
    const d = new Date(date);
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    filters.value[field] = `${year}-${month}-${day}`;
  } else {
    filters.value[field] = null;
  }

  if (field === 'from_date') menuFrom.value = false;
  if (field === 'to_date') menuTo.value = false;
}

function applyFilters() {
  emit('apply-filter', filters.value);
}

function clearFilters() {
  filters.value = {
    type: null,
    from_date: null,
    to_date: null,
    reference: '',
    timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
  };
  applyFilters();
}
</script>

<style scoped>
:deep(input[type="date"]) {
    text-align: right;
    padding-right: 10px;
}
:deep(input[type="date"]::-webkit-calendar-picker-indicator) {
    margin-left: 10px;
}
</style>
