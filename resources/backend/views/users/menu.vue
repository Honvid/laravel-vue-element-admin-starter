<template>
    <div class="app-container">
        <div class="filter-container">
            <el-button v-permission="['menus.store']" class="filter-item" style="margin-left: 10px;" type="primary" icon="el-icon-edit"
                       @click="add">
                {{ $t('table.add') }}
            </el-button>
        </div>

        <el-table :data="list" v-loading="listLoading" row-key="id" :tree-props="defaultProps" style="width: 100%;" border>
            <el-table-column :label="$t('menu.name')">
                <template slot-scope="scope">{{ scope.row.title }}</template>
            </el-table-column>
            <el-table-column :label="$t('menu.icon')" width="60">
                <template slot-scope="scope"><svg-icon :icon-class="scope.row.icon || ''"/></template>
            </el-table-column>
            <el-table-column :label="$t('menu.uri')">
                <template slot-scope="scope">{{ scope.row.uri }}</template>
            </el-table-column>
            <el-table-column :label="$t('menu.priority')" width="60">
                <template slot-scope="scope">{{ scope.row.priority }}</template>
            </el-table-column>
            <el-table-column :label="$t('menu.link')" width="100">
                <template slot-scope="scope">
                  <el-switch 
                    :active-value="active"
                    :inactive-value="inActive"
                    v-model="scope.row.link"
                    :disabled="scope.row.parent_id === 0"
                    @change="handelSwitchChange(scope.row)"></el-switch>
                </template>
            </el-table-column>
            <el-table-column :label="$t('menu.show')" width="100">
                <template slot-scope="scope">
                  <el-switch 
                    :active-value="active"
                    :inactive-value="inActive"
                    v-model="scope.row.show"
                    :disabled="scope.row.parent_id === 0"
                    @change="handelSwitchChange(scope.row)"></el-switch>
                </template>
            </el-table-column>
            <el-table-column align="center" :label="$t('table.actions')" width="200">
                <template slot-scope="scope" v-if="scope.row.parent_id > 0">
                    <el-button v-permission="['menus.update']" type="primary" icon="el-icon-edit" size="mini" plain @click="edit(scope)">
                        {{ $t('table.edit') }}
                    </el-button>
                    <el-button v-permission="['menus.destroy']" type="danger" icon="el-icon-delete" size="mini" plain @click="deleteObject(scope)">
                        {{ $t('table.delete') }}
                    </el-button>
                </template>
            </el-table-column>
        </el-table>

        <el-dialog :visible.sync="dialogVisible"
                   :title="dialogType==='edit'? $t('menu.edit'):$t('menu.add')">
            <el-form ref="formModel" :rules="rules" :model="formModel" :label-width="formWidth" label-position="left">
                <el-form-item :label="$t('menu.name')" prop="title">
                    <el-input v-model="formModel.title" :placeholder="$t('menu.name')"/>
                </el-form-item>
                <el-form-item :label="$t('menu.uri')" prop="uri">
                    <el-input v-model="formModel.uri" :placeholder="$t('menu.uri')"/>
                </el-form-item>
                <el-form-item :label="$t('menu.parent')" prop="parent">
                  <el-cascader 
                    v-model="formModel.parent"
                    placeholder="Please choose"
                    size="300"
                    clearable
                    :options="list"
                    :props="defaultProps">
                  </el-cascader>
                </el-form-item>
                <el-form-item :label="$t('menu.icon')" prop="icon">
                    <el-input v-model="formModel.icon" :placeholder="$t('menu.icon')"/>
                </el-form-item>
                <el-form-item :label="$t('menu.permission')" prop="permission">
                    <el-input v-model="formModel.permission" :placeholder="$t('menu.permission')"/>
                </el-form-item>
                <el-form-item :label="$t('menu.priority')" prop="priority">
                    <el-input v-model.number="formModel.priority" :placeholder="$t('menu.priority')"/>
                </el-form-item>
                <el-form-item :label="$t('menu.link')" prop="link">
                    <el-switch 
                      :active-value="active"
                      :inactive-value="inActive"
                      v-model.number="formModel.link"></el-switch>
                </el-form-item>
                <el-form-item :label="$t('menu.show')" prop="show">
                    <el-switch 
                      :active-value="active"
                      :inactive-value="inActive"
                      v-model.number="formModel.show"></el-switch>
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
  import { getMenus, getMenuLists, addMenu, deleteMenu, updateMenu } from 'common/api/menu'

  export default {
    directives: { waves, permission },
    data() {
      return {
        listLoading: true,
        formModel: {},
        list: [],
        formWidth: '80px',
        menus: [],
        dialogVisible: false,
        active: 1,
        inActive: 0,
        dialogType: 'new',
        defaultProps: {
          checkStrictly: true,
          children: 'children',
          label: 'title',
          value: "id"
        },
        rules: {
          title: [
            { required: true, trigger: 'change', type: 'string', message: this.$t('menu.titleRequired') }
          ],
          uri: [
            { required: true, trigger: 'change', type: 'string', message: this.$t('menu.uriRequired') }
          ],
          priority: [
            { trigger: 'change', type: 'number', message: this.$t('menu.priorityNumber') }
          ]
        }
      }
    },
    created() {
      this.getMenus()
    },
    methods: {
      async getMenus() {
        this.listLoading = true
        const res = await getMenus(this.pagination)
        const menus = await getMenuLists()
        this.listLoading = false
        this.list = res.data
        this.menus = menus.data
      },
      add() {
        this.dialogType = 'new'
        this.dialogVisible = true
        this.formModel = {}
        this.formModel.show = 1
        this.$refs['formModel'] && this.$refs['formModel'].resetFields()
      },
      async handelSwitchChange(row) {
        await updateMenu(row.id, row)
      },
      edit(scope) {
        this.dialogType = 'edit'
        this.dialogVisible = true
        if(scope.row.parent_id === 0){
          scope.row.parent = []
        }else{
          let path = [];
          this.getNodesParent(scope.row.parent_id, this.menus, path)
          let parent = [];
          for (let i = path.length - 1; i >= 0; i--) {
            parent.push(path[i])
          }
          scope.row.parent = parent
        }
        this.$refs['formModel'] && this.$refs['formModel'].resetFields()
        this.formModel = deepClone(scope.row)
      },
      getNodesParent(parent, nodes, path = []) {
        for (let i = 0; i < nodes.length; i++) {
          let node = nodes[i]
          if (parent === node.id) {
            path.push(node.id)
            if (node.parent_id > 0) {
              this.getNodesParent(node.parent_id, nodes, path)
            }
          } 
        }
      },
      deleteObject({$index, row}) {
        this.$confirm(this.$t('confirm.message'), this.$t('confirm.title'), {
          confirmButtonText: this.$t('confirm.confirm'),
          cancelButtonText: this.$t('confirm.cancel'),
          type: 'warning'
        })
          .then(async () => {
            await deleteMenu(row.id)
            this.$message({
              type: 'success',
              dangerouslyUseHTMLString: true,
              message: `
                ${this.$t('menu.delete')}
                <b>${row.title}</b>
                ${this.$t('confirm.deleteSuccess')}
              `,
            })
            this.getMenus()
          })
          .catch(err => {
            console.error(err)
          })
      },
      confirm() {
        this.$refs['formModel'].validate(async (valid) => {
          if (valid) {
            const parentId = this.formModel.parent[this.formModel.parent.length -1];
            this.formModel.parent_id = parentId;
            this.dialogType === 'edit' ? await updateMenu(this.formModel.id, this.formModel) : await addMenu(this.formModel)
            this.dialogVisible = false
            this.$message({
              dangerouslyUseHTMLString: true,
              message: `
                ${this.dialogType === 'edit' ? this.$t('menu.edit') : this.$t('menu.add')}
                <b>${this.formModel.title}</b>
                ${this.$t('confirm.success')}
              `,
              type: 'success'
            })
            this.getMenus()
          } else {
            return false;
          }
        }) 
      }
    },
    computed:{
      getFormWidth(){
        this.formWidth = (this.$i18n.locale === 'en' ? '120px' : '80px');
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
