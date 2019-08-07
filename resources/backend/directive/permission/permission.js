import store from 'common/store'

export default {
  inserted(el, binding, vnode) {
    const {value} = binding
    const permissions = store.getters && store.getters.permissions

    if (permissions.indexOf('*') !== -1) {
      return true;
    }

    if (value && value instanceof Array && value.length > 0) {
      const permission = value

      const hasPermission = permissions.some(per => {
        return permission.includes(per)
      })

      if (!hasPermission) {
        el.parentNode && el.parentNode.removeChild(el)
      }
    } else {
      throw new Error(`need permissions!`)
    }
  }
}
