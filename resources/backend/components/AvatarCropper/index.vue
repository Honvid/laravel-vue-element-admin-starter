<template>
  <div class="avater-cropper-container">
    <div @click="avatarDialogVisible = true">
      <el-avatar shape="square" :size="50" :src="newAvatar" @error="!newAvatar">
        <img src="@/assets/empty.png"/>
      </el-avatar>
    </div>
    <el-dialog :visible.sync="avatarDialogVisible" :title="$t('user.editAvatar')" append-to-body>
        <div class="cropper-content">
          <div class="cropper">
            <vue-cropper
              ref="cropper"
              :img="option.image"
              :outputSize="option.size"
              :outputType="option.outputType"
              :info="true"
              :full="option.full"
              :canMove="option.canMove"
              :canMoveBox="option.canMoveBox"
              :original="option.original"
              :autoCrop="option.autoCrop"
              :autoCropWidth="option.autoCropWidth"
              :autoCropHeight="option.autoCropHeight"
              :fixed="option.fixed"
              @realTime="realTime"
              @imgLoad="imgLoad"
            ></vue-cropper>
          </div>
          <div class="show-preview" :style="{'width': previews.w + 'px', 'height': previews.h + 'px',  'overflow': 'hidden', 'margin': '5px'}">
            <div :style="previews.div" class="preview">
              <img :src="previews.url" :style="previews.img">
            </div>
          </div>
        </div>
        <el-row style="margin-top:20px;">
          <label class="el-button el-button--primary el-button--small" for="uploads" style="margin-right:10px"><i class="el-icon-upload"></i> {{ $t('avatar.upload') }}</label>
          <input type="file" id="uploads" style="position:absolute; clip:rect(0 0 0 0);" accept="image/png, image/jpeg, image/gif, image/jpg" @change="uploadImg($event)">
          <el-button icon="el-icon-zoom-in" size="mini" style="padding:7px 10px" @click="changeScale(1)">{{ $t('avatar.scaleUp') }}</el-button>
          <el-button icon="el-icon-zoom-out" size="mini" style="padding:7px 10px" @click="changeScale(-1)">{{ $t('avatar.scaleDown') }}</el-button>
          <el-button icon="el-icon-refresh-left" size="mini" style="padding:7px 10px" @click="rotateLeft">{{ $t('avatar.leftRotation') }}</el-button>
          <el-button icon="el-icon-refresh-right" size="mini" style="padding:7px 10px" @click="rotateRight">{{ $t('avatar.rightRotation') }}</el-button>
          <el-button icon="el-icon-refresh" size="mini" style="padding:7px 10px" @click="reset">{{ $t('avatar.reset') }}</el-button>
        </el-row>
        <div slot="footer" class="dialog-footer">
            <el-button @click="avatarDialogVisible=false">
                {{ $t('table.cancel') }}
            </el-button>
            <el-button type="primary" @click="changeAvater">
                {{ $t('table.confirm') }}
            </el-button>
        </div>
    </el-dialog>
    
  </div>
</template>

