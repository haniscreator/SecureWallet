import { mount } from '@vue/test-utils'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import WalletWidget from '../WalletWidget.vue'
import { vi } from 'vitest'

const vuetify = createVuetify({ components, directives })

describe('WalletWidget', () => {
    const defaultProps = {
        name: 'Main Wallet',
        amount: '5,000.00',
        currency: 'USD',
        symbol: '$',
        usersCount: 3,
        color: 'blue'
    }

    it('renders wallet details correctly', () => {
        const wrapper = mount(WalletWidget, {
            global: {
                plugins: [vuetify]
            },
            props: defaultProps
        })

        // Check Name
        expect(wrapper.text()).toContain('Main Wallet')

        // Check Amount and Symbol
        expect(wrapper.text()).toContain('$5,000.00')

        // Check Currency Code
        expect(wrapper.text()).toContain('USD')

        // Check Users Count
        expect(wrapper.text()).toContain('3 Users Assigned')
    })

    it('handles default users count', () => {
        const wrapper = mount(WalletWidget, {
            global: {
                plugins: [vuetify]
            },
            props: {
                ...defaultProps,
                usersCount: undefined
            }
        })

        expect(wrapper.text()).toContain('0 Users Assigned')
    })

    it('applies color prop to avatar', () => {
        const wrapper = mount(WalletWidget, {
            global: {
                plugins: [vuetify]
            },
            props: {
                ...defaultProps,
                color: 'red'
            }
        })

        // We check if the VAvatar or Icon receives the color class or style
        // Since VAvatar uses classes for colors in Vuetify 3 (text-red etc or bg-red) or inline styles
        // Let's verify html contains the color class or style
        // A safer check is usually creating a snapshot or checking attributes if possible. 
        // But simply checking if it renders without error is often enough for UI components unless logic depends on color.

        // Vuetify 3 applies color classes like 'text-red' or 'bg-red' depending on variant
        expect(wrapper.html()).toContain('text-red')
    })
})
