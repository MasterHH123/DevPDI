<template>
    <section>
        <div class="row">
            <div class="col-12">
                <view-title />
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card row">
                    <loading v-show="isLoading"/>
                    <div class="col-4 text-center">
                        <div 
                            :style="{backgroundImage: `url(${user.avatar_full_path})`}"
                            class="icon-bg profile-photo">
                        </div>
                        <input 
                            type="file"
                            accept="image/*" 
                            name="avatar" 
                            ref="hiddenAvatarInput" 
                            @change.prevent="showAvatarPreview" 
                            v-show="false"
                        >
                        <button class="mt-1 btn-sm" @click.prevent="$refs.hiddenAvatarInput.click()">
                            Cambiar foto
                        </button>
                    </div>
                    <div class="col-8">
                        <form @submit.prevent="saveUser" class="row form-block">
                            <input type="hidden" name="_method" value="PUT">
                            <div class="col-12">
                                <h4 class="m-auto">
                                    Mis datos
                                </h4>
                            </div>
                            <div class="col-6">
                                <input type="text" :value="user.first_name" name="first_name" placeholder="* Nombre(s)" tabindex="1" required>
                                <input type="text" :value="user.second_last_name" name="second_last_name" placeholder="Apellido materno" tabindex="3">
                            </div>
                            <div class="col-6">
                                <input type="text" :value="user.last_name" name="last_name" placeholder="* Apellido paterno" tabindex="2" required>
                                <input type="email" :value="user.email" name="email" placeholder="* Correo electrónico" tabindex="4" required>
                            </div>
                            <div class="col-6">
                                <input type="text" :value="user.username" name="username" placeholder="* Alias para iniciar sesión" tabindex="5" required>
                            </div>
                            <div class="col-6 d-flex">
                                <label class="checkbox w-33 mr-1">
                                    <input type="checkbox" name="change_password" v-model="willChangePass">
                                    <span class="checkmark"></span>
                                    Cambiar clave
                                </label>
                                <input type="password" name="password" placeholder="* Clave de acceso" minlength="8" tabindex="6" autocomplete="new-password" :disabled="!willChangePass" :required="willChangePass">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="ml-auto d-block w-auto btn-success">
                                    &check; Guardar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import viewTitle from '../global/viewTitle'
import loading from '../global/Loading'
export default {
    components: {
        viewTitle,
        loading
    },
    computed: {
        user(){
            return this.$store.state.user
        },
        isLoading(){
            return this.$store.state.users.loading
        }
    },
    mounted(){
        this.originalAvatar = this.user.avatar_full_path
    },
    destroyed(){
        this.user.avatar_full_path = this.originalAvatar
    },
    data(){
        return {
            willChangePass: false,
            originalAvatar: null,
        }
    },
    methods: {
        saveUser(ev){
            let formData = new FormData(ev.target)
            let avatarPicker = this.$refs.hiddenAvatarInput

            // append avatar
            if( avatarPicker.files.length == 1 )
                formData.append('avatar', avatarPicker.files[0])

            // Append workshifts to bypass back-validation
            this.user.work_shifts.forEach((ws)=>formData.append('work_shifts[]', ws.id))

            this.$store.state.users.loading = true
            this.$store.dispatch('updateUser', {
                id: this.user.id,
                data: formData
            })
            .then(r=>{
                let res = r.data
                if( res.success ){
                    toast.fire(res.message, '', 'success')
                    this.$store.commit('setUserProfile', res.data)
                    this.originalAvatar = res.data.avatar_full_path
                }
            })
            .catch(err=>{
                window.onXHRError(err)
            })
            .finally(()=>this.$store.state.users.loading = false)
        },
        showAvatarPreview(ev){
            let input = ev.target
            let imageFile = input.files[0]
            if( !imageFile || imageFile.type.indexOf('image') === -1 )
                return toast.fire('Selecciona un archivo de imagen!', '', 'info')

            let fr = new FileReader()
            fr.onload = (frEv)=>{
                this.user.avatar_full_path = frEv.target.result
            }
            fr.readAsDataURL(imageFile)
        }
    }
}
</script>

<style scoped>
.profile-photo{
    width: 12em;
    height: 12em;
    background-color: var(--primary-vanished);
    border-radius: 50%;
    display: block;
    margin: auto;
}
</style>