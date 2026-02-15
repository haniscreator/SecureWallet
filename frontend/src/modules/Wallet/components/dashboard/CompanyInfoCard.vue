<template>
  <v-card class="company-info-card rounded-0 overflow-hidden fill-height" elevation="0" border>
      <div class="d-flex fill-height">
          <!-- Left Side: Hero Icon Only -->
          <div class="left-panel d-flex flex-column justify-center align-center px-6 py-4" style="background-color: #F6F6F9;">
              <div class="shield-icon-wrapper">
                 <v-icon color="#FF5252" size="24" class="shield-badge">mdi-shield-check</v-icon>
                 <v-icon color="primary" size="48">mdi-domain</v-icon>
              </div>
          </div>

          <v-divider vertical></v-divider>

          <!-- Right Side: Split into Stats, Details, and Address Columns -->
          <div class="right-panel flex-grow-1 pa-6 bg-white fill-height d-flex align-center">
              <v-row no-gutters class="w-100 fill-height align-center">
                  
                  <!-- Column 2: Stats -->
                  <v-col cols="12" md="4" class="d-flex flex-column justify-center pr-4 border-end-sm">
                      <div class="text-h6 font-weight-bold text-grey-darken-3">Company</div>
                      <v-divider class="my-4"></v-divider>
                      
                      <div class="stats-column d-flex flex-column gap-2">
                          <div class="d-flex align-center mb-2">
                               <span class="text-grey-darken-1 text-body-2 mr-2" style="min-width: 100px;">Total Wallets:</span>
                               <span class="font-weight-bold text-grey-darken-3">{{ totalWallets }}</span>
                          </div>
                          <div class="d-flex align-center mb-2">
                               <span class="text-grey-darken-1 text-body-2 mr-2" style="min-width: 100px;">Total Users:</span>
                               <span class="font-weight-bold text-grey-darken-3">{{ totalUsers }}</span>
                          </div>
                          <div class="d-flex align-center mb-2">
                               <span class="text-grey-darken-1 text-body-2 mr-2" style="min-width: 100px;">Currencies:</span>
                               <span class="font-weight-bold text-grey-darken-3">{{ totalCurrencies }}</span>
                          </div>
                          <div class="d-flex align-center mb-2">
                               <span class="text-grey-darken-1 text-body-2 mr-2" style="min-width: 100px;">Tx Count:</span>
                               <span class="font-weight-bold text-grey-darken-3">{{ totalTxCount }}</span>
                          </div>
                      </div>
                  </v-col>

                  <!-- Column 3: Company Details (Name, Email, Address) -->
                  <v-col cols="12" md="8" class="d-flex flex-column justify-center px-md-6">
                      <div class="d-flex align-start mb-4">
                          <v-icon size="small" class="mr-3 text-grey-darken-1 mt-1">mdi-office-building</v-icon> 
                          <div>
                              <div class="text-caption text-grey-darken-1">Company Name</div>
                              <div class="font-weight-bold text-grey-darken-3 text-body-1">{{ companyName }}</div>
                          </div>
                      </div>
                       <div class="d-flex align-start mb-4">
                          <v-icon size="small" class="mr-3 text-grey-darken-1 mt-1">mdi-email-outline</v-icon> 
                          <div>
                              <div class="text-caption text-grey-darken-1">Email</div>
                              <div class="text-body-2 text-grey-darken-3">{{ companyEmail }}</div>
                          </div>
                      </div>
                      <div class="d-flex align-start">
                          <v-icon size="small" class="mr-3 text-grey-darken-1 mt-1">mdi-map-marker-outline</v-icon> 
                          <div>
                              <div class="text-caption text-grey-darken-1">Address</div>
                              <div class="text-body-2 text-grey-darken-3">123 Market St, Suite 400<br>San Francisco, CA 94103</div>
                          </div>
                      </div>
                  </v-col>
              </v-row>
          </div>
      </div>
  </v-card>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useWalletStore } from '@/modules/Wallet/store';
import { useUserStore } from '@/modules/User/store';
import { useAuthStore } from '@/modules/Auth/store';

const walletStore = useWalletStore();
const userStore = useUserStore();
const authStore = useAuthStore();

// Computed Stats
const totalWallets = computed(() => walletStore.wallets.length);
const totalUsers = computed(() => userStore.members.length); 
const totalCurrencies = computed(() => Object.keys(walletStore.totalBalance || {}).length);
const companyName = computed(() => authStore.user?.company_name || 'Korporatio');
const companyEmail = computed(() => authStore.user?.email || 'admin@korporatio.com');

const totalTxCount = computed(() => {
    return walletStore.dashboardTotalItems;
});
</script>

<style scoped>
.company-info-card {
    background: white;
    min-height: 200px;
    position: relative;
}

.left-panel {
    min-width: 120px;
    background: #FAFAFA; /* Light grey */
}

.shield-icon-wrapper {
    background: white;
    border-radius: 50%;
    width: 64px;
    height: 64px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05); /* Lighter shadow */
}

.shield-badge {
    position: absolute;
    bottom: -4px;
    right: -4px;
    background: white;
    border-radius: 50%;
    padding: 2px;
}

.border-top-light {
    border-top: 1px solid rgba(0,0,0,0.05);
}
</style>
