import { mount } from '@vue/test-utils'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import TotalBalanceCard from '../TotalBalanceCard.vue'
import { createTestingPinia } from '@pinia/testing'
import { vi } from 'vitest'

const vuetify = createVuetify({ components, directives })

describe('TotalBalanceCard', () => {
    it('renders balance correctly from store', () => {
        const wrapper = mount(TotalBalanceCard, {
            global: {
                plugins: [
                    vuetify,
                    createTestingPinia({
                        createSpy: vi.fn,
                        initialState: {
                            wallet: {
                                wallets: [
                                    { id: 1, balance: 5000, currency: { code: 'USD', symbol: '$' } },
                                    { id: 2, balance: 1000, currency: { code: 'EUR', symbol: '€' } }
                                ]
                            }
                        },
                        stubActions: false // Allow getters to compute from state
                    })
                ]
            }
        })

        // Check if USD balance is rendered
        expect(wrapper.text()).toContain('5,000')
        expect(wrapper.text()).toContain('$')

        // Check if EUR balance is rendered
        expect(wrapper.text()).toContain('1,000')
        expect(wrapper.text()).toContain('€')
    })

    it('handles empty state', () => {
        const wrapper = mount(TotalBalanceCard, {
            global: {
                plugins: [
                    vuetify,
                    createTestingPinia({
                        createSpy: vi.fn,
                        initialState: {
                            wallet: {
                                totalBalanceByCurrency: {}
                            }
                        }
                    })
                ]
            }
        })

        expect(wrapper.text()).toContain('$0.00')
    })
})
