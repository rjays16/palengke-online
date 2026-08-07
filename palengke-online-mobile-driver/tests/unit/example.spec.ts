import { mount } from '@vue/test-utils'
import HomePage from '@/views/HomePage.vue'
import { describe, expect, test } from 'vitest'

describe('HomePage.vue', () => {
  test('renders the welcome message', () => {
    const wrapper = mount(HomePage)
    expect(wrapper.text()).toMatch('Welcome back!')
  })
})
