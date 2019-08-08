import { myMenu } from 'common/api/menu'

const state = {
  backend: [],
  frontend: []
}

const mutations = {
  SET_BACKEND: (state, menus) => {
    state.backend = menus
  },
  SET_FRONTEND:  (state, menus) => {
    state.frontend = menus
  }
}

const actions = {
  // get user info
  myMenu({ commit, state }) {
    return new Promise((resolve, reject) => {
      myMenu().then(response => {
        if (!response) {
          reject('Verification failed, please Login again.')
        }

        if(!response.data){
          reject('there need menu, please apply from administrator.')
        }

        for (const menu of response.data) {
          if(menu.uri === 'backend' && menu.children){
            commit('SET_BACKEND', menu.children)
          }
          if(menu.uri === 'frontend' && menu.children){
            commit('SET_FRONTEND', menu.children)
          }
        }
        resolve(response)
      }).catch(error => {
        reject(error)
      })
    })
  },
}

export default {
  namespaced: true,
  state,
  mutations,
  actions
}
