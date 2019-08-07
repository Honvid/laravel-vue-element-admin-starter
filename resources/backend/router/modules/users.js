/** When your routing table is too long, you can split it into small modules **/

import Layout from '@/layout'

const usersRouter = {
  path: '/users',
  component: Layout,
  redirect: '/users/user',
  name: 'UserManage',
  meta: {
    title: 'UserManage',
    icon: 'users'
  },
  children: [
    {
      path: 'user',
      component: () => import('@/views/users/user'),
      name: 'User',
      meta: {title: 'User'}
    },
    {
      path: 'role',
      name: 'Role',
      component: () => import('@/views/users/role'),
      meta: {title: 'Role'}
    },
    {
      path: 'permission',
      component: () => import('@/views/users/permission'),
      name: 'Permission',
      meta: {title: 'Permission'}
    },
    {
      path: 'menu',
      component: () => import('@/views/users/menu'),
      name: 'Menu',
      meta: {title: 'Menu'}
    }
  ]
}
export default usersRouter
