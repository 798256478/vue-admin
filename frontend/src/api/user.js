import request from '@/utils/request'

export function login(data) {
  return request({
    url: '/admin/login',
    method: 'post',
    data
  })
}

export function getInfo(token) {
  return request({
    url: '/admin/userInfo',
    method: 'get',
    params: {  }
  })
}

export function logout() {
  return request({
    url: '/admin/logout',
    method: 'get'
  })
}


export function userList(query) {
  return request({
    url: '/admin/user/list',
    method: 'get',
    params: query
  })
}


export function addUser(data) {
  return request({
    url: '/admin/user/add',
    method: 'post',
    data
  })
}

export function editUser(data) {
  return request({
    url: '/admin/user/edit',
    method: 'post',
    data
  })
}

export function deleteUser(data) {
  return request({
    url: '/admin/user/delete',
    method: 'post',
    data
  })
}
