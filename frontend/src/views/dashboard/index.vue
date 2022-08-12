<template>
  <div class="dashboard-container">
    <div class="dashboard-header">vue + elementui + thinkphp5.1 + Mysql</div>
    <div class="dashboard-text">当前用户: {{ name }}</div>

    <div class="dashboard-table-title">耗时明细</div>
    <div class="dashboard-table-content">
      <el-table
        :data="tableData"
        >
      style="width: 100%">
        <el-table-column
          prop="module"
          label="模块"
          width="280">
        </el-table-column>
        <el-table-column
          prop="time"
          label="耗时"
          width="280">
        </el-table-column>
      </el-table>
    </div>

  </div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  data(){
    return {
      tableData: [
        {
          module: '用户管理api',
          time: '0.5h'
        },
        {
          module: '用户组管理api',
          time: '0.5h'
        },
        {
          module: '权限管理',
          time: '0.5h'
        },
        {
          module: '数据库及其他',
          time: '1h'
        },
        {
          module: '前端',
          time: '4h'
        },
        {
          module: '总计',
          time: '6.5h'
        },
      ]
    }
  },
  name: 'Dashboard',
  computed: {
    ...mapGetters([
      'name',
    ])
  },
}
</script>

<style lang="scss" scoped>
.dashboard {
  &-container {
    margin: 30px;
  }
  &-text {
    font-size: 30px;
    line-height: 46px;
  }
  &-header {
    font-size: 36px;
    line-height: 46px;
    margin-bottom: 20px;
    font-weight: 700;
  }
  &-table-title{
    font-size: 32px;
    line-height: 46px;
    margin: 10px 0;
    font-weight: 700;
  }
}

</style>
