<template>
    <div class="app-container">
        <div class="filter-container">
            <el-input v-model="pagination.name" :placeholder="$t('permission.name')" style="width: 200px;"
                      class="filter-item" @keyup.enter.native="filter"/>
            <el-button v-waves class="filter-item" type="primary" icon="el-icon-search" @click="filter">
                {{ $t('table.search') }}
            </el-button>
            <el-button v-permission="['permissions.store']" class="filter-item" style="margin-left: 10px;" type="primary" icon="el-icon-edit"
                       @click="add">
                {{ $t('table.add') }}
            </el-button>
        </div>

        <el-table :data="list" v-loading="listLoading" style="width: 100%;" row-key="id" border>
            <el-table-column :label="$t('table.id')" width="60">
                <template slot-scope="scope">{{ scope.row.id }}</template>
            </el-table-column>
            <el-table-column :label="$t('permission.name')">
                <template slot-scope="scope">{{ scope.row.name }}</template>
            </el-table-column>
            <el-table-column :label="$t('permission.title')">
                <template slot-scope="scope">{{ scope.row.title }}</template>
            </el-table-column>
            <el-table-column :label="$t('permission.group')">
                <template slot-scope="scope">{{ scope.row.group }}</template>
            </el-table-column>
            <el-table-column align="center" :label="$t('table.actions')">
                <template slot-scope="scope">
                    <el-button v-permission="['permissions.update']" type="primary" icon="el-icon-edit" size="mini" plain @click="edit(scope)">
                        {{ $t('table.edit') }}
                    </el-button>
                    <el-button v-permission="['permissions.destroy']" type="danger" icon="el-icon-delete" size="mini" plain @click="deleteObject(scope)">
                        {{ $t('table.delete') }}
                    </el-button>
                </template>
            </el-table-column>
        </el-table>

        <div style="margin-top: 20px">
            <pagination :total="total" :page.sync="pagination.page" :limit.sync="pagination.pageSize" @pagination="filter" />
        </div>

        <el-dialog :visible.sync="dialogVisible"
                   :title="dialogType==='edit'? $t('permission.edit'):$t('permission.add')">
            <el-form ref="formModel" :rules="rules" :model="formModel" :label-width="formWidth" label-position="left">
                <el-form-item :label="$t('permission.name')" prop="name">
                    <el-input v-model="formModel.name" :placeholder="$t('permission.name')"/>
                </el-form-item>
                <el-form-item :label="$t('permission.group')" prop="group">
                  <el-select
                    v-model="formModel.group"
                    filterable
                    allow-create
                    default-first-option
                    :placeholder="$t('permission.group')">
                    <el-option
                      v-for="item in groups"
                      :key="item"
                      :label="item"
                      :value="item">
                    </el-option>
                  </el-select>
                </el-form-item>
                <el-form-item :label="$t('permission.title')" prop="title">
                    <el-input
                            v-model="formModel.title"
                            :autosize="{ minRows: 2, maxRows: 4}"
                            type="textarea"
                            :placeholder="$t('permission.title')"
                    />
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
  import { getAbilities, addAbility, updateAbility, getAbilityGroups, deleteAbility } from 'common/api/ability'
  import Pagination from '@/components/Pagination' // secondary package based on el-pagination

  export default {
    components: { Pagination },
    directives: { waves, permission },
    data() {
      return {
        total: 0,
        listLoading: true,
        formModel: {},
        list: [],
        formWidth: "80px",
        dialogVisible: false,
        groups: [],
        dialogType: 'new',
        pagination: {
          page: 1,
          pageSize: 20,
          name: undefined
        },
        rules: {
          name: [
            { required: true, trigger: 'change', type: 'string', message: this.$t('permission.nameRequired') },
            { max:100, message: this.$t('permission.nameRange') }
          ],
          title: [
            { trigger: 'change', type: 'string', max:200, message: this.$t('permission.titleRange') }
          ],
          group: [
            { required: true, trigger: 'change', type: 'string', message: this.$t('permission.groupRequired') },
            { max:20, message: this.$t('permission.groupRange') }
          ]
        }
      }
    },
    created() {
      this.getAbilities()
    },
    methods: {
      async getAbilities() {
        this.listLoading = true
        const res = await getAbilities(this.pagination)
        const group = await getAbilityGroups()
        this.listLoading = false
        this.list = res.data.data
        this.total = res.data.total
        this.pagination.page = res.data.current_page
        this.pagination.pageSize = parseInt(res.data.per_page)
        this.groups = group.data
      },
      filter() {
        this.getAbilities()
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
      },
      deleteObject({$index, row}) {
        this.$confirm(this.$t('confirm.message'), this.$t('confirm.title'), {
          confirmButtonText: this.$t('confirm.confirm'),
          cancelButtonText: this.$t('confirm.cancel'),
          type: 'warning'
        })
          .then(async () => {
            await deleteAbility(row.id)
            this.list.splice($index, 1)
            this.$message({
              type: 'success',
              dangerouslyUseHTMLString: true,
              message: `
                ${this.$t('permission.delete')}
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
            this.dialogType === 'edit' ? await updateAbility(this.formModel.id, this.formModel) : await addAbility(this.formModel)
            this.dialogVisible = false
            this.$message({
              dangerouslyUseHTMLString: true,
              message: `
                ${this.dialogType === 'edit' ? this.$t('permission.edit') : this.$t('permission.add')}
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
        this.formWidth = (this.$i18n.locale === 'en' ? '180px' : '80px');
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
