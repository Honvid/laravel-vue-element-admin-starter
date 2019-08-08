import request from 'common/utils/request'

export function getRoles(data) {
  return request({
    url: '/api/v1/roles',
    method: 'get',
    params: data
  })
}

export function addRole(data) {
  return request({
    url: '/api/v1/roles',
    method: 'post',
    data
  })
}

export function updateRole(id, data) {
  return request({
    url: `/api/v1/roles/${id}`,
    method: 'put',
    data
  })
}

export function deleteRole(id) {
  return request({
    url: `/api/v1/roles/${id}`,
    method: 'delete'
  })
}
