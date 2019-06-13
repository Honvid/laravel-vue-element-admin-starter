const state = {
  showSignInDialog: false,
  showSignUpDialog: false,
}

const mutations = {
  SHOW_SIGN_IN_DIALOG: (state, status) => {
    state.showSignInDialog = status
  },
  SHOW_SIGN_UP_DIALOG: (state, status) => {
    state.showSignUpDialog = status
  }
}

const actions = {
  setShowSignInDialog({ commit }, status) {
    commit('SHOW_SIGN_IN_DIALOG', status)
  },
  setShowSignUpDialog({ commit }, status) {
    commit('SHOW_SIGN_UP_DIALOG', status)
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions
}
