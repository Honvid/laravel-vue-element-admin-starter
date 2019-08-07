const getters = {
  sidebar: state => state.app.sidebar,
  language: state => state.app.language,
  size: state => state.app.size,
  device: state => state.app.device,
  visitedViews: state => state.tagsView.visitedViews,
  cachedViews: state => state.tagsView.cachedViews,
  token: state => state.user.token,
  avatar: state => state.user.avatar,
  name: state => state.user.name,
  permissions: state => state.user.permissions,
  backend_menus: state => state.menu.backend,
  frontend_menus: state => state.menu.frontend,
  errorLogs: state => state.errorLog.logs,
  show_sign_in_dialog: state => state.common.showSignInDialog,
  show_sign_up_dialog: state => state.common.showSignUpDialog,
}
export default getters
