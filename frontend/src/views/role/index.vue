<template>
    <div class="app-container">
      <div class="search-box">
        <el-button type="primary" @click="showDialog()">添加角色</el-button>
      </div>
      <el-table
        v-loading="listLoading"
        :data="list"
        element-loading-text="Loading"
        border
        fit
        highlight-current-row
      >
        <el-table-column align="center" label="ID" width="60">
          <template slot-scope="scope">
            {{ scope.row.id }}
          </template>
        </el-table-column>

        <el-table-column label="角色名">
          <template slot-scope="scope">
            {{ scope.row.name }}
          </template>
        </el-table-column>

        <el-table-column class-name="status-col" label="角色描述"  align="center">
          <template slot-scope="scope">
            {{ scope.row.desc }}
          </template>
        </el-table-column>
        <el-table-column class-name="status-col" label="是否超管角色"  align="center">
          <template slot-scope="scope">
            {{ scope.row.is_super ? '是' : '否' }}
          </template>
        </el-table-column>

        <el-table-column label="创建时间">
          <template slot-scope="scope">
            {{ scope.row.create_time }}
          </template>
        </el-table-column>
        <el-table-column class-name="status-col" label="操作"  align="center">
          <template slot-scope="scope" v-if="!scope.row.is_super">
            <el-link type="primary" @click="showDialog(scope.row)">编辑</el-link>
            <el-divider direction="vertical"></el-divider>
          <el-link type="primary" @click="deleteConfirm(scope.row.id)">删除</el-link>
          </template>
        </el-table-column>

      </el-table>

      <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getList" />

        <el-dialog title="新增/编辑分组" :visible.sync="dialogFormVisible">
          <el-form label-position="left" :model="ruleForm" :rules="rules" ref="ruleForm" label-width="120px" class="demo-ruleForm">
            <el-form-item label="分组名" prop="name">
              <el-input v-model="ruleForm.name" ></el-input>
            </el-form-item>
            <el-form-item label="分组介绍" prop="desc">
              <el-input  v-model="ruleForm.desc" ></el-input>
            </el-form-item>
            <el-form-item label="接口权限" prop="desc">
              <el-checkbox-group v-model="ruleForm.api_ids">
                <div v-for="item in apis">
                    <el-divider content-position="left">{{ item.group }}</el-divider>
                    <el-checkbox v-for="apiItem in item.api" :label="apiItem.id" :key="apiItem.id">{{apiItem.name}}</el-checkbox>
                </div>
              </el-checkbox-group>

            </el-form-item>


          </el-form>
          <div slot="footer" class="dialog-footer">
            <el-button @click="dialogFormVisible = false">取 消</el-button>
            <el-button type="primary" @click="submitForm('ruleForm')">确 定</el-button>
          </div>
        </el-dialog>
      </div>
  </template>

  <script>
  import { getGroupList,addGroup,editGroup,deleteGroup } from '@/api/group'
  import { getAll } from '@/api/api'
  import Pagination from '@/components/Pagination' // Secondary package based on el-pagination
  export default {
    components: { Pagination },
    filters: {

    },
    data() {
      return {
        list: null,
        total: 0,
        listLoading: true,
        listQuery: {
          page: 1,
          limit: 10
        },
        dialogFormVisible:false,
        ruleForm:{
            id:undefined,
            name:"",
            desc:"",
            api_ids:[],
        },
        rules: {

        },
        apis:[]

      }
    },
    created() {
      this.getList()
      this.loadApis()
    },
    methods: {
      loadApis() {
        this.listLoading = true
        getAll().then(response => {
          this.apis = response.data.list
          console.log(this.apis);
        })
      },
      getList() {
        this.listLoading = true
        getGroupList(this.listQuery).then(response => {
          this.list = response.data.list
          this.total = response.data.total
          this.listLoading = false
        }).catch(err=>{
          console.log(err)
          this.listLoading = false
        })
      },
      showDialog(row){
        if(row){
          this.ruleForm.id = row.id
          this.ruleForm.name = row.name
          this.ruleForm.desc = row.desc
          this.ruleForm.api_ids = row.api_ids
        }else{
          this.ruleForm.id = undefined
          this.ruleForm.name = ""
          this.ruleForm.desc = ""
          this.ruleForm.api_ids = []

        }
        this.dialogFormVisible = true;

      },
      submitForm(formName) {
          this.$refs[formName].validate((valid) => {
            if (valid) {
              let api = addGroup
              if(this.ruleForm.id){
                api = editGroup
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
          deleteGroup({id}).then(response => {
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
      }
    }
  }
  </script>

  <style scoped>
  .search-box{
    padding-bottom: 10px;
  }
  </style>
