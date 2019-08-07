import store from 'common/store'

/**
 * @param {Array} value
 * @returns {Boolean}
 * @example see @/views/permission/directive.vue
 */
export default function checkPermission(value) {
  if (value && value instanceof Array && value.length > 0) {
    const permissions = store.getters && store.getters.permissions

    if (permissions.indexOf('*') !== -1) {
      return true;
    }

    const permission = value

    const hasPermission = permissions.some(per => {
      return permission.includes(per)
    })

    if (!hasPermission) {
      return false
    }
    return true
  } else {
    console.error(`need permissions!`)
    return false
  }
}
