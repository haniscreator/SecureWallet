import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import ApprovalFilter from '../ApprovalFilter.vue'

const vuetify = createVuetify({ components, directives })

describe('ApprovalFilter', () => {
    const mountComponent = () => {
        return mount(ApprovalFilter, {
            global: {
                plugins: [vuetify]
            }
        })
    }

    it('emits apply-filter when Filter button is clicked', async () => {
        const wrapper = mountComponent()
        const referenceInput = wrapper.find('input[placeholder="Search Reference..."]')
        await referenceInput.setValue('Approval Ref')

        const filterButton = wrapper.findAll('button').filter(b => b.text().includes('Filter'))[0]
        await filterButton.trigger('click')

        expect(wrapper.emitted('apply-filter')).toBeTruthy()
        const emittedValues = wrapper.emitted('apply-filter')![0][0] as any
        expect(emittedValues.reference).toBe('Approval Ref')
    })

    it('emits apply-filter with empty values when Clear button is clicked', async () => {
        const wrapper = mountComponent()
        const referenceInput = wrapper.find('input[placeholder="Search Reference..."]')
        await referenceInput.setValue('Approval Ref')

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
        await referenceInput.setValue('Enter Approval Search')
        await referenceInput.trigger('keyup.enter')

        expect(wrapper.emitted('apply-filter')).toBeTruthy()
        expect((wrapper.emitted('apply-filter')![0][0] as any).reference).toBe('Enter Approval Search')
    })
})
