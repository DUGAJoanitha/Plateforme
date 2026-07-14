import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import Dashboard from '../app/pages/dashboard.vue'
import DashboardKpi from '../app/pages/dashboard-kpi.vue'
import Finance from '../app/pages/finance.vue'
import Projects from '../app/pages/projects.vue'
import Login from '../app/pages/login.vue'
import Signup from '../app/pages/signup.vue'

describe('Frontend Pages', () => {
  it('Dashboard page mounts properly', () => {
    const wrapper = mount(Dashboard)
    expect(wrapper.exists()).toBe(true)
  })

  it('Dashboard KPI page mounts properly', () => {
    const wrapper = mount(DashboardKpi)
    expect(wrapper.exists()).toBe(true)
  })
  
  it('Finance page mounts properly', () => {
    const wrapper = mount(Finance)
    expect(wrapper.exists()).toBe(true)
  })

  it('Projects page mounts properly', () => {
    const wrapper = mount(Projects)
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
})
