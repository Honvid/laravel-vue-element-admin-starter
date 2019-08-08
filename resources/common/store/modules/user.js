import { login, register, logout, getInfo } from 'common/api/user'
import { getToken, setToken, removeToken } from 'common/utils/auth'
import router, { resetRouter } from '@/router'

const state = {
  token: getToken(),
  name: '',
  avatar: '',
  permissions: []
}

const mutations = {
  SET_TOKEN: (state, token) => {
    state.token = token
  },
  SET_NAME: (state, name) => {
    state.name = name
  },
  SET_AVATAR: (state, avatar) => {
    state.avatar = avatar
  },
  SET_PERMISSIONS: (state, permissions) => {
    state.permissions = permissions
  }
}

const actions = {
  // user login
  login({ commit }, userInfo) {
    const { username, password } = userInfo
    return new Promise((resolve, reject) => {
      login({ username: username.trim(), password: password }).then(response => {
        if(response.code && response.code > 0) {
          reject(response.message)
          return
        }
        const {access_token, permissions, name, avatar } = response
        commit('SET_TOKEN', access_token)
        commit('SET_PERMISSIONS', permissions)
        commit('SET_NAME', name)
        commit('SET_AVATAR', avatar)
        setToken(access_token)
        resolve()
      }).catch(error => {
        reject(error)
      })
    })
  },

  register({ commit }, userInfo) {
    const { username, email, password, confirm } = userInfo
    return new Promise((resolve, reject) => {
      register({
        username: username.trim(),
        email: email.trim(),
        password: password,
        confirm: confirm }).then(response => {
        const {access_token, permissions, name, avatar } = response
        commit('SET_TOKEN', access_token)
        commit('SET_PERMISSIONS', permissions)
        commit('SET_NAME', name)
        commit('SET_AVATAR', avatar)
        setToken(access_token)
        resolve()
      }).catch(error => {
        reject(error)
      })
    })
  },

  // get user info
  getInfo({ commit, state }) {
    return new Promise((resolve, reject) => {
      getInfo().then(response => {
        if (!response) {
          reject('Verification failed, please Login again.')
          return
        }
        const { permissions, name, avatar } = response
        if (!permissions || permissions.length <= 0) {
          reject('getInfo: permissions must be a non-null array!')
          return
        }

        commit('SET_PERMISSIONS', permissions)
        commit('SET_NAME', name)
        commit('SET_AVATAR', avatar)
        resolve(response)
      }).catch(error => {
        console.log(error)
        reject(error)
      })
    })
  },

  // user logout
  logout({ commit, state }) {
    return new Promise((resolve, reject) => {
      logout().then(() => {
        commit('SET_TOKEN', '')
        commit('SET_ROLES', [])
        commit('SET_NAME', '')
        commit('SET_AVATAR', '')
        commit('SET_INTRODUCTION', '')
        removeToken()
        resetRouter()
        resolve()
      }).catch(error => {
        reject(error)
      })
    })
  },

  // remove token
  resetToken({ commit }) {
    return new Promise(resolve => {
      commit('SET_TOKEN', '')
      commit('SET_PERMISSIONS', [])
      removeToken()
      resolve()
    })
  },
}

export default {
  namespaced: true,
  state,
  mutations,
  actions
}
