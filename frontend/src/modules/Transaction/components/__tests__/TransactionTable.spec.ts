import { describe, it, expect, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import TransactionTable from '../TransactionTable.vue'
import { createTestingPinia } from '@pinia/testing'
import { useAuthStore } from '@/modules/Auth/store'

const vuetify = createVuetify({ components, directives })

const mockTransactions = [
    {
        id: 1,
        amount: 100.00,
        type: 'debit' as const,
        reference: 'Test Transaction 1',
        created_at: '2026-02-19T10:00:00Z',
        from_wallet: {
            name: 'Source Wallet',
            currency: { symbol: '$' }
        },
        to_wallet: {
            name: 'Dest Wallet',
            currency: { symbol: '$' },
            is_external: false
        },
        status: { name: 'Completed', code: 'completed' },
        user: { id: 1, name: 'John Doe' }
    },
    {
        id: 2,
        amount: 50.00,
        type: 'credit' as const,
        reference: 'Test Transaction 2',
        created_at: '2026-02-19T11:00:00Z',
        from_wallet: {
            name: 'External Source',
            currency: { symbol: '€' }
        },
        to_wallet: {
            name: 'External Wallet',
            address: '0x1234567890abcdef',
            currency: { symbol: '€' },
            is_external: true
        },
        status: { name: 'Pending', code: 'pending' },
        user: { id: 1, name: 'John Doe' }
    }
]

describe('TransactionTable', () => {
    const defaultProps = {
        loading: false,
        items: mockTransactions,
        totalItems: 2,
        page: 1,
        itemsPerPage: 10
    }

    const mountWithPlugins = (props = {}) => {
        return mount(TransactionTable, {
            props: { ...defaultProps, ...props },
            global: {
                plugins: [
                    vuetify,
                    createTestingPinia({
                        createSpy: vi.fn,
                        initialState: {
                            auth: {
                                user: { id: 1 }
                            }
                        }
                    })
                ]
            }
        })
    }

    it('renders transaction data correctly', () => {
        const wrapper = mountWithPlugins()

        expect(wrapper.text()).toContain('Source Wallet')
        expect(wrapper.text()).toContain('Dest Wallet')
        expect(wrapper.text()).toContain('Test Transaction 1')
        expect(wrapper.text()).toContain('$100')

        // External wallet address truncation
        expect(wrapper.text()).toContain('0x123456...abcdef')
    })

    it('emits update:page when pagination changes', async () => {
        const wrapper = mountWithPlugins({ totalItems: 20 })
        const pagination = wrapper.findComponent({ name: 'VPagination' })
        await pagination.vm.$emit('update:modelValue', 2)

        expect(wrapper.emitted('update:page')).toBeTruthy()
        expect(wrapper.emitted('update:page')![0]).toEqual([2])
    })

    it('emits view-details when VIEW button is clicked', async () => {
        const wrapper = mountWithPlugins()
        const viewButtons = wrapper.findAll('button').filter(b => b.text().includes('VIEW'))

        await viewButtons[0].trigger('click')

        expect(wrapper.emitted('view-details')).toBeTruthy()
        expect(wrapper.emitted('view-details')![0][0]).toEqual(mockTransactions[0])
    })

    it('emits cancel-transaction when CANCEL button is clicked', async () => {
        const wrapper = mountWithPlugins()
        // Second row has status 'pending' and user matches auth user
        const cancelButtons = wrapper.findAll('button').filter(b => b.text().includes('CANCEL'))

        await cancelButtons[0].trigger('click')

        expect(wrapper.emitted('cancel-transaction')).toBeTruthy()
        expect(wrapper.emitted('cancel-transaction')![0][0]).toEqual(mockTransactions[1])
    })

    it('shows loading state in v-data-table-server', () => {
        const wrapper = mountWithPlugins({ loading: true })
        const dataTable = wrapper.findComponent({ name: 'VDataTableServer' })
        expect(dataTable.props('loading')).toBe(true)
    })

    it('shows "No transactions found" when items are empty', () => {
        const wrapper = mountWithPlugins({ items: [], totalItems: 0 })
        expect(wrapper.text()).toContain('No transactions found.')
    })
})
