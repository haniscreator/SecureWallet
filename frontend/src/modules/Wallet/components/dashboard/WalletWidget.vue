<template>
  <v-card class="rounded-lg pa-3" elevation="0" border>
    <div class="d-flex align-center mb-4">
      <v-avatar 
        rounded="0" 
        size="32" 
        :color="color" 
        class="mr-3 rounded-sm"
      >
        <v-icon color="white" size="small">{{ icon }}</v-icon>
      </v-avatar>
      <span class="text-subtitle-1 font-weight-bold">{{ name }}</span>
    </div>
    
    <div class="d-flex align-baseline">
      <span class="text-h5 font-weight-bold mr-2">{{ symbol }}{{ amount }}</span>
      <span class="text-caption text-grey font-weight-bold">{{ currency }}</span>
    </div>
  </v-card>
</template>

<script setup lang="ts">
defineProps<{
  name: string;
  amount: string;
  currency: string;
  symbol: string;
  icon?: string;
  color?: string;
}>();
</script>
