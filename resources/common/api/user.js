import request from 'common/utils/request'

export function login(data) {
  return request({
    url: '/api/v1/user/login',
    method: 'post',
    data
  })
}

export function register(data) {
  return request({
    url: '/api/v1/user/register',
    method: 'post',
    data
  })
}

export function getInfo() {
  return request({
    url: '/api/v1/user/info',
    method: 'get',
  })
}

export function logout() {
  return request({
    url: '/api/v1/user/logout',
    method: 'get'
  })
}
