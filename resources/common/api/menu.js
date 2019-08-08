import request from 'common/utils/request'

export function getMenus(data) {
  return request({
    url: '/api/v1/menus',
    method: 'get',
    params: data
  })
}

export function getMenuLists() {
  return request({
    url: '/api/v1/menu-list',
    method: 'get'
  })
}

export function addMenu(data) {
  return request({
    url: '/api/v1/menus',
    method: 'post',
    data
  })
}

export function updateMenu(id, data) {
  return request({
    url: `/api/v1/menus/${id}`,
    method: 'put',
    data
  })
}

export function deleteMenu(id) {
  return request({
    url: `/api/v1/menus/${id}`,
    method: 'delete'
  })
}

export function myMenu() {
  return request({
    url: `/api/v1/my-menu`,
    method: 'get'
  })
}
