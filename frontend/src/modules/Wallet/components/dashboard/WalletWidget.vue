<template>
  <v-card class="rounded-0 d-flex flex-row overflow-hidden wallet-card" elevation="0" border>
    <!-- Left Color Strip Removed -->

    <div class="flex-grow-1 d-flex flex-column">
        <!-- Header -->
        <div class="pa-4 pb-3 d-flex align-center">
             <v-avatar 
                rounded="lg" 
                size="36" 
                :color="color || 'primary'" 
                variant="tonal"
                class="mr-3"
              >
                <!-- Use wallet icon instead of currency symbol icon -->
                <v-icon :color="color || 'primary'" size="small">mdi-wallet</v-icon>
              </v-avatar>
             <span class="text-subtitle-1 font-weight-bold">{{ name }}</span>
        </div>
        
        <v-divider></v-divider>

        <!-- Body -->
        <div class="pa-4 pt-3 flex-grow-1 d-flex flex-column justify-center" style="background-color: #F6F6F9;"> 
             <div class="d-flex align-baseline mb-2">
                 <span class="text-h5 font-weight-bold mr-1">{{ symbol }}{{ amount }}</span>
                 <span class="text-caption text-grey-darken-1 font-weight-bold mt-1">{{ currency }}</span>
             </div>
             
              <div class="d-flex align-center text-caption text-grey-darken-1 mt-auto pt-2">
                  <v-icon size="small" class="mr-1">mdi-account-group-outline</v-icon>
                  <span class="font-weight-medium">{{ usersCount || 0 }} Users Assigned</span>
              </div>
        </div>
    </div>
  </v-card>
</template>

<style scoped>
.wallet-card {
    min-height: 180px;
    height: 100%;
}
</style>

<script setup lang="ts">
defineProps<{
  name: string;
  amount: string;
  currency: string;
  symbol: string;
  icon?: string;
  color?: string;
  usersCount?: number;
}>();
</script>
