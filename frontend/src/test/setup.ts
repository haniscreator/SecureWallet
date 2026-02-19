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

// Mock visualViewport
if (typeof window !== 'undefined' && !window.visualViewport) {
    (window as any).visualViewport = {
        offsetLeft: 0,
        offsetTop: 0,
        pageLeft: 0,
        pageTop: 0,
        width: 1024,
        height: 768,
        scale: 1,
        addEventListener: vi.fn(),
        removeEventListener: vi.fn(),
        dispatchEvent: vi.fn(),
    }
}

// Mock IntersectionObserver
class IntersectionObserverMock {
    observe = vi.fn()
    unobserve = vi.fn()
    disconnect = vi.fn()
}
Object.defineProperty(window, 'IntersectionObserver', {
    writable: true,
    configurable: true,
    value: IntersectionObserverMock
})
Object.defineProperty(global, 'IntersectionObserver', {
    writable: true,
    configurable: true,
    value: IntersectionObserverMock
})

// Mock lottie-web
vi.mock('lottie-web', () => ({
    default: {
        loadAnimation: vi.fn().mockReturnValue({
            destroy: vi.fn(),
            stop: vi.fn(),
            play: vi.fn(),
            setSpeed: vi.fn(),
            setDirection: vi.fn(),
        }),
    },
}))
