<template>
    <el-dialog :title="$t('login.title')" customClass="login-container" width="520px" :visible="showDialog"
               @close="closeDialog">
        <el-form ref="loginForm" :model="loginForm" :rules="loginRules" class="login-form" auto-complete="on">
            <el-form-item prop="username">
                <span class="svg-container">
                  <svg-icon icon-class="user"/>
                </span>
                <el-input
                        ref="username"
                        v-model="loginForm.username"
                        :placeholder="$t('login.username')"
                        name="username"
                        type="text"
                        tabindex="1"
                        auto-complete="on"
                />
            </el-form-item>
            <el-tooltip v-model="capsTooltip" content="Caps lock is On" placement="right" manual>
                <el-form-item prop="password">
                  <span class="svg-container">
                    <svg-icon icon-class="password"/>
                  </span>
                    <el-input
                            :key="passwordType"
                            ref="password"
                            v-model="loginForm.password"
                            :type="passwordType"
                            :placeholder="$t('login.password')"
                            name="password"
                            tabindex="2"
                            auto-complete="on"
                            @keyup.native="checkCapslock"
                            @blur="capsTooltip = false"
                            @keyup.enter.native="handleLogin"
                    />
                    <span class="show-pwd" @click="showPwd">
                        <svg-icon :icon-class="passwordType === 'password' ? 'eye' : 'eye-open'"/>
                      </span>
                </el-form-item>
            </el-tooltip>
            <el-button type="primary" class="login-btn" @click="handleLogin">{{ $t('login.logIn') }}</el-button>
        </el-form>
        <div slot="footer" class="dialog-footer">
            <el-row>
                <el-button circle><svg-icon icon-class="wechat" class="icon" /></el-button>
                <el-button circle><svg-icon icon-class="qq" class="icon" /></el-button>
                <!--<el-button circle><svg-icon icon-class="github" class="icon" /></el-button>-->
                <!--<el-button circle><svg-icon icon-class="facebook" class="icon" /></el-button>-->
                <!--<el-button circle><svg-icon icon-class="weibo" class="icon" /></el-button>-->
            </el-row>
        </div>
    </el-dialog>
</template>

<script>
  export default {
    name: 'SignIn',
    data() {
      return {
        loginForm: {
          username: '',
          password: ''
        },
        loginRules: {
          username: [{
            required: true, trigger: 'blur', validator: (rule, value, callback) => {
              if (value.length === 0) {
                callback(new Error('Please enter user name.'))
              } else {
                callback()
              }
            }
          }],
          password: [{
            required: true, trigger: 'blur', validator: (rule, value, callback) => {
              if (value.length === 0) {
                callback(new Error('Please enter password.'))
              } else {
                callback()
              }
            }
          }]
        },
        passwordType: 'password',
        capsTooltip: false,
        loading: false
      }
    },
    computed: {
      showDialog() {
        return this.$store.getters.show_sign_in_dialog
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
        this.$store.dispatch('common/setShowSignInDialog', false);
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
      handleLogin() {
        this.$refs.loginForm.validate(valid => {
          if (valid) {
            this.loading = true
            this.$store.dispatch('user/login', this.loginForm)
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
      },
      // afterQRScan() {
      //   if (e.key === 'x-admin-oauth-code') {
      //     const code = getQueryObject(e.newValue)
      //     const codeMap = {
      //       wechat: 'code',
      //       tencent: 'code'
      //     }
      //     const type = codeMap[this.auth_type]
      //     const codeName = code[type]
      //     if (codeName) {
      //       this.$store.dispatch('LoginByThirdparty', codeName).then(() => {
      //         this.$router.push({ path: this.redirect || '/' })
      //       })
      //     } else {
      //       alert('第三方登录失败')
      //     }
      //   }
      // }
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
        .login-container .el-input input {
            color: $cursor;
        }
    }

    /* reset element-ui css */
    .login-container {

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

    .login-container {
        min-height: 100%;
        width: 100%;
        background-color: $bg;
        overflow: hidden;

        .login-form {
            position: relative;
            width: 80%;
            max-width: 100%;
            padding: 10px;
            margin: 0 auto;
            overflow: hidden;
            .login-btn {
                width: 100%;
                height: 40px;
                font-size: 16px;
            }
        }

        .dialog-footer{
            .el-button {
                padding: 8px;
                font-size: 24px;
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