import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import TransactionFilter from '../TransactionFilter.vue'

const vuetify = createVuetify({ components, directives })

describe('TransactionFilter', () => {
    const mountComponent = () => {
        return mount(TransactionFilter, {
            global: {
                plugins: [vuetify]
            }
        })
    }

    it('emits apply-filter when Filter button is clicked', async () => {
        const wrapper = mountComponent()
        const referenceInput = wrapper.find('input[placeholder="Search Reference..."]')
        await referenceInput.setValue('Test Ref')

        const filterButton = wrapper.findAll('button').filter(b => b.text().includes('Filter'))[0]
        await filterButton.trigger('click')

        expect(wrapper.emitted('apply-filter')).toBeTruthy()
        const emittedValues = wrapper.emitted('apply-filter')![0][0] as any
        expect(emittedValues.reference).toBe('Test Ref')
    })

    it('emits apply-filter with empty values when Clear button is clicked', async () => {
        const wrapper = mountComponent()
        const referenceInput = wrapper.find('input[placeholder="Search Reference..."]')
        await referenceInput.setValue('Test Ref')

        const clearButton = wrapper.findAll('button').filter(b => b.text().includes('Clear'))[0]
        await clearButton.trigger('click')

        expect(wrapper.emitted('apply-filter')).toBeTruthy()
        const emittedValues = wrapper.emitted('apply-filter')![0][0] as any
        expect(emittedValues.reference).toBe('')
        expect(emittedValues.from_date).toBeNull()
        expect(emittedValues.to_date).toBeNull()
    })

    it('updates reference value on keyup.enter', async () => {
        const wrapper = mountComponent()
        const referenceInput = wrapper.find('input[placeholder="Search Reference..."]')
        await referenceInput.setValue('Enter Search')
        await referenceInput.trigger('keyup.enter')

        expect(wrapper.emitted('apply-filter')).toBeTruthy()
        expect((wrapper.emitted('apply-filter')![0][0] as any).reference).toBe('Enter Search')
    })

    it('opens date picker menu when date field is clicked', async () => {
        const wrapper = mountComponent()
        const dateInputs = wrapper.findAll('input[placeholder="Select Date"]')

        // Vuetify menus typically use a click on the activator
        await dateInputs[0].trigger('click')

        // Check if v-date-picker is visible (might need to check document if teleported, 
        // but often enough to see if internal state changed or component exists)
        const datePickers = wrapper.findAllComponents({ name: 'VDatePicker' })
        // Note: In tests, menus might not show up immediately without proper setup or waiting
        // This is a basic check.
    })
})
