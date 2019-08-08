<template>
  <div class="app-container">
    <div class="filter-container">
      <el-input v-model="pagination.name" :placeholder="$t('user.name')" style="width: 200px;"
                class="filter-item" @keyup.enter.native="filter"/>
      <el-button v-waves class="filter-item" type="primary" icon="el-icon-search" @click="filter">
          {{ $t('table.search') }}
      </el-button>
      <el-button v-permission="['users.store']" class="filter-item" style="margin-left: 10px;" type="primary" icon="el-icon-edit"
                 @click="add">
          {{ $t('table.add') }}
      </el-button>
    </div>

    <el-table :data="list" v-loading="listLoading" style="width: 100%;" row-key="id" border>
      <el-table-column :label="$t('table.id')" width="60">
        <template slot-scope="scope">{{ scope.row.id }}</template>
      </el-table-column>
      <el-table-column :label="$t('user.name')">
        <template slot-scope="scope">{{ scope.row.name }}</template>
      </el-table-column>
      <el-table-column :label="$t('user.avatar')">
        <template slot-scope="scope">
          <el-avatar shape="square" :size="50" :src="scope.row.avatar" @error="!scope.row.avatar">
            <img src="@/assets/empty.png"/>
          </el-avatar>
        </template>
      </el-table-column>
      <el-table-column :label="$t('user.mobile')">
        <template slot-scope="scope">{{ scope.row.mobile }}</template>
      </el-table-column>
      <el-table-column :label="$t('user.email')">
        <template slot-scope="scope">{{ scope.row.email }}</template>
      </el-table-column>
      <el-table-column align="center" :label="$t('table.actions')">
        <template slot-scope="scope">
          <el-button v-permission="['users.update']" type="primary" size="mini" plain @click="edit(scope)">
              {{ $t('table.edit') }}
          </el-button>
          <el-button v-permission="['users.destroy']" type="danger" size="mini" plain @click="deleteObject(scope)">
              {{ $t('table.delete') }}
          </el-button>
        </template>
      </el-table-column>
    </el-table>

    <div style="margin-top: 20px">
      <el-button>{{ $t('table.deleteAll') }}</el-button>
      <pagination :total="total" :page.sync="pagination.page" :limit.sync="pagination.pageSize" @pagination="filter" />
    </div>

    <el-dialog :visible.sync="dialogVisible"
               :title="dialogType==='edit'? $t('user.edit'):$t('user.add')">
      <el-form ref="formModel" :rules="rules" :model="formModel" :label-width="formWidth" label-position="left">
        <!-- 阻止浏览器的自动填充 -->
        <input type="email" name="_prevent_auto_complete_name" 
          autocomplete="off" readonly="readonly" style="display: none !important;"/>
        <input type="password" name="_prevent_auto_complete_pass" 
          autocomplete="new-password" readonly="readonly" style="display: none !important;" />
        <el-form-item :label="$t('user.name')" prop="name">
          <el-input v-model="formModel.name" :placeholder="$t('user.name')"/>
        </el-form-item>
        <el-form-item :label="$t('user.email')" prop="email">
          <el-input type="email" v-model="formModel.email" auto-complete="off" :placeholder="$t('user.email')"/>
          <input type="email" style="display:none;" />
        </el-form-item>
        <el-form-item :label="$t('user.avatar')" prop="avatar">
          <avatar-cropper :avatar="formModel.avatar" v-on:changeAvater="changeAvater" />
        </el-form-item>
        <el-form-item :label="$t('user.mobile')" prop="mobile">
          <el-input v-model="formModel.mobile" :placeholder="$t('user.mobile')"/>
        </el-form-item>
        <el-form-item :label="$t('user.password')" prop="password">
          <el-input type="password" v-model="formModel.password" auto-complete="new-password" :placeholder="$t('user.password')"/>
        </el-form-item>
        <el-form-item :label="$t('user.assign')" prop="roles">
          <el-select v-model="formModel.roles" multiple>
            <el-option
              v-for="role in roles"
              :key="role.id"
              :label="role.name"
              :value="role.name">
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item v-if="formModel.provider" :label="$t('user.provider')" prop="provider">
          <el-tag :key="formModel.provider" type="success">{{ formModel.provider }} </el-tag>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogVisible=false">
          {{ $t('table.cancel') }}
        </el-button>
        <el-button type="primary" @click="confirm">
          {{ $t('table.confirm') }}
        </el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
  import path from 'path'
  import permission from '@/directive/permission' // 权限判断指令
  import waves from '@/directive/waves' // waves directive
  import { deepClone } from 'common/utils'
  import { validUsername, validEmail, validPwd } from 'common/utils/validate'
  import { getUsers, addUser, updateUser, deleteUser } from 'common/api/user'
  import { getRoles } from 'common/api/role'
  import AvatarCropper from '@/components/AvatarCropper' // secondary package based on el-pagination
  import Pagination from '@/components/Pagination' // secondary package based on el-pagination

  export default {
    components: { Pagination, AvatarCropper },
    directives: { waves, permission },
    data() {
      let checkPhone = (rule, value, callback) => {
        if (value) {
          const reg = /^1[3|4|5|7|8][0-9]\d{8}$/
          if (reg.test(value)) {
            callback()
          } else {
            return callback(new Error(this.$t('user.mobileValidate')))
          }
        }
        callback()
      }
      return {
        total: 0,
        listLoading: true,
        formWidth: '80px',
        formModel: {},
        list: [],
        dialogVisible: false,
        avatarDialogVisible: false,
        newAvater: '',
        roles: [],
        dialogType: 'new',
        pagination: {
          page: 1,
          pageSize: 20,
          name: undefined
        },
        rules: {
          name: [
            { required: true, trigger: 'change', type: 'string', message: this.$t('user.nameRequired') },
          ],
          mobile: [
            { trigger: 'change', validator: checkPhone }
          ],
          email: [
            { required: true, trigger: 'change', type: 'email', message: this.$t('user.emailValidate') },
          ],
          password: [
            { trigger: 'change', validator: (rule, value, callback) => {
              if ((value === undefined || value.length === 0) && this.dialogType === 'new') {
                callback(new Error(this.$t('user.passwordRequired')))
              } else {
                if (value && value.length < 6) {
                  callback(new Error(this.$t('user.passwordRange')))
                } else {
                  callback()
                }
              }
            } }
          ]
        }
      }
    },
    created() {
      this.getUsers()
    },
    methods: {
      async getUsers() {
        this.listLoading = true
        const res = await getUsers(this.pagination)
        this.listLoading = false
        this.list = res.data.data
        this.total = res.data.total
        this.pagination.page = res.data.current_page
        this.pagination.pageSize = parseInt(res.data.per_page)
        const roles = await getRoles({pageSize: 10000})
        this.roles = roles.data.data;
      },
      filter() {
        this.getUsers()
      },
      add() {
        this.formModel = {}
        this.dialogType = 'new'
        this.dialogVisible = true
        this.$refs['formModel'] && this.$refs['formModel'].resetFields()
      },
      changeAvater(avatar){
        this.formModel.avatar = avatar
      },
      edit(scope) {
        this.dialogType = 'edit'
        this.dialogVisible = true
        this.$refs['formModel'] && this.$refs['formModel'].resetFields()
        this.formModel = deepClone(scope.row)
      },
      deleteObject({$index, row}) {
        this.$confirm(this.$t('confirm.message'), this.$t('confirm.title'), {
          confirmButtonText: this.$t('confirm.confirm'),
          cancelButtonText: this.$t('confirm.cancel'),
          type: 'warning'
        })
          .then(async () => {
            await deleteUser(row.id)
            this.list.splice($index, 1)
            this.$message({
              type: 'success',
              dangerouslyUseHTMLString: true,
              message: `
                ${this.$t('user.delete')}
                <b>${row.name}</b>
                ${this.$t('confirm.deleteSuccess')}
              `,
            })
          })
          .catch(err => {
            console.error(err)
          })
      },
      async confirm() {
        this.$refs['formModel'].validate(async (valid) => {
          if (valid) {
            this.dialogType === 'edit' ? await updateUser(this.formModel.id, this.formModel) : await addUser(this.formModel)
            this.dialogVisible = false
            this.$message({
              dangerouslyUseHTMLString: true,
              message: `
                ${this.dialogType === 'edit' ? this.$t('user.edit') : this.$t('user.add')}
                <b>${this.formModel.name}</b>
                ${this.$t('confirm.success')}
              `,
              type: 'success'
            })
            this.filter()
          } else {
            return false;
          }
        })
      }
    },
    computed:{
      getFormWidth(){
        this.formWidth = (this.$i18n.locale === 'en' ? '140px' : '80px');
      }
    },
    watch: {
      getFormWidth(){}
    }
  }
</script>

<style lang="scss">

    $bg: #283443;
    $light_gray: #fff;
    $cursor: #fff;

    /* reset element-ui css */
    .app-container {
        .el-dialog__header {
            line-height: normal;
        }
        .el-dialog__body {
            padding: 10px;
            border-top: 1px solid #eee;
            .el-form {
                width: 80%;
                margin: 10px auto 0 auto;
                .ability-table {
                    .el-table__header-wrapper {
                        line-height: 20px;
                    }
                }
            }
        }
        .el-dialog__footer {
            text-align: right;
            border-top: 1px solid #eee;
        }
    }
</style>

<style lang="scss" scoped>
    .app-container {
        .pagination-container {
            padding: 0;
            margin-top: 4px;
            float: right;
        }
    }
</style>
