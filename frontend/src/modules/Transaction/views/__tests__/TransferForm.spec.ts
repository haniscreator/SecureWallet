import { describe, it, expect, vi, beforeEach } from 'vitest'
import { mount, flushPromises } from '@vue/test-utils'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import TransferForm from '../TransferForm.vue'
import { createTestingPinia } from '@pinia/testing'
import { walletApi } from '@/modules/Wallet/api'
import { transactionApi } from '@/modules/Transaction/api'
import { useRouter, useRoute } from 'vue-router'

const mockPush = vi.fn()
// Mock vue-router
vi.mock('vue-router', () => ({
    useRouter: vi.fn(() => ({
        push: mockPush
    })),
    useRoute: vi.fn(() => ({
        query: {}
    }))
}))

// Mock APIs
vi.mock('@/modules/Wallet/api', () => ({
    walletApi: {
        getWallets: vi.fn(),
        getTransferTargets: vi.fn(),
        validateAddress: vi.fn()
    }
}))

vi.mock('@/modules/Transaction/api', () => ({
    transactionApi: {
        initiateTransfer: vi.fn()
    }
}))

const vuetify = createVuetify({
    components,
    directives,
})

const mockWallets = [
    {
        id: 1,
        name: 'Main Wallet',
        balance: 1000.00,
        available_balance: 800.00,
        status: true,
        currency_id: 1,
        created_at: '2026-02-19T10:00:00Z',
        currency: { symbol: '$', code: 'USD' }
    }
]

const mockTargets = [
    {
        id: 2,
        name: 'Target Wallet',
        balance: 0,
        available_balance: 0,
        currency_id: 1,
        status: true,
        created_at: '2026-02-19T10:00:00Z',
        currency: { code: 'USD' },
        users: [{ name: 'Jane Doe' }]
    }
]

describe('TransferForm', () => {
    beforeEach(() => {
        vi.clearAllMocks()
        vi.mocked(walletApi.getWallets).mockResolvedValue({ data: mockWallets } as any)
        vi.mocked(walletApi.getTransferTargets).mockResolvedValue({ data: mockTargets } as any)
    })

    const mountWithPlugins = (routeQuery = {}) => {
        vi.mocked(useRoute).mockReturnValue({ query: routeQuery } as any)

        // Wrap in VApp for full Vuetify support
        return mount({
            template: '<v-app><TransferForm /></v-app>',
            components: { TransferForm }
        }, {
            global: {
                stubs: {
                    'v-expand-transition': { template: '<div><slot /></div>' }
                },
                plugins: [
                    vuetify,
                    createTestingPinia({
                        createSpy: vi.fn,
                        initialState: {
                            notification: { notifications: [] }
                        }
                    })
                ]
            }
        })
    }

    it('renders source wallet info when redirected from wallet list', async () => {
        const root = mountWithPlugins({ from: '1' })
        const wrapper = root.findComponent(TransferForm)

        await flushPromises()
        await wrapper.vm.$nextTick()

        expect(wrapper.text()).toContain('Main Wallet')
        expect(wrapper.text()).toContain('Source Wallet')
        expect(wrapper.text()).toContain('$800')
    })

    it('allows switching between internal and external transfer types', async () => {
        const root = mountWithPlugins({ from: '1' })
        await flushPromises()
        const wrapper = root.findComponent(TransferForm)

        const cards = wrapper.findAll('.hover-card')
        const internalCard = cards[0]
        const externalCard = cards[1]

        await internalCard.trigger('click')
        await flushPromises()
        await wrapper.vm.$nextTick()

        expect(wrapper.text()).toContain('Choose destination wallet')

        await externalCard.trigger('click')
        await flushPromises()
        await wrapper.vm.$nextTick()
        expect(wrapper.text()).toContain('External Wallet Address')
    })

    it('completes an internal transfer successfully', async () => {
        const root = mountWithPlugins({ from: '1' })
        await flushPromises()
        const wrapper = root.findComponent(TransferForm)

        // Select internal
        const internalCard = wrapper.findAll('.hover-card')[0]
        await internalCard.trigger('click')
        await flushPromises()

        // Fill details
        await wrapper.findComponent(components.VSelect).setValue(2)
        await wrapper.find('input[type="number"]').setValue(100)
        await wrapper.findComponent(components.VTextarea).setValue('Internal ref')

        // Wait for validation
        await flushPromises()
        await (wrapper.vm as any).$nextTick()

        // Mock success response
        vi.mocked(transactionApi.initiateTransfer).mockResolvedValue({ message: 'Success', transaction: {} } as any)

        // Use the exposed method directly to avoid issues with disabled button state in JSDOM
        await (wrapper.vm as any).submitTransfer()
        await flushPromises()

        expect(transactionApi.initiateTransfer).toHaveBeenCalledWith({
            source_wallet_id: 1,
            type: 'internal',
            to_wallet_id: 2,
            to_address: null,
            amount: 100,
            description: 'Internal ref'
        })

        expect(mockPush).toHaveBeenCalledWith('/wallets')
    })

    it('validates external address and handles submission', async () => {
        const root = mountWithPlugins({ from: '1' })
        await flushPromises()
        const wrapper = root.findComponent(TransferForm)

        // Select external
        const externalCard = wrapper.findAll('.hover-card')[1]
        await externalCard.trigger('click')
        await flushPromises()

        // Fill address
        const addressInput = wrapper.find('input[placeholder="e.g. 0x123..."]')
        await addressInput.setValue('0xExternalAddress')

        // Mock validation
        vi.mocked(walletApi.validateAddress).mockResolvedValue({
            data: { valid: true, exists: false, message: 'Valid address' }
        } as any)

        await addressInput.trigger('blur')
        await flushPromises()

        // Fill amount
        await wrapper.find('input[type="number"]').setValue(50)

        // Wait for form validation state to update
        await flushPromises()
        await (wrapper.vm as any).$nextTick()

        // Mock success
        vi.mocked(transactionApi.initiateTransfer).mockResolvedValue({ message: 'Success', transaction: {} } as any)

        await (wrapper.vm as any).submitTransfer()
        await flushPromises()

        expect(transactionApi.initiateTransfer).toHaveBeenCalledWith({
            source_wallet_id: 1,
            type: 'external',
            to_wallet_id: null,
            to_address: '0xExternalAddress',
            amount: 50,
            description: ''
        })

        expect(mockPush).toHaveBeenCalledWith('/wallets')
    })

    it('shows error message if source wallet is frozen', async () => {
        const frozenWallet = [{ ...mockWallets[0], status: false }]
        vi.mocked(walletApi.getWallets).mockResolvedValue({ data: frozenWallet } as any)

        const root = mountWithPlugins({ from: '1' })
        await flushPromises()
        const wrapper = root.findComponent(TransferForm)

        // Error only shows after type is selected
        const internalCard = wrapper.findAll('.hover-card')[0]
        await internalCard.trigger('click')
        await flushPromises()
        await wrapper.vm.$nextTick()

        expect(wrapper.text()).toContain('This wallet is frozen and cannot initiate transfers.')
    })
})
