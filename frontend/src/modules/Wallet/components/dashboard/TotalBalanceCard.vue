<template>
  <v-card class="rounded-lg pa-4" elevation="0" border>
    <v-card-title class="text-subtitle-1 text-grey-darken-1 font-weight-medium pb-4">
      Total Balance
    </v-card-title>
    
    <v-card-text>
      <div class="d-flex flex-column gap-2">
        <div class="d-flex align-center">
          <span class="text-h4 font-weight-bold mr-2">$12,500.00</span>
          <span class="text-subtitle-1 text-grey font-weight-bold">USD</span>
        </div>
        
        <div class="d-flex align-center text-grey-darken-1">
          <span class="text-h5 font-weight-bold mr-2">€ 8,750.00</span>
          <span class="text-subtitle-2 font-weight-bold">EUR</span>
        </div>
      </div>
    </v-card-text>
  </v-card>
</template>

<script setup lang="ts">
// Widget is purely presentational for now
</script>

<style scoped>
.gap-2 {
  gap: 8px;
}
</style>
