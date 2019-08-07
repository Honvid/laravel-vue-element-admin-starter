import router from './router'
import store from '../common/store'
import {Message} from 'element-ui'
import NProgress from 'nprogress' // progress bar
import 'nprogress/nprogress.css' // progress bar style
import {getToken} from 'common/utils/auth' // get token from cookie
import getPageTitle from 'common/utils/get-page-title'

NProgress.configure({showSpinner: false}) // NProgress Configuration

const whiteList = ['/login', '/auth-redirect'] // no redirect whitelist

router.beforeEach(async (to, from, next) => {
  NProgress.start()
  document.title = getPageTitle(to.meta.title)
  const hasToken = getToken()
  if (hasToken) {
    const hasName = store.getters.name && store.getters.name.length > 0
    if (hasName) {
      next()
    } else {
      try {
        await store.dispatch('user/getInfo')
        await store.dispatch('menu/myMenu')
        next({...to, replace: true})
      } catch (error) {
        await store.dispatch('user/resetToken')
        Message.error(error || 'Has Error')
        next({path: '/'})
        NProgress.done()
      }
    }
  }
  next()
})

// NProgress.done()
router.afterEach(() => {
  NProgress.done()
})
