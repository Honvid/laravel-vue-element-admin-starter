<template>
    <div class="app-container">
        <div class="filter-container">
            <el-input v-model="pagination.name" :placeholder="$t('role.name')" style="width: 200px;"
                      class="filter-item" @keyup.enter.native="filter"/>
            <el-button v-waves class="filter-item" type="primary" icon="el-icon-search" @click="filter">
                {{ $t('table.search') }}
            </el-button>
            <el-button v-permission="['roles.store']" class="filter-item" style="margin-left: 10px;" type="primary" icon="el-icon-edit"
                       @click="add">
                {{ $t('table.add') }}
            </el-button>
        </div>

        <el-table :data="list" v-loading="listLoading" style="width: 100%;" border>
            <el-table-column type="selection" width="35"></el-table-column>
            <el-table-column :label="$t('table.id')" width="60">
                <template slot-scope="scope">{{ scope.row.id }}</template>
            </el-table-column>
            <el-table-column :label="$t('role.name')">
                <template slot-scope="scope">{{ scope.row.name }}</template>
            </el-table-column>
            <el-table-column :label="$t('role.title')">
                <template slot-scope="scope">{{ scope.row.title }}</template>
            </el-table-column>
            <el-table-column align="center" :label="$t('table.actions')">
                <template slot-scope="scope">
                    <el-button v-permission="['roles.update']" type="primary" icon="el-icon-edit" size="mini" plain @click="edit(scope)">
                        {{ $t('table.edit') }}
                    </el-button>
                    <el-button v-permission="['roles.destroy']" type="danger" icon="el-icon-delete" size="mini" plain @click="deleteObject(scope)">
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
                   :title="dialogType==='edit'? $t('role.edit'):$t('role.add')">
            <el-form ref="formModel" :rules="rules" :model="formModel" :label-width="formWidth" label-position="left">
                <el-form-item :label="$t('role.name')" prop="name">
                    <el-input v-model="formModel.name" :placeholder="$t('role.name')"/>
                </el-form-item>
                <el-form-item :label="$t('role.title')" prop="title">
                    <el-input
                            v-model="formModel.title"
                            :autosize="{ minRows: 2, maxRows: 4}"
                            type="textarea"
                            :placeholder="$t('role.title')"/>
                </el-form-item>
                <el-form-item :label="$t('permission.permissions')" prop="abilities">
                    <ability-box v-bind:checked="formModel.abilities"
                                 v-on:listenAbilityChecked="checkedAbilities"></ability-box>
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
  import { getAbilities } from 'common/api/ability'
  import { getRoles, addRole, deleteRole, updateRole } from 'common/api/role'
  import Pagination from '@/components/Pagination' // secondary package based on el-pagination
  import AbilityBox from '@/components/AbilityBox'

  export default {
    components: { Pagination, AbilityBox },
    directives: { waves, permission },
    data() {
      return {
        total: 0,
        listLoading: true,
        formModel: {},
        list: [],
        abilities: [],
        formWidth: '80px',
        dialogVisible: false,
        dialogType: 'new',
        checkStrictly: false,
        pagination: {
          page: 1,
          pageSize: 20,
          name: undefined
        },
        rules: {
          name: [
            { required: true, trigger: 'change', type: 'string', message: this.$t('role.nameRequired') },
            { max:100, message: this.$t('role.nameRange') }
          ],
          title: [
            { trigger: 'change', type: 'string', max:200, message: this.$t('role.titleRange') }
          ]
        }
      }
    },
    created() {
      this.getRoles()
    },
    methods: {
      // get roles list
      async getRoles() {
        this.listLoading = true
        const res = await getRoles(this.pagination)
        this.listLoading = false
        this.list = res.data.data
        this.total = res.data.total
        this.pagination.page = res.data.current_page
        this.pagination.pageSize = parseInt(res.data.per_page)
      },
      // query
      filter() {
        this.getRoles()
      },
      // get the checked abilities
      checkedAbilities(data) {
        this.abilities = []
        for (let group in data) {
          this.abilities = this.abilities.concat(data[group])
        }
        this.abilities = Array.from(new Set(this.abilities));
      },
      handleSelectionChange(val) {
        this.multipleSelection = val;
      },
      add() {
        this.formModel = {}
        this.dialogType = 'new'
        this.$refs['formModel'] && this.$refs['formModel'].resetFields()
        this.dialogVisible = true
      },
      edit(scope) {
        this.dialogType = 'edit'
        this.$refs['formModel'] && this.$refs['formModel'].resetFields()
        this.dialogVisible = true
        this.formModel = deepClone(scope.row)
        this.abilities = []
        for (let ability of this.formModel.abilities) {
          this.abilities.push(ability.name)
        }
      },
      deleteObject({$index, row}) {
        this.$confirm(this.$t('confirm.message'), this.$t('confirm.title'), {
          confirmButtonText: this.$t('confirm.confirm'),
          cancelButtonText: this.$t('confirm.cancel'),
          type: 'warning'
        })
          .then(async () => {
            await deleteRole(row.id)
            this.list.splice($index, 1)
            this.$message({
              type: 'success',
              dangerouslyUseHTMLString: true,
              message: `
                ${this.$t('users.deleteRole')}
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
            this.dialogType === 'edit' ? await updateRole(this.formModel.id, formModel) : await addRole(this.formModel)
            this.dialogVisible = false
            this.$message({
              dangerouslyUseHTMLString: true,
              message: `
                ${this.dialogType === 'edit' ? this.$t('role.edit') : this.$t('role.add')}
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
        this.formWidth = (this.$i18n.locale === 'en' ? '130px' : '80px');
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
