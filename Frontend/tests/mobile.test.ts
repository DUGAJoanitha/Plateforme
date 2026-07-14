import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import Activities from '../../mobile/activities.vue'
import Projects from '../../mobile/projects.vue'
import Dashboard from '../../mobile/dashboard.vue'
import Login from '../../mobile/login.vue'
import Signup from '../../mobile/signup.vue'
import Kpis from '../../mobile/kpis.vue'

describe('Mobile Pages', () => {
  it('Activities page mounts properly', () => {
    const wrapper = mount(Activities)
    expect(wrapper.exists()).toBe(true)
  })

  it('Projects page mounts properly', () => {
    const wrapper = mount(Projects)
    expect(wrapper.exists()).toBe(true)
  })
  
  it('Dashboard page mounts properly', () => {
    const wrapper = mount(Dashboard)
    expect(wrapper.exists()).toBe(true)
  })

  it('Login page mounts properly', () => {
    const wrapper = mount(Login)
    expect(wrapper.exists()).toBe(true)
  })

  it('Signup page mounts properly', () => {
    const wrapper = mount(Signup)
    expect(wrapper.exists()).toBe(true)
  })

  it('Kpis page mounts properly', () => {
    const wrapper = mount(Kpis)
    expect(wrapper.exists()).toBe(true)
  })
})
