import { describe, it, expect, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import ApprovalTable from '../ApprovalTable.vue'
import { createTestingPinia } from '@pinia/testing'

const vuetify = createVuetify({ components, directives })

const mockApprovals = [
    {
        id: 10,
        amount: 1000.00,
        reference: 'Large Transfer 1',
        created_at: '2026-02-19T10:00:00Z',
        type: 'debit' as const,
        from_wallet: {
            name: 'Company Wallet',
            currency: { symbol: '$' }
        },
        to_wallet: {
            name: 'Vendor Wallet',
            currency: { symbol: '$' },
            is_external: false
        },
        user: { id: 2, name: 'Jane Smith' }
    },
    {
        id: 11,
        amount: 500.00,
        reference: 'External Transfer',
        created_at: '2026-02-19T11:00:00Z',
        type: 'debit' as const,
        from_wallet: {
            name: 'Company Wallet',
            currency: { symbol: '$' }
        },
        to_wallet: {
            name: 'External Wallet',
            address: '0xabcdef1234567890',
            is_external: true
        },
        user: { id: 3, name: 'Alice Wong' }
    }
]

describe('ApprovalTable', () => {
    const defaultProps = {
        loading: false,
        items: mockApprovals,
        totalItems: 2,
        page: 1,
        itemsPerPage: 10
    }

    const mountWithPlugins = (props = {}) => {
        return mount(ApprovalTable, {
            props: { ...defaultProps, ...props },
            global: {
                plugins: [
                    vuetify,
                    createTestingPinia({ createSpy: vi.fn })
                ]
            }
        })
    }

    it('renders approval list correctly', () => {
        const wrapper = mountWithPlugins()

        expect(wrapper.text()).toContain('Large Transfer 1')
        expect(wrapper.text()).toContain('Jane Smith')
        expect(wrapper.text()).toContain('$ 1,000.00')

        // Truncated address for external wallet
        expect(wrapper.text()).toContain('0xabcd...7890')
    })

    it('emits approve when Approve button is clicked', async () => {
        const wrapper = mountWithPlugins()
        const approveButtons = wrapper.findAll('button').filter(b => b.text().includes('Approve'))

        await approveButtons[0].trigger('click')

        expect(wrapper.emitted('approve')).toBeTruthy()
        expect(wrapper.emitted('approve')![0][0]).toEqual(mockApprovals[0])
    })

    it('emits reject when Reject button is clicked', async () => {
        const wrapper = mountWithPlugins()
        const rejectButtons = wrapper.findAll('button').filter(b => b.text().includes('Reject'))

        await rejectButtons[0].trigger('click')

        expect(wrapper.emitted('reject')).toBeTruthy()
        expect(wrapper.emitted('reject')![0][0]).toEqual(mockApprovals[0])
    })

    it('emits view-details when View button is clicked', async () => {
        const wrapper = mountWithPlugins()
        const viewButtons = wrapper.findAll('button').filter(b => b.text().includes('View'))

        await viewButtons[0].trigger('click')

        expect(wrapper.emitted('view-details')).toBeTruthy()
        expect(wrapper.emitted('view-details')![0][0]).toEqual(mockApprovals[0])
    })

    it('emits update:page when pagination changes', async () => {
        const wrapper = mountWithPlugins({ totalItems: 20 })
        const pagination = wrapper.findComponent({ name: 'VPagination' })
        await pagination.vm.$emit('update:modelValue', 2)

        expect(wrapper.emitted('update:page')).toBeTruthy()
        expect(wrapper.emitted('update:page')![0]).toEqual([2])
    })
})
