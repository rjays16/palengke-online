import { mount } from '@vue/test-utils'
import { createPinia } from 'pinia'
import SellerLandingPage from '@/views/SellerLandingPage.vue'
import { describe, expect, test } from 'vitest'

describe('SellerLandingPage.vue', () => {
  test('renders the welcome message', () => {
    const wrapper = mount(SellerLandingPage, {
      global: {
        plugins: [createPinia()],
      },
    })
    expect(wrapper.text()).toMatch('Welcome back!')
  })
})
