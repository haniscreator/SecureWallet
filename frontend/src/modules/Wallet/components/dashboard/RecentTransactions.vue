<template>
  <v-card class="rounded-lg mt-6" elevation="0" border>
    <v-card-title class="pa-4 text-h6 font-weight-bold">
      Recent Transactions
    </v-card-title>
    
    <v-table class="pa-2">
      <thead>
        <tr>
          <th class="text-left text-grey-darken-1 font-weight-medium">Date</th>
          <th class="text-left text-grey-darken-1 font-weight-medium">From/To</th>
          <th class="text-left text-grey-darken-1 font-weight-medium">Type</th>
          <th class="text-left text-grey-darken-1 font-weight-medium">Amount</th>
          <th class="text-left text-grey-darken-1 font-weight-medium">Reference</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in transactions" :key="item.id">
          <td class="text-body-2 text-grey-darken-2">{{ item.date }}</td>
          <td>
            <div class="d-flex align-center">
              <v-avatar 
                size="24" 
                rounded="sm" 
                :color="getWalletColor(item.wallet)" 
                class="mr-2"
              >
                <v-icon size="x-small" color="white">{{ getWalletIcon(item.wallet) }}</v-icon>
              </v-avatar>
              <span class="text-body-2 font-weight-medium">{{ item.wallet }}</span>
            </div>
          </td>
          <td>
            <span class="text-body-2">{{ item.type }}</span>
          </td>
          <td>
            <span :class="['text-body-2 font-weight-bold', item.amount.startsWith('-') ? 'text-teal' : 'text-teal']">
              {{ item.amount }}
            </span>
          </td>
          <td class="text-body-2 text-grey-darken-1">{{ item.reference }}</td>
        </tr>
      </tbody>
    </v-table>
    
    <div class="d-flex justify-end pa-4">
        <!-- Pagination mockup -->
        <v-pagination
            :length="4"
            density="compact"
            active-color="primary"
        ></v-pagination>
    </div>
  </v-card>
</template>

<script setup lang="ts">
import { ref } from 'vue';

const transactions = ref([
  { id: 1, date: '12/10/2022', wallet: 'Main Wallet', type: 'Debit', amount: '-$500.00', reference: 'Invoice #123' },
  { id: 2, date: '12/09/2022', wallet: 'EUR Wallet', type: 'Credit', amount: '€1,000.00', reference: 'Client Payment' },
  { id: 3, date: '12/09/2022', wallet: 'Marketing Wallet', type: 'Debit', amount: '-$200.00', reference: 'Advertising' },
  { id: 4, date: '12/07/2022', wallet: 'Main Wallet', type: 'Credit', amount: '€2,500.00', reference: 'Transfer' },
]);

function getWalletColor(name: string) {
    if (name.includes('Main')) return 'blue-darken-2';
    if (name.includes('EUR')) return 'blue-darken-4';
    return 'green-darken-3'; // Marketing
}

function getWalletIcon(name: string) {
    if (name.includes('Main')) return 'mdi-wallet-outline';
    if (name.includes('EUR')) return 'mdi-currency-eur';
    return 'mdi-currency-usd';
}
</script>
