import request from '@/utils/request'

export function getAll() {
  return request({
    url: '/admin/api/all',
    method: 'get',
    params: {}
  })
}
