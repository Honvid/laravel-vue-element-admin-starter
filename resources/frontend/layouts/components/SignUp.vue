<template>
    <el-dialog :title="$t('register.title')" customClass="register-container" width="520px" :visible="showDialog"
               @close="closeDialog">
        <el-form ref="registerForm" :model="registerForm" :rules="registerRules" class="register-form" auto-complete="off">
            <el-form-item prop="username">
                <span class="svg-container">
                  <svg-icon icon-class="user"/>
                </span>
                <el-input
                        ref="username"
                        v-model="registerForm.username"
                        :placeholder="$t('register.username')"
                        name="username"
                        type="text"
                        tabindex="1"
                        auto-complete="off"
                />
            </el-form-item>
            <el-form-item prop="email">
                <span class="svg-container">
                  <svg-icon icon-class="email"/>
                </span>
                <el-input
                        ref="email"
                        v-model="registerForm.email"
                        :placeholder="$t('register.email')"
                        name="email"
                        type="mail"
                        tabindex="2"
                        auto-complete="off"
                />
            </el-form-item>
            <el-tooltip v-model="capsTooltip" style="margin-bottom: 0" content="Caps lock is On" placement="right"
                        manual>
                <el-form-item prop="password">
                  <span class="svg-container">
                    <svg-icon icon-class="password"/>
                  </span>
                    <el-input
                            :key="passwordType"
                            ref="password"
                            v-model="registerForm.password"
                            :type="passwordType"
                            :placeholder="$t('register.password')"
                            name="password"
                            tabindex="3"
                            auto-complete="off"
                            @keyup.native="checkCapslock"
                            @blur="capsTooltip = false"
                    />
                    <span class="show-pwd" @click="showPwd">
                        <svg-icon :icon-class="passwordType === 'password' ? 'eye' : 'eye-open'"/>
                      </span>
                </el-form-item>
            </el-tooltip>
            <div class='password-level'>
                <span class="small" :style="passwordLevel.small">弱</span>
                <span class="middle" :style="passwordLevel.middle">中</span>
                <span class="larger" :style="passwordLevel.larger">强</span>
            </div>
            <el-tooltip v-model="capsTooltip" content="Caps lock is On" placement="right" manual>
                <el-form-item prop="confirm">
                  <span class="svg-container">
                    <svg-icon icon-class="password"/>
                  </span>
                    <el-input
                            :key="confirmType"
                            ref="confirm"
                            v-model="registerForm.confirm"
                            type="password"
                            :placeholder="$t('register.confirm')"
                            name="confirm"
                            tabindex="4"
                            auto-complete="off"
                            @keyup.native="checkCapslock"
                            @blur="capsTooltip = false"
                    />
                    <span class="show-pwd" @click="showConfirm">
                        <svg-icon :icon-class="confirmType === 'confirm' ? 'eye' : 'eye-open'"/>
                      </span>
                </el-form-item>
            </el-tooltip>
            <el-button type="primary" class="register-btn" @click="handleSignUp">{{ $t('register.title') }}</el-button>
        </el-form>
        <div slot="footer" class="dialog-footer">
            <el-row>
                <el-button circle>
                    <svg-icon icon-class="wechat" class="icon"/>
                </el-button>
                <el-button circle>
                    <svg-icon icon-class="qq" class="icon"/>
                </el-button>
                <!--<el-button circle><svg-icon icon-class="github" class="icon" /></el-button>-->
                <!--<el-button circle><svg-icon icon-class="facebook" class="icon" /></el-button>-->
                <!--<el-button circle><svg-icon icon-class="weibo" class="icon" /></el-button>-->
            </el-row>
        </div>
    </el-dialog>
</template>