<script>
  import { VueCropper } from 'vue-cropper'

  export default {
    components:{
      VueCropper
    },
    props: {
      avatar: {
        type: String,
        default:'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAHgAAAB4CAYAAAA5ZDbSAAAAAXNSR0IArs4c6QAACCJJREFUeAHtnUlvFEcUx1/P5rHx7vEy3o3B2EzCkoUTpxws8T245MoHyS3iwveI5APKgRMKEkSxkJAiEtZgDIF4ADMwTOrf0NHMZLaa7qrq17wnjQb3dNd79f911avqrm48arJr12+WqpXqRbV5u0a0TFQbbtpF/oyVAl7ZI7qnQtpJ59JXzp87s1sfnvrto+3u7ub2/z78gTz6vlarpYLt8s1HAc/zPlCNLhcm8pdKpVIFkfuAfbgvDn9SYL/jUx2JtJ0CCvTVwnj+AiD7LRUtV+C2k4vfdrD0e2MVuufn3HfVX6Vb5geyU8TortPZ9KkMBlQ1kpzbSSyOv6HBgi266G2OFZCYe1JgO/VxKtTTzrITMwXAVrVgmecy46YRbm3YH0VrHCG7MlNAADMDphuuANZVjNn+ApgZMN1wBbCuYsz2F8DMgOmGK4B1FWO2vwBmBkw3XAGsqxiz/QUwM2C64QpgXcWY7S+AmQHTDVcA6yrGbH8BzAyYbrgCWFcxZvtnmMWrHe7oyDAVpsZpZHiIcrksDagP7G3lHVXU56D8mvafvaB/DsraZXM4IJGA1YIzWpyfUZ9ZH2orEEODaRoazNP42AgtLcz6sB88ekIPHu2RWs/U6hCW2xIHGK11fW2J8gM5LSBo3UdXF2m+OEO/373vt2qtAmK6c6Jy8OryPJU217Xh1rPBiYEyUFYSLDGAtzbWaGWpGBkTlIUyuVsiAKO1zUxPRs4CZXJvyewBI+dG2XKbzxKUDR9cjTVgjJYxoDJt8AFfHI01YEyFdEfL/UCCD/jiaMwBz1rTHHNqjsYWMK5QYe5qy+ALPrkZW8AuBj4ufIY9odgCxrVl2+bCZ9g6sgVss3sORHbhM/Dd7zdbwMFdoX4r3s9xLnz2E2f9MWwB11fC1r853mRiCxj3c21b5Z19n2HryBYwbtbbNhc+w9aRLWCsxLBtB+VXtl2G9scWMJbZ2Lb9Zy9tuwztjy1grKGy2WXCF8d1W2wB49TGGipbZtNXlHViDniPDt/679yMUpP/lQUfWIzH0VgDxupHLJAzbfDBdaUla8AAi8HWn/cfG2OMsl0M6KKqEHvAEOKPe49o7+nzqDT5rxyUibI5WyIAA8DtO3cjbclouSiTuyVq4TtaW/nV674WvgcgMaBK0sL3RAEGJOTLZ89fdn10JQAafGOeK4+uBGrE/Bsj3vsPn/gfefgs5rDChoerTxyvQIWtd3B8YgZZQYXku1EBAdyoR+L+EsCJQ9pYIbaAUymP5mYL9PWZLZoYH22sVYR/DQ3lKZ1OR1ii3aLYTZOwsnGhOE3F2WnKZj+Gv7mxSjdu3o789iF8nf5ig1Jeih4+3vOnUe/fV+0SCunN+/naLyzeV5BKpfynCPG6hVYPgr14eUC3frsTUo7GwwEXr3gIrFqtKtBP1fTrL+ICmkUXjS74m7MnaXlxriVcAACIKJ/lRVn1cOEDXTVi+PZsiSYnxrAp9hZrwJlMhjaPr9Kp0nEazA90FRPiR5GPUQbKamfour88eYw21pcJPUucLbbRDao34Hx1epNmZ6Z61g9dN/JxmCcQcCzKaJUGmgMpzk37PUucH2mJJeCx0WE6e+pET622WfRcNhvq3Rp4LwfK6NXQszTn6l6PtbFf7ABPFyb8Ljmruud+rd983Crv9hIDcjO67MJk/F71ECvAmNeiBUWR13Tzcbe82w00Yj65eVQrpXQrM4rfYwMYrQ6Dll5yXy8V18nHOnm3k2/4PHFshaZi1JJjARgDqpI6+6OCG0DoNR/r5t2g/Fbf/omlRv54TWIczDlgTIWQv/Btwrrl437zbqdYM5k0lbbWY3GJ0zngLTUl6WWO20nQbr+1y8dh824nv2jBcXhTnlPAGDHbuCLUKh9HlXc7QZ6aHKM5jXl8p7L6/c0Z4HQ6Revq7a62rDkfR5l3O9VhbWVBddXOZCZnnleXF2hA85W/nYTs5bcgH5vIu+38o6dYWXL35lozI5t2tf20/cjQoH/Lr8tuRn7udI3ZiENV6IJ6B/XjJ/v05s2hKRdty3XSghfb3PJrG2WEPyAfRz0d6xYeFiesqbtTLsw6YEwhZtTg6nMzXPwIFijYrLt1wEV1OTKKS5E2RYrCl7/EaKYQRVFaZVgHPK9usX2uVpxLOGBcWMj3cOM+qScALuhgJG/TrLbgiXG7lbMpZK++bN+IsAp4bFQAYzGDTbMGGAOrOC9tsSX68JFBq4NMa4Bx5tqef9qCpuMHGoyNHNE5JNS+VgGHijRBB49a7KatAbZ93TnO54Pp26P1dbcGOMwiuvqAk/Bvm1e07AH+9BxREgCFrYMADqtgzI+32ZvZa8GG1lzFnGXL8Gy2YGv3g2/cut2ysrLRrALWAB8evjVbEym9pQLWuuiW3mWjcQUEsHGJ3ToQwG71N+5dABuX2K0DAexWf+PeBbBxid06EMBu9TfuXQAbl9itAwHsVn/j3gWwcYndOhDAbvU37l0AG5fYrQMB7FZ/494FsHGJ3ToQwG71N+5dABuX2K0DAexWf+PeBbBxid06EMBu9TfuXQAbl9itAwXYK7sNQbybU8Arq/e/0D1zDqRklwqALbroHZdBiG+jCuyk0rn0FfXM6gejbqRw6wqAKdimzp87s0s1umw9AnFoVgHFFGz9UXRhIn9JEb9q1qOUbksBsART+PMBl0qlSmE8f0G95O9H6a5tYYjeD9iBIViCKTyogVajXbt+s1StVC+qrdvq/7xbJqrZfS1MYzjyV1cFvPKnmdAOcq6fcuuO+RcbTpTXEDYkmgAAAABJRU5ErkJggg=='
      }
    },
    name: 'AvatarCropper',
    data() {
      return {
        avatarDialogVisible: false,
        previews: {},
        newAvatar: '',
        option: {
          image: '',
          size: 1,
          full: false,//输出原图比例截图 props名full
          outputType:"png",
          canMove: true,
          original: false,
          canMoveBox: true,
          autoCrop: true,
          autoCropWidth: 200,
          autoCropHeight: 200,
          fixed: true,
        }
      }
    },
    created() {
      this.option.image = this.avatar
      this.newAvatar = this.avatar
    },
    watch: {
      avatar(newValue, oldValue){
        this.avatar = newValue
        this.option.image = this.avatar
        this.newAvatar = this.avatar
      }
    },
    methods: {
      changeScale(num) {
        num = num || 1
        this.$refs.cropper.changeScale(num) 
      },
      rotateLeft() {
        this.$refs.cropper.rotateLeft()
      },
      rotateRight() {
        this.$refs.cropper.rotateRight()
      },
      // 实时预览函数
      realTime(data) {
        this.previews = data
      },
      changeAvater() {
        this.$refs.cropper.getCropData((data) => {
          this.$emit('changeAvater', data)
          this.newAvatar = data
          this.avatarDialogVisible = false
        })
      },
      uploadImg(e) {
        //上传图片
        // this.option.img
        var file = e.target.files[0]
        if (!/\.(gif|jpg|jpeg|png|bmp|GIF|JPG|PNG)$/.test(e.target.value)) {
          this.$message({
              type: 'warning',
              message: `${this.$t('avatar.validate')}`,
            })
          return false
        }
        var reader = new FileReader()
        reader.onload = (e) => {
          let data
          if (typeof e.target.result === 'object') {
            // 把Array Buffer转化为blob 如果是base64不需要
            data = window.URL.createObjectURL(new Blob([e.target.result]))
          } else {
            data = e.target.result
          }
          this.option.image = data
        }
        // 转化为base64
        reader.readAsDataURL(file)
        // 转化为blob
        // reader.readAsArrayBuffer(file)
      },
      imgLoad (msg) {
        
      },
      reset() {
        this.option.image = this.avatar
        this.newAvatar = this.avatar
      }
    }
  }
</script>

<style lang="scss">
.cropper-content{
  display: flex;
  display: -webkit-flex;
  justify-content: flex-end;
  -webkit-justify-content: flex-end;
  .cropper{
    width: 350px;
    height: 300px;
  }
  .show-preview{
    flex: 1;
    -webkit-flex: 1;
    display: flex;
    display: -webkit-flex;
    justify-content: center;
    -webkit-justify-content: center;
    .preview{
      overflow: hidden;
      border-radius: 50%;
      border:1px solid #cccccc;
      background: #cccccc;
      margin-left: 40px;
    }
  }
}
</style>