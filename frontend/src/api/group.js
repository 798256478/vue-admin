import request from '@/utils/request'

export function getGroupList(query) {
  return request({
    url: '/admin/group/list',
    method: 'get',
    params: query
  })
}

export function getAllGroup() {
  return request({
    url: '/admin/group/all',
    method: 'get',
    params: {}
  })
}


export function addGroup(data) {
  return request({
    url: '/admin/group/add',
    method: 'post',
    data
  })
}

export function editGroup(data) {
  return request({
    url: '/admin/group/edit',
    method: 'post',
    data
  })
}

export function deleteGroup(data) {
  return request({
    url: '/admin/group/delete',
    method: 'post',
    data
  })
}
