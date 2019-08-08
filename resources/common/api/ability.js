import request from 'common/utils/request'

export function getAbilities(data) {
  return request({
    url: '/api/v1/abilities',
    method: 'get',
    params: data
  })
}

export function getAbilityGroups() {
  return request({
    url: '/api/v1/ability-groups',
    method: 'get'
  })
}

export function addAbility(data) {
  return request({
    url: '/api/v1/abilities',
    method: 'post',
    data
  })
}

export function updateAbility(id, data) {
  return request({
    url: `/api/v1/abilities/${id}`,
    method: 'put',
    data
  })
}

export function deleteAbility(id) {
  return request({
    url: `/api/v1/abilities/${id}`,
    method: 'delete'
  })
}
