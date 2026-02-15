<template>
  <v-container fluid class="fill-height align-start pa-0 bg-grey-lighten-5">
    <!-- Navbar / Header (Optional, or rely on layout if used inside layout, but requirements imply standalone page style) -->
    <!-- Assuming this is a public page, we might want a simple header or just the content. 
         The design shows a clean layout. We'll center the content. -->

    <v-row justify="center" class="mt-16">
        <v-col cols="12" md="10" lg="8">
            <!-- Header Row -->
            <v-row class="mb-4 text-grey-darken-1 font-weight-bold">
                <v-col cols="12" md="3">Version</v-col>
                <v-col cols="12" md="9">Description</v-col>
            </v-row>

            <!-- Changelog Entry -->
            <v-row>
                <!-- Left Column: Version & Date -->
                <v-col cols="12" md="3">
                    <div class="text-h6 font-weight-bold text-grey-darken-3">1.1.0</div>
                    <div class="text-body-2 text-grey-darken-1 mt-1">Feb 15, 2026</div>
                </v-col>

                <!-- Right Column: Card -->
                <v-col cols="12" md="9">
                    <v-card class="rounded-xl overflow-hidden elevation-0" color="white">
                        <v-row no-gutters>
                            <!-- Left Panel of Card: Logo -->
                            <v-col cols="12" md="4" class="bg-white d-flex flex-column align-center justify-center pa-8">
                                <div class="logo-circle mb-4 bg-primary d-flex align-center justify-center rounded-circle" style="width: 64px; height: 64px;">
                                    <v-icon color="white" size="32">mdi-shield-check</v-icon>
                                </div>
                                <div class="text-h5 font-weight-bold text-grey-darken-3">SecureWallet</div>
                                <div class="text-caption text-grey text-uppercase mt-1 tracking-wider">Changelog</div>
                            </v-col>

                            <!-- Right Panel of Card: Content -->
                            <v-col cols="12" md="8" class="pa-6">
                                <v-expansion-panels variant="accordion" class="changelog-panels">
                                    
                                    <!-- Improvements -->
                                    <v-expansion-panel elevation="0">
                                        <v-expansion-panel-title class="text-body-1 font-weight-bold text-grey-darken-3">
                                            Improvements
                                        </v-expansion-panel-title>
                                        <v-expansion-panel-text class="text-grey-darken-1">
                                            <ul class="pl-4">
                                                <li>Code Refactoring</li>
                                                <li>Performance</li>
                                            </ul>
                                        </v-expansion-panel-text>
                                    </v-expansion-panel>

                                    <!-- Fixes -->
                                    <v-expansion-panel elevation="0">
                                        <v-expansion-panel-title class="text-body-1 font-weight-bold text-grey-darken-3">
                                            Fixes
                                        </v-expansion-panel-title>
                                        <v-expansion-panel-text class="text-grey-darken-1">
                                            <ul class="pl-4">
                                                <li>Minor bug fixed</li>
                                            </ul>
                                        </v-expansion-panel-text>
                                    </v-expansion-panel>

                                    <!-- New Features -->
                                    <v-expansion-panel elevation="0">
                                        <v-expansion-panel-title class="text-body-1 font-weight-bold text-grey-darken-3">
                                            New Features
                                        </v-expansion-panel-title>
                                        <v-expansion-panel-text class="text-grey-darken-1">
                                            <ul class="pl-4">
                                                <li>Transfer Feature</li>
                                                <li>Approval Flow</li>
                                                <li>Manager as new user role</li>
                                            </ul>
                                        </v-expansion-panel-text>
                                    </v-expansion-panel>

                                </v-expansion-panels>
                            </v-col>
                        </v-row>
                    </v-card>
                </v-col>
            </v-row>
        </v-col>
    </v-row>
  </v-container>
</template>

<script setup lang="ts">
// No script logic needed for this static page
</script>

<style scoped>
.tracking-wider {
    letter-spacing: 0.1em;
}

/* Customize expansion panels to look cleaner */
:deep(.v-expansion-panel--active:not(:first-child), .v-expansion-panel--active + .v-expansion-panel) {
    margin-top: 0;
}
:deep(.v-expansion-panel-title) {
    padding: 16px 0;
    min-height: 48px;
}
:deep(.v-expansion-panel-text__wrapper) {
    padding: 0 0 16px 16px; 
}
/* Ensure entire panel area is clickable/visual */
:deep(.v-expansion-panel) {
    background-color: transparent !important; 
}
/* Remove border lines if variant accordion adds them */
:deep(.v-expansion-panel::after) {
    border-top: none !important;
}

/* Remove hover overlay effect */
:deep(.v-expansion-panel-title__overlay) {
    opacity: 0 !important;
}
</style>
