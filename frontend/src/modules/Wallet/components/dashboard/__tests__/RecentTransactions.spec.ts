import { mount } from '@vue/test-utils'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import RecentTransactions from '../RecentTransactions.vue'
import { createTestingPinia } from '@pinia/testing'
import { vi } from 'vitest'

const vuetify = createVuetify({ components, directives })

describe('RecentTransactions', () => {
    it('renders list of transactions', () => {
        const wrapper = mount(RecentTransactions, {
            global: {
                plugins: [
                    vuetify,
                    createTestingPinia({
                        createSpy: vi.fn,
                        initialState: {
                            wallet: {
                                recentGlobalTransactions: [
                                    {
                                        id: 1,
                                        amount: 50.00,
                                        type: 'debit',
                                        wallet_name: 'Checking',
                                        reference: 'Coffee',
                                        created_at: '2026-01-30T10:00:00Z',
                                        to_wallet: { currency: { symbol: '$' } }
                                        // Mocks of related objects are not strictly needed for list if not accessed
                                        // But store usually keeps flat structure for this list based on our store analysis
                                    },
                                    {
                                        id: 2,
                                        amount: 1000.00,
                                        type: 'credit',
                                        wallet_name: 'Savings',
                                        reference: 'Salary',
                                        created_at: '2026-01-29T10:00:00Z',
                                        to_wallet: { currency: { symbol: '$' } }
                                    }
                                ]
                            }
                        }
                    })
                ]
            }
        })

        expect(wrapper.text()).toContain('Checking')
        expect(wrapper.text()).toContain('Coffee')
        expect(wrapper.text()).toContain('$50.00') // Check formatting if locale string used

        expect(wrapper.text()).toContain('Savings')
        expect(wrapper.text()).toContain('Salary')
        expect(wrapper.text()).toContain('$1,000.00')
    })

    it('handles empty state', () => {
        const wrapper = mount(RecentTransactions, {
            global: {
                plugins: [
                    vuetify,
                    createTestingPinia({
                        createSpy: vi.fn,
                        initialState: {
                            wallet: {
                                recentGlobalTransactions: []
                            }
                        }
                    })
                ]
            }
        })

        expect(wrapper.text()).toContain('No recent transactions')
    })
})
