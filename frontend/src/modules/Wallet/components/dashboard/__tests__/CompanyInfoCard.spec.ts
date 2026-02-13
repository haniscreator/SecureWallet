import { mount } from '@vue/test-utils'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import CompanyInfoCard from '../CompanyInfoCard.vue'
import { createTestingPinia } from '@pinia/testing'
import { vi, describe, it, expect } from 'vitest'

const vuetify = createVuetify({ components, directives })

describe('CompanyInfoCard', () => {
    it('renders company stats and info correctly', () => {
        const wrapper = mount(CompanyInfoCard, {
            global: {
                plugins: [
                    vuetify,
                    createTestingPinia({
                        createSpy: vi.fn,
                        initialState: {
                            wallet: {
                                wallets: [1, 2, 3], // Mocking length
                                totalBalance: { USD: {}, EUR: {} }, // Mocking keys length
                                dashboardTotalItems: 5 // Mocking length
                            },
                            user: {
                                members: [1, 2, 3, 4] // Mocking length
                            },
                            auth: {
                                user: {
                                    company_name: 'Test Corp',
                                    email: 'test@corp.com'
                                }
                            }
                        }
                    })
                ]
            }
        })

        // Check Title
        expect(wrapper.text()).toContain('Company')

        // Check Stats
        expect(wrapper.text()).toContain('Total Wallets:')
        expect(wrapper.text()).toContain('3') // wallets.length

        expect(wrapper.text()).toContain('Total Users:')
        expect(wrapper.text()).toContain('4') // members.length

        expect(wrapper.text()).toContain('Currencies:')
        expect(wrapper.text()).toContain('2') // keys length

        expect(wrapper.text()).toContain('Tx Count:')
        expect(wrapper.text()).toContain('5') // recentRemoteTransactions length

        // Check Company Details
        expect(wrapper.text()).toContain('Test Corp')
        expect(wrapper.text()).toContain('test@corp.com')

        // Check Address (Static)
        expect(wrapper.text()).toContain('123 Market St')
    })
})
