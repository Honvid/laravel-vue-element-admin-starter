import request from 'common/utils/request'

export function login(data) {
  return request({
    url: '/api/v1/users/login',
    method: 'post',
    data
  })
}

export function register(data) {
  return request({
    url: '/api/v1/users/register',
    method: 'post',
    data
  })
}

export function getInfo() {
  return request({
    url: '/api/v1/users/info',
    method: 'get',
  })
}

export function logout() {
  return request({
    url: '/api/v1/users/logout',
    method: 'get'
  })
}

export function getUsers(data) {
  return request({
    url: '/api/v1/users',
    method: 'get',
    params: data
  })
}

export function addUser(data) {
  return request({
    url: '/api/v1/users',
    method: 'post',
    data
  })
}

export function updateUser(id, data) {
  return request({
    url: `/api/v1/users/${id}`,
    method: 'put',
    data
  })
}

export function deleteUser(id) {
  return request({
    url: `/api/v1/users/${id}`,
    method: 'delete'
  })
}
