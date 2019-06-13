import Vue from 'vue'
import VueRouter from 'vue-router'
import Layout from '^/layouts/index.vue'

/**
 * Extends Vue to use Vue Router
 */
Vue.use(VueRouter)

/**
 * Makes a new VueRouter that we will use to run all of the routes for the app.
 */
export default new VueRouter({
  mode:"history",
  routes: [
    {
      path: '',
      component: Layout,
      children: [
        {
          path: '/',
          component: () => import('^/views/home/index'),
          name: 'home',
          meta: { title: 'home', icon: 'home', affix: true }
        }
      ]
    },
  ]
});