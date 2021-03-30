import * as ComponentMap from './components'

export default {
  install(Vue) {
    const $printPluginOutput = new Vue()

    // register all components globally
    Object.keys(ComponentMap).forEach(componentName =>
      Vue.component(componentName, ComponentMap[componentName]))

    // allow directives to access $printPluginOutput
    Vue.$printPluginOutput = $printPluginOutput

    // allow components to access $printPluginOutput
    Vue.prototype.$printPluginOutput = $printPluginOutput
  },
}