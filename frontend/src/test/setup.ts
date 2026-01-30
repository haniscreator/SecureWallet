import { config } from '@vue/test-utils'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import ResizeObserver from 'resize-observer-polyfill'
import { vi } from 'vitest'

// Polyfill ResizeObserver
global.ResizeObserver = ResizeObserver

const vuetify = createVuetify({
    components,
    directives,
})

// Configure global plugins for all tests
config.global.plugins = [vuetify]
// Mock getBoundingClientRect
Element.prototype.getBoundingClientRect = vi.fn(() => {
    return {
        width: 120,
        height: 120,
        top: 0,
        left: 0,
        bottom: 0,
        right: 0,
    } as DOMRect;
});
