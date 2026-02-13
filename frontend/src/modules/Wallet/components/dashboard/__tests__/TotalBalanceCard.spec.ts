import { mount } from '@vue/test-utils'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import TotalBalanceCard from '../TotalBalanceCard.vue'
import { createTestingPinia } from '@pinia/testing'
import { vi, describe, it, expect } from 'vitest'

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
                                totalBalance: {
                                    USD: { amount: 5000, symbol: '$' },
                                    EUR: { amount: 1000, symbol: '€' }
                                }
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
                                totalBalance: {}
                            }
                        }
                    })
                ]
            }
        })

        expect(wrapper.text()).toContain('$0.00')
    })

    it('shows skeleton loader when loading', () => {
        const wrapper = mount(TotalBalanceCard, {
            global: {
                plugins: [
                    vuetify,
                    createTestingPinia({
                        createSpy: vi.fn,
                        initialState: {
                            wallet: {
                                totalBalanceLoading: true,
                                totalBalance: {}
                            }
                        }
                    })
                ]
            }
        })

        // Check for skeleton loader presence
        // Vuetify skeleton loader usually renders a class .v-skeleton-loader
        expect(wrapper.find('.v-skeleton-loader').exists()).toBe(true)

        // Ensure $0.00 or other text is not shown
        expect(wrapper.text()).not.toContain('$0.00')
    })
})
