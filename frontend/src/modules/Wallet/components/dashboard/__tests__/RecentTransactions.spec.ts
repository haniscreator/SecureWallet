import { mount } from '@vue/test-utils'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import RecentTransactions from '../RecentTransactions.vue'
import { createTestingPinia } from '@pinia/testing'
import { vi } from 'vitest'
import { useWalletStore } from '@/modules/Wallet/store'

const vuetify = createVuetify({ components, directives })

describe('RecentTransactions', () => {
    it('renders list of transactions from dashboard state', () => {
        const wrapper = mount(RecentTransactions, {
            global: {
                plugins: [
                    vuetify,
                    createTestingPinia({
                        createSpy: vi.fn,
                        initialState: {
                            wallet: {
                                dashboardTransactions: [
                                    {
                                        id: 1,
                                        amount: '50.00',
                                        type: 'debit',
                                        reference: 'Coffee',
                                        created_at: '2026-01-30T10:00:00Z',
                                        to_wallet: {
                                            name: 'Coffee Shop',
                                            currency: { symbol: '$' }
                                        },
                                        from_wallet: {
                                            name: 'Checking',
                                            currency: { symbol: '$' }
                                        }
                                    }
                                ],
                                dashboardTotalItems: 1,
                                dashboardPage: 1
                            }
                        }
                    })
                ]
            }
        })

        expect(wrapper.text()).toContain('Checking')
        expect(wrapper.text()).toContain('Coffee')
        expect(wrapper.text()).toContain('$50')
    })

    it('triggers fetchDashboardTransactions on mount', () => {
        const wrapper = mount(RecentTransactions, {
            global: {
                plugins: [
                    vuetify,
                    createTestingPinia({
                        createSpy: vi.fn,
                    })
                ]
            }
        })
        const store = useWalletStore()
        expect(store.fetchDashboardTransactions).toHaveBeenCalledWith(1, 10)
    })

    it('triggers fetchDashboardTransactions on page change', async () => {
        const wrapper = mount(RecentTransactions, {
            global: {
                plugins: [
                    vuetify,
                    createTestingPinia({
                        createSpy: vi.fn,
                        initialState: {
                            wallet: {
                                dashboardTransactions: [],
                                dashboardTotalItems: 20, // Enough for 2 pages
                                dashboardPage: 1
                            }
                        }
                    })
                ]
            }
        })
        const store = useWalletStore()

        // Find pagination (it might be tricky to click v-pagination directly in test due to Vuetify implementation)
        // Accessing component instance method is simpler if exposed, but relying on v-model update is standard
        // Let's try emitting update event or finding the button

        // Simpler approach: call the handler directly if exposed or check bind
        // Or find the v-pagination and emit
        const pagination = wrapper.findComponent({ name: 'VPagination' })
        await pagination.vm.$emit('update:modelValue', 2)

        expect(store.fetchDashboardTransactions).toHaveBeenCalledWith(2, 10)
    })

    it('shows skeleton loader when dashboardLoading is true', () => {
        const wrapper = mount(RecentTransactions, {
            global: {
                plugins: [
                    vuetify,
                    createTestingPinia({
                        createSpy: vi.fn,
                        initialState: {
                            wallet: {
                                dashboardLoading: true,
                                dashboardTransactions: []
                            }
                        }
                    })
                ]
            }
        })

        const table = wrapper.findComponent({ name: 'TransactionTable' })
        expect(table.exists()).toBe(true)
        expect(table.props('loading')).toBe(true)
    })
})
