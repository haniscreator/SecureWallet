<template>
  <div class="transfer-form-container">
    <div class="page-header mb-6 d-flex align-center justify-space-between">
      <h1 class="text-h4 font-weight-bold primary--text">Initiate Transfer</h1>
    </div>

    <!-- Source Wallet Info -->
    <v-card class="mb-6 pa-6 rounded-0 elevation-2" v-if="selectedSourceWallet">
        <div class="d-flex align-center justify-space-between">
            <div>
                <div class="text-subtitle-2 text-grey-darken-1">Source Wallet</div>
                <div class="text-h6 font-weight-bold">{{ selectedSourceWallet.name }}</div>
            </div>
            <div class="text-right">
                <div class="text-subtitle-2 text-grey-darken-1">Available Balance</div>
                <div class="text-h6 font-weight-bold" :class="{'text-error': Number(selectedSourceWallet.available_balance) < Number(selectedSourceWallet.balance)}">
                    {{ selectedSourceWallet.currency?.symbol }}{{ Number(selectedSourceWallet.available_balance).toLocaleString() }} 
                    <span class="text-body-2 text-grey-darken-1">{{ selectedSourceWallet.currency?.code }}</span>
                </div>
                <div class="text-caption text-grey-darken-1" v-if="Number(selectedSourceWallet.available_balance) < Number(selectedSourceWallet.balance)">
                    (Total: {{ Number(selectedSourceWallet.balance).toLocaleString() }})
                </div>
            </div>
        </div>
    </v-card>
    <div v-else class="mb-6 text-grey">Loading Source Wallet...</div>

    <!-- Lottie Animation -->
    <div class="d-flex justify-center mb-6">
        <div ref="lottieContainer" style="width: 50px; height: 50px;"></div>
    </div>

    <v-card class="pa-6 rounded-0 elevation-2">
        <!-- Step 1: Choose Transfer Type (Cards) -->
        <h3 class="text-h6 mb-4 font-weight-bold">Choose Transfer Type</h3>
        <v-row class="mb-6">
            <v-col cols="12" sm="6">
                <v-card 
                    class="py-8 px-4 text-center cursor-pointer transition-swing hover-card d-flex flex-column align-center justify-center rounded-0"
                    elevation="0"
                    variant="outlined"
                    min-height="250"
                    @click="formData.type = 'internal'"
                    :class="{'selected-card': formData.type === 'internal'}"
                    :color="formData.type === 'internal' ? 'primary-lighten-5' : undefined"
                    style="border-color: #e0e0e0;"
                >
                    <v-icon size="64" color="primary" class="mb-4">mdi-bank-transfer</v-icon>
                    <div class="text-h6 font-weight-bold text-no-wrap mb-2">Internal Transfer</div>
                    <div class="text-body-2 text-grey-darken-1">Send funds instantly to another user's wallet within the platform.</div>
                </v-card>
            </v-col>
            
            <v-col cols="12" sm="6">
                <v-card 
                    class="py-8 px-4 text-center cursor-pointer transition-swing hover-card d-flex flex-column align-center justify-center rounded-0"
                    elevation="0"
                    variant="outlined"
                    min-height="250"
                    @click="formData.type = 'external'"
                    :class="{'selected-card': formData.type === 'external'}"
                    :color="formData.type === 'external' ? 'primary-lighten-5' : undefined"
                    style="border-color: #e0e0e0;"
                >
                    <v-icon size="64" color="secondary" class="mb-4">mdi-earth</v-icon>
                    <div class="text-h6 font-weight-bold text-no-wrap mb-2">External Transfer</div>
                    <div class="text-body-2 text-grey-darken-1">Withdraw funds to an external blockchain address.</div>
                </v-card>
            </v-col>
        </v-row>

        <!-- Step 2: Transfer Details Form -->
        <v-expand-transition>
            <div v-if="formData.type">
                <v-divider class="mb-6"></v-divider>
                <h3 class="text-h6 mb-4 font-weight-bold">Transfer Details</h3>
                <v-form v-model="validDetails" @submit.prevent="submitTransfer">
                    <!-- Internal: Target Wallet (Col 8) -->
                    <v-row v-if="formData.type === 'internal'">
                        <v-col cols="12" md="8">
                            <v-select
                                v-model="formData.to_wallet_id"
                                :items="internalTargetWallets"
                                item-title="name"
                                item-value="id"
                                label="Choose destination wallet"
                                placeholder="Select a destination wallet"
                                variant="outlined"
                                :rules="[v => !!v || 'Destination wallet is required', validateInternalTarget]"
                                required
                                :loading="loadingTargets"
                                class="mb-4 right-aligned-messages"
                                autocomplete="off"
                            >
                                <template v-slot:item="{ props, item }">
                                    <v-list-item 
                                        v-bind="props" 
                                        :subtitle="(item.raw.currency?.code || '') + ' - User: ' + (item.raw.users?.[0]?.name || 'Unknown')"
                                    ></v-list-item>
                                </template>
                            </v-select>
                        </v-col>
                    </v-row>

                    <!-- External: Address Input (Col 8) -->
                    <v-row v-if="formData.type === 'external'">
                        <v-col cols="12" md="8">
                            <v-text-field
                                v-model="formData.to_address"
                                label="External Wallet Address"
                                placeholder="e.g. 0x123..."
                                variant="outlined"
                                :rules="[v => !!v || 'Address is required']"
                                required
                                class="mb-4 right-aligned-messages"
                                @blur="validateExternalAddress"
                                @input="addressValidationResult = null"
                                :error="addressValidationResult && !addressValidationResult.valid"
                                :color="addressValidationResult?.valid ? 'success' : 'error'"
                                autocomplete="off"
                            >
                                <template v-slot:details v-if="addressValidationResult && (!addressValidationResult.valid || !addressValidationResult.exists)">
                                    <div class="text-caption" :class="{
                                        'text-error': !addressValidationResult.valid,
                                        'text-warning': addressValidationResult.valid && !addressValidationResult.exists
                                    }">
                                        {{ addressValidationResult.message }}
                                    </div>
                                </template>
                            </v-text-field>
                        </v-col>
                    </v-row>

                    <!-- Amount (Half Width) -->
                    <v-row>
                        <v-col cols="12" md="6">
                            <v-text-field
                                v-model.number="formData.amount"
                                label="Amount"
                                placeholder="0.00"
                                variant="outlined"
                                type="number"
                                min="0.01"
                                step="0.01"
                                :rules="amountRules"
                                required
                                class="mb-4 right-aligned-messages"
                                autocomplete="off"
                            ></v-text-field>
                        </v-col>
                    </v-row>

                    <!-- Reference -->
                    <v-textarea
                        v-model="formData.description"
                        label="Reference (Optional)"
                        placeholder="Enter reference"
                        variant="outlined"
                        rows="3"
                        class="mb-4"
                        autocomplete="off"
                    ></v-textarea>
                    
                    <v-alert v-if="error" type="error" class="mb-4" closable>{{ error }}</v-alert>

                    <div class="d-flex justify-end align-center mt-4">
                        <v-btn variant="text" color="grey-darken-1" class="mr-4 text-none text-subtitle-1" @click="router.push('/wallets')">
                            Cancel
                        </v-btn>
                        <v-btn color="primary" variant="flat" @click="submitTransfer" :loading="submitting" size="large" width="200" class="text-none text-subtitle-1"
                            :disabled="!validDetails || (formData.type === 'external' && addressValidationResult?.valid === false)"
                        >
                            Transfer Funds
                        </v-btn>
                    </div>
                </v-form>
            </div>
        </v-expand-transition>
    </v-card>
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import lottie from 'lottie-web';
import { useTransferForm } from '@/modules/Transaction/composables/useTransferForm';

