<template>
  <div class="app-container">
    <div class="search-box">
      <el-button type="primary" @click="showDialog()">添加用户</el-button>
    </div>
    <el-table
      v-loading="listLoading"
      :data="list"
      @sort-change="sortChange"
      element-loading-text="Loading"
      border
      fit
      highlight-current-row
    >
      <el-table-column align="center" label="ID" width="60"  prop="id" sortable="custom">
        <template slot-scope="scope">
          {{ scope.row.id }}
        </template>
      </el-table-column>
      <el-table-column label="用户名">
        <template slot-scope="scope">
          {{ scope.row.username }}
        </template>
      </el-table-column>

      <el-table-column class-name="status-col" label="角色"  align="center">
        <template slot-scope="scope">
          <el-tag>{{ scope.row.groups | roleFilter }}</el-tag>
        </template>
      </el-table-column>

      <el-table-column label="登录时间" prop="logintime" sortable="custom">
        <template slot-scope="scope">
          {{ scope.row.logintimedesc }}
        </template>
      </el-table-column>
      <el-table-column label="登录IP">
        <template slot-scope="scope">
          {{ scope.row.loginip }}
        </template>
      </el-table-column>
      <el-table-column label="创建时间">
        <template slot-scope="scope">
          {{ scope.row.create_time }}
        </template>
      </el-table-column>
      <el-table-column class-name="status-col" label="操作"  align="center">
        <template slot-scope="scope" v-if="!scope.row.is_super_admin">
          <el-link type="primary" @click="showDialog(scope.row, 'detail')">详情</el-link>
          <el-divider direction="vertical"></el-divider>
          <el-link type="primary" @click="showDialog(scope.row)">编辑</el-link>
          <el-divider direction="vertical"></el-divider>
          <el-link type="primary" @click="deleteConfirm(scope.row.id)">删除</el-link>
        </template>
      </el-table-column>

    </el-table>

    <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getList" />

      <el-dialog title="新增/编辑用户" :visible.sync="dialogFormVisible">
        <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="100px" class="demo-ruleForm">
          <el-form-item label="用户名" prop="username">
            <el-input v-model="ruleForm.username" :disabled="disableUsername" ></el-input>
          </el-form-item>
          <el-form-item label="密码" prop="password">
            <el-input type="password" v-model="ruleForm.password"  placeholder="新增必填，编辑时可不填" ></el-input>
          </el-form-item>
          <el-form-item label="分组" prop="group_id">
            <el-select v-model="ruleForm.group_id" placeholder="请选择分组">
              <el-option v-for="role in roles" :key="role.id" :label="role.name" :value="role.id"></el-option>

            </el-select>
          </el-form-item>

        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogFormVisible = false">取 消</el-button>
          <el-button type="primary" @click="submitForm('ruleForm')">确 定</el-button>
        </div>
      </el-dialog>

    <el-dialog title="用户详情" :visible.sync="dialogDetailVisible">
      <el-form label-width="100px" class="demo-ruleForm">
        <el-form-item label="用户名">
          <div class="detail-item-content">{{detailInfo.username}}</div>
        </el-form-item>

        <el-form-item label="分组">
          <div class="detail-item-content">{{detailInfo.group_name}}</div>

        </el-form-item>

        <el-form-item label="登录时间">
          <div class="detail-item-content">{{detailInfo.logintimedesc}}</div>

        </el-form-item>

        <el-form-item label="登录ip">
          <div class="detail-item-content">{{detailInfo.loginip}}</div>

        </el-form-item>

        <el-form-item label="创建时间">
          <div class="detail-item-content">{{detailInfo.create_time}}</div>

        </el-form-item>

      </el-form>
    </el-dialog>
    </div>
</template>

<script>
import { userList,addUser,editUser,deleteUser } from '@/api/user'
import { getAllGroup } from '@/api/group'
import Pagination from '@/components/Pagination' // Secondary package based on el-pagination
export default {
  components: { Pagination },
  filters: {
    statusFilter(status) {
      if(status==1){
        return '正常'
      }else{
        return '禁用'
      }
    },
    roleFilter(groups) {
      return groups ? groups.name : "无"
    }
  },
  data() {
    return {
      list: null,
      total: 0,
      listLoading: true,
      listQuery: {
        page: 1,
        limit: 10,
        sort: ''
      },
      dialogFormVisible:false,
      dialogDetailVisible: false,
      ruleForm:{
          id:undefined,
          username:"",
          password:"",
          group_id:undefined,
      },
      detailInfo: {

      },
      rules: {

      },
      disableUsername:false,
      roles:[],
      uploadApi: `${process.env.VUE_APP_BASE_API}/admin/upload`,
    }
  },
  created() {
    this.getList()
    this.loadRoles()
  },
  methods: {
    sortChange(column) {
      if(column.prop === 'id'){
        this.listQuery.sort = column.order === 'ascending'?'asc':'desc';
        this.getList()
      }else{
        if(column.order === 'ascending'){
          this.list = this.list.sort(function (x, y) {
            return x[column.prop] - y[column.prop];
          });
        }else{
          this.list = this.list.sort(function (x, y) {
            return y[column.prop] - x[column.prop];
          });
        }
      }
    },
    loadRoles(){
      getAllGroup().then(response => {
        this.roles = response.data.list
      })
    },
    getList() {
      this.listLoading = true
      userList(this.listQuery).then(response => {
        this.list = response.data.list
        this.total = response.data.total
        this.listLoading = false
      }).catch(err=>{
          console.log(err)
          this.listLoading = false
        })
    },
    showDialog(row, type='form'){
      if(row){
        this.ruleForm.id = row.id
        this.ruleForm.username = row.username
        this.ruleForm.password = ""
        this.ruleForm.group_id = row.group_id
        console.log(row)
        this.disableUsername = true
      }else{
        this.ruleForm.id = undefined
        this.ruleForm.username = ""
        this.ruleForm.password = ""
        this.ruleForm.group_id = undefined
        this.disableUsername = false
      }
      if(type=='detail'){
        this.detailInfo = row
        this.detailInfo.group_name = row.groups.name
        this.dialogDetailVisible = true;
      }else{
        this.dialogFormVisible = true;
      }

    },
    submitForm(formName) {
        this.$refs[formName].validate((valid) => {
          if (valid) {
            let api = addUser
            if(this.ruleForm.id){
              api = editUser
            }
            api(this.ruleForm).then(response => {
              if(response.code==200){
                this.dialogFormVisible = false;
                this.getList()
              }
            })
          } else {
            console.log('error submit!!');
            return false;
          }
        });
      },
      deleteConfirm(id){
        this.$confirm('真的要删除这条数据吗?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          deleteUser({id}).then(response => {
              if(response.code==200){
                this.$message({
                  type: 'success',
                  message: '删除成功!'
                });
                this.getList()
              }
            })
        }).catch(() => {

        });
      },
  }
}
</script>

<style lang="scss" scoped>
.search-box{
  padding-bottom: 10px;
}
.detail-item-content{
  font-weight: 700;
  margin-left: 20px;
}
</style>
