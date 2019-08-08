<template>
    <div class="ability-box">
        <el-checkbox :indeterminate="isIndeterminate['all']" v-model="checkAll['all']"
                     @change="handleCheckAll">{{ $t('checkbox.all') }}
        </el-checkbox>
        <el-table :data="abilities" class="ability-table" style="width: 100%;" border>
            <el-table-column :label="$t('permission.group')">
                <template slot-scope="scope">
                    {{ scope.row.group }} <br>
                    <el-checkbox :indeterminate="isIndeterminate[scope.row.group]"
                                 v-model="checkAll[scope.row.group]"
                                 @change="handleCheckAllChange(scope.row.group,scope.$index, $event)">
                        {{ $t('checkbox.all') }}
                    </el-checkbox>
                </template>
            </el-table-column>
            <el-table-column :label="$t('permission.permissions')">
                <template slot-scope="scope">
                    <el-checkbox-group v-model="checkedAbilities[scope.row.group]"
                                       @change="handleCheckedChange(scope.row.group, scope.$index)">
                        <el-checkbox v-for="permission in scope.row.permissions" :label="permission.name"
                                     :key="permission.name">{{permission.title}}
                        </el-checkbox>
                    </el-checkbox-group>
                </template>
            </el-table-column>
        </el-table>
    </div>
</template>
<script>
  import path from 'path'
  import { getAbilities } from 'common/api/ability'

  export default {
    name: 'AbilityBox',
    props: {
      checked: Array,
      default: []
    },
    data() {
      return {
        abilities: [],
        checkAll: {},
        checkedAbilities: {},
        isIndeterminate: {},
        loading: true
      }
    },
    created() {
      this.getAbilities()
    },
    methods: {
      // init the abilities data
      async getAbilities() {
        const res = await getAbilities({pageSize: 10000})
        this.initAbilities(res.data.data)
      },
      // switch to checked all of the permissions
      handleCheckAll(val) {
        this.abilities.forEach(item => {
          let items = [];
          if (val) {
            item.permissions.forEach(i => {
              items.push(i.name)
            })
          }
          this.$set(this.checkAll, item.group, val)
          this.$set(this.isIndeterminate, item.group, false)
          this.$set(this.checkedAbilities, item.group, items);
          this.send()
        })
        this.$set(this.isIndeterminate, 'all', false);
      },
      // switch to checked all of the permission group
      handleCheckAllChange(group, index, val) {
        let items = [];
        if (val) {
          this.abilities[index].permissions.forEach(item => {
            items.push(item.name);
          })
        }
        let size = 0;
        this.abilities.forEach(item => {
          this.checkAll[item.group] === true ? size++ : size
        })
        this.$set(this.checkAll, 'all', size === this.abilities.length)
        this.$set(this.isIndeterminate, 'all', size > 0 && size < this.abilities.length)
        this.$set(this.checkedAbilities, group, items)
        this.$set(this.isIndeterminate, group, false)
        this.send()
      },
      // when checkbox switch the status of checking
      handleCheckedChange(group, index) {
        let checkedCount = this.checkedAbilities[group].length;
        this.$set(this.checkAll, group, checkedCount === this.abilities[index].permissions.length)
        let size = 0;
        this.abilities.forEach(item => {
          this.checkAll[item.group] === true ? size++ : size
        })
        this.$set(this.checkAll, 'all', size === this.abilities.length)
        this.$set(this.isIndeterminate, 'all', size > 0 && size < this.abilities.length)
        this.$set(this.isIndeterminate, group, checkedCount > 0 && checkedCount < this.abilities[index].permissions.length)
        this.send()
      },
      send() {
        this.$emit('listenAbilityChecked', this.checkedAbilities)
      },
      initAbilities(abilities) {
        let data = {};
        abilities.forEach(ability => {
          if (data.hasOwnProperty(ability.group)) {
            data[ability.group].push(ability)
          } else {
            data[ability.group] = [ability]
          }
          this.checkedAbilities[ability.group] = [];
        })
        // show all abilities of this role.
        if(this.checked){
          this.checked.forEach(item => {
            if (this.checkedAbilities.hasOwnProperty(item.group)) {
              this.checkedAbilities[item.group].push(item.name)
            } else {
              this.checkedAbilities[item.group] = [item.name]
            }
            this.$set(this.checkedAbilities, item.group, this.checkedAbilities[item.group])
          })
        }
        this.abilities = [];
        let all = 0;
        let size = 0;
        for (let group in data) {
          this.abilities.push({group: group, permissions: data[group]})
          this.$set(this.isIndeterminate, group, false)
          this.$set(this.checkAll, group, false)
          if (this.checkedAbilities.hasOwnProperty(group)) {
            if (this.checkedAbilities[group].length === data[group].length) {
              this.$set(this.isIndeterminate, group, false)
              this.$set(this.checkAll, group, true)
              all++
            } else if (this.checkedAbilities[group].length > 0) {
              this.$set(this.isIndeterminate, group, true)
              this.$set(this.checkAll, group, false)
              size++
            }
          }
        }
        this.$set(this.checkAll, 'all', all === this.abilities.length)
        this.$set(this.isIndeterminate, 'all', size > 0 && all < this.abilities.length)
      }
    }
  }
</script>