const router = useRouter(); 
const lottieContainer = ref<HTMLElement | null>(null);

const {
    formData,
    validDetails,
    submitting,
    error,
    selectedSourceWallet,
    internalTargetWallets,
    loadingTargets,
    addressValidationResult,
    amountRules,
    validateInternalTarget,
    validateExternalAddress,
    submitTransfer,
    init
} = useTransferForm();

onMounted(async () => {
  if (lottieContainer.value) {
    lottie.loadAnimation({
      container: lottieContainer.value,
      renderer: 'svg',
      loop: true,
      autoplay: true,
      path: '/Arrow Down.json'
    });
  }

  await init();
});

defineExpose({
    formData,
    submitTransfer,
    validDetails
});
</script>

<style scoped>
.transfer-form-container {
  max-width: 1000px;
  margin: 0 auto;
  padding: 16px;
}
.transfer-card {
    border: 1px solid #e0e0e0;
}

.hover-card:hover {
    background-color: #f5f5f5;
    border-color: #1976D2 !important;
    transform: translateY(-2px);
}
.selected-card {
    border: 2px solid #1976D2;
    background-color: #F0F7FF;
}

/* --- Right Aligned Validation Message --- */
.right-aligned-messages :deep(.v-input__details) {
    justify-content: flex-end;
    text-align: right;
}

@media (max-width: 600px) {
    .text-h6 {
        font-size: 1rem !important; /* Adjust for mobile if needed */
    }
}
</style>