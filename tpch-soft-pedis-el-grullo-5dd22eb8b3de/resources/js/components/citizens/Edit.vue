<template>
    <section>
        <div class="row">
            <div class="col-12">
                <view-title/>
            </div>
            <dynamic-menu>
                <router-link to="/ciudadanos" tag="button" class="float-right">
                    &larr; Regresar
                </router-link>
            </dynamic-menu>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <loading v-show="isLoading"/>
                    <form @submit.prevent="saveCitizen" class="row form-block">
                        <input type="hidden" name="_method" value="put">
                        <div class="col-12">
                            <h4>
                                Datos generales
                            </h4>
                        </div>
                        <div class="col-12 text-center">
                            <div 
                                :style="{backgroundImage: `url(${citizen.avatar_full_path || '/img/icons/anonim.svg' })`}"
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
                            <div class="d-flex">
                                <button 
                                    type="button"
                                    class="mt-1 btn-sm w-auto ml-auto" 
                                    @click.prevent="$refs.hiddenAvatarInput.click()">
                                    Cambiar<br>
                                    foto
                                </button>
                                &nbsp;
                                <button 
                                    type="button"
                                    class="mt-1 btn-sm w-auto mr-auto btn-danger" 
                                    @click.prevent="removeAvatar">
                                    Quitar<br>
                                    foto
                                </button>
                            </div>
                        </div>
                        <div class="col-6">
                            <input type="text" :value="citizen.first_name" name="first_name" placeholder="* Nombre(s)" required>
                        </div>
                        <div class="col-6">
                            <input type="text" :value="citizen.last_name" name="last_name" placeholder="* Apellido paterno" required>
                        </div>
                        <div class="col-6">
                            <input type="text" :value="citizen.second_last_name" name="second_last_name" placeholder="* Apellido materno" required>
                        </div>
                        <div class="col-6">
                            <select name="gender" :value="citizen.gender" required>
                                <option value="">* Género</option>
                                <option value="F">Femenino</option>
                                <option value="M">Masculino</option>
                                <option value="O">No especificado</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <input type="text" :value="citizen.address_street" name="address_street" placeholder="* Domicilio" required>
                        </div>
                        <div class="col-6">
                            <input type="text" :value="citizen.phone" name="phone" placeholder="Teléfono" minlength="10" maxlength="10">
                        </div>
                        <div class="col-6">
                            <input type="date" :value="citizen.birth_date_std" name="birth_date" placeholder="* Fecha de nacimiento" required>
                        </div>
                        <div class="col-6">
                            <input type="text" :value="citizen.curp" name="curp" placeholder="CURP" minlength="18" maxlength="18">
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
    </section>
</template>

<script>
import viewTitle from '../global/viewTitle'
import dynamicMenu from '../global/DynamicMenu'
import loading from '../global/Loading'
export default {
    components: {
        viewTitle,
        dynamicMenu,
        loading,
    },
    mounted(){
        let citizenId = this.$route.params.id
        this.loadCitizenToEdit( citizenId )
    },
    data(){
        return  {
            citizen: {},
        }
    },
    computed: {
        isLoading(){
            return this.$store.state.citizens.loading
        }
    },
    methods: {
        loadCitizenToEdit(id){
            this.$store.state.citizens.loading = true
            this.$store.dispatch('getCitizen', {id})
            .then(r=>{
                let res = r.data
                if( res.success)
                    this.citizen = res.data
            })
            .catch(err=>{
                window.onXHRError(err)
            })
            .finally(()=>this.$store.state.citizens.loading = false)
        },
        saveCitizen(ev){
            let formData = new FormData(ev.target)

            if( this.citizen.avatar_full_path == null )
                formData.append('did_remove_avatar', 1)

            this.$store.state.citizens.loading = true
            this.$store.dispatch('updateCitizen', {
                id: this.$route.params.id,
                data: formData
            })
            .then(r=>{
                let res = r.data
                if( res.success ){
                    toast.fire(res.message, '', 'success')
                    this.$router.push('/ciudadanos')
                }
            })
            .catch(err=>{
                window.onXHRError(err)
            })
            .finally(()=>this.$store.state.citizens.loading = false)
        },
        showAvatarPreview(ev){
            let input = ev.target
            let imageFile = input.files[0]
            if( !imageFile || imageFile.type.indexOf('image') === -1 )
                return toast.fire('Selecciona un archivo de imagen!', '', 'info')

            let fr = new FileReader()
            fr.onload = (frEv)=>{
                this.citizen.avatar_full_path = frEv.target.result
            }
            fr.readAsDataURL(imageFile)
        },
        removeAvatar(){
            this.$refs.hiddenAvatarInput.value = ''
            this.citizen.avatar_full_path = null
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