<script>

  import {validUsername, validEmail, validPwd, validPwdStrong} from 'common/utils/validate'
  import i18n from 'common/lang'

  export default {
    name: 'SignUp',
    data() {
      return {
        passwordLevel: {
          small: 'background-color:#eee',
          middle: 'background-color:#eee',
          larger: 'background-color:#eee'
        },
        registerForm: {
          username: '',
          email: '',
          password: '',
          confirm: '',
        },
        registerRules: {
          username: [{
            required: true, trigger: 'blur', validator: (rule, value, callback) => {
              if (value.length === 0) {
                callback(new Error(i18n.t('validate.username')))
              } else {
                if (!validUsername(value)) {
                  callback(new Error(i18n.t('validate.username_error')))
                } else {
                  callback()
                }
              }
            },

          }],
          email: [{
            required: true, trigger: 'blur', validator: (rule, value, callback) => {
              if (value.length === 0) {
                callback(new Error(i18n.t('validate.email')))
              } else {
                if (!validEmail(value)) {
                  callback(new Error(i18n.t('validate.email')))
                } else {
                  callback()
                }
              }
            }
          }],
          password: [{
            required: true, trigger: 'blur', validator: (rule, value, callback) => {
              if (value.length === 0) {
                callback(new Error(i18n.t('validate.password')))
              } else {
                if(!validPwd(value)){
                  callback(new Error(i18n.t('validate.password_error')))
                }else {
                  callback()
                }
              }
            }
          }],
          confirm: [{
            required: true, trigger: 'blur', validator: (rule, value, callback) => {
              if (value.length === 0) {
                callback(new Error(i18n.t('validate.confirm')))
              } else {
                if(value !== this.registerForm.password) {
                  callback(new Error(i18n.t('validate.confirm_error')))
                }else {
                  callback()
                }
              }
            }
          }]
        },
        passwordType: 'password',
        confirmType: 'confirm',
        capsTooltip: false,
        loading: false
      }
    },
    computed: {
      showDialog(){
          return this.$store.getters.show_sign_up_dialog
      },
      password() {
        return this.registerForm.password
      }
    },
    watch: {
      password(newValue, oldValue){
        if(newValue.length === 0){
          this.passwordLevel.small = 'background-color: #eee'
          this.passwordLevel.middle = 'background-color: #eee'
          this.passwordLevel.larger = 'background-color: #eee'
        }else {
          let level = validPwdStrong(newValue)
          this.passwordLevel.small = level > 1 || level === 1 ? 'background-color: red' : 'background-color: #eee'
          this.passwordLevel.middle = level > 2 || level === 2 ? 'background-color: orange' : 'background-color: #eee'
          this.passwordLevel.larger = level === 4 ? 'background-color: #00D1B2' : 'background-color: #eee'
        }
      }
    },
    methods: {
      checkCapslock({shiftKey, key} = {}) {
        if (key && key.length === 1) {
          if (shiftKey && (key >= 'a' && key <= 'z') || !shiftKey && (key >= 'A' && key <= 'Z')) {
            this.capsTooltip = true
          } else {
            this.capsTooltip = false
          }
        }
        if (key === 'CapsLock' && this.capsTooltip === true) {
          this.capsTooltip = false
        }
      },
      closeDialog() {
        this.$store.dispatch('common/setShowSignUpDialog', false);
      },
      showPwd() {
        if (this.passwordType === 'password') {
          this.passwordType = ''
        } else {
          this.passwordType = 'password'
        }
        this.$nextTick(() => {
          this.$refs.password.focus()
        })
      },
      showConfirm() {
        if (this.confirmType === 'confirm') {
          this.confirmType = ''
        } else {
          this.confirmType = 'confirm'
        }
        this.$nextTick(() => {
          this.$refs.confirm.focus()
        })
      },
      handleSignUp() {
        this.$refs.registerForm.validate(valid => {
          if (valid) {
            this.loading = true
            this.$store.dispatch('user/register', this.registerForm)
              .then(() => {
                this.loading = false
              })
              .catch(() => {
                this.loading = false
              })
            this.closeDialog();
          } else {
            console.log('error submit!!')
            return false
          }
        })
      }
    }
  }
</script>

<style lang="scss">
    /* 修复input 背景不协调 和光标变色 */
    /* Detail see https://github.com/PanJiaChen/vue-element-admin/pull/927 */

    $bg: #283443;
    $light_gray: #fff;
    $cursor: #fff;

    @supports (-webkit-mask: none) and (not (cater-color: $cursor)) {
        .register-container .el-input input {
            color: $cursor;
        }
    }

    /* reset element-ui css */
    .register-container {

        .el-dialog__header {
            line-height: normal;
        }

        .el-dialog__body {
            padding: 10px;
        }

        .el-dialog__footer {
            text-align: center;
            border-top: 1px solid #eee;
        }
        .el-input {
            display: inline-block;
            height: 47px;
            width: 85%;

            input {
                background: transparent;
                border: 0px;
                -webkit-appearance: none;
                border-radius: 0px;
                padding: 12px 5px 12px 15px;
                color: $light_gray;
                height: 47px;
                caret-color: $cursor;

                &:-webkit-autofill {
                    box-shadow: 0 0 0px 1000px $bg inset !important;
                    -webkit-text-fill-color: $cursor !important;
                }
            }
        }

        .el-form-item {
            border: 1px solid rgba(255, 255, 255, 0.1);
            background: $bg;
            border-radius: 5px;
            color: #454545;
        }
    }
</style>

<style lang="scss" scoped>
    $bg: #2d3a4b;
    $dark_gray: #889aa4;
    $light_gray: #eee;

    .register-container {
        min-height: 100%;
        width: 100%;
        background-color: $bg;
        overflow: hidden;

        .register-form {
            position: relative;
            width: 80%;
            max-width: 100%;
            padding: 10px;
            margin: 0 auto;
            overflow: hidden;
            .register-btn {
                width: 100%;
                height: 40px;
                font-size: 16px;
            }

            .password-level span {
                display: inline-block;
                width: 30%;
                font-size: 14px;
                height: 25px;
                color: #fff;
                line-height: 25px;
                text-align: center;
            }

            .small {
                border-top-left-radius: 5px;
                border-bottom-left-radius: 5px;
                border-right: 0px solid;
                margin-right: 3px;
            }

            .middle {
                border-left: 0px solid;
                border-right: 0px solid;
                margin-left: -5px;
                margin-right: 3px;
            }

            .larger {
                border-top-right-radius: 5px;
                border-bottom-right-radius: 5px;
                border-left: 0px solid;
                margin-left: -5px;
            }
        }

        .tips {
            font-size: 14px;
            color: #fff;
            margin-bottom: 10px;

            span {
                &:first-of-type {
                    margin-right: 16px;
                }
            }
        }

        .svg-container {
            padding: 6px 5px 6px 15px;
            color: $dark_gray;
            line-height: 40px;
            vertical-align: middle;
            width: 30px;
            display: inline-block;
        }

        .show-pwd {
            position: absolute;
            right: 10px;
            top: 7px;
            font-size: 16px;
            color: $dark_gray;
            cursor: pointer;
            user-select: none;
        }
    }
</style>