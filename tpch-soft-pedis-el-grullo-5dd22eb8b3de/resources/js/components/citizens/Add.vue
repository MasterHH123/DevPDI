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
                        <div class="col-12">
                            <h4>
                                Datos generales
                            </h4>
                        </div>
                        <div class="col-12 text-center">
                            <div 
                                :style="{backgroundImage: `url(${citizenAvatarPreview || '/img/icons/anonim.svg' })`}"
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
                            <button 
                                type="button"
                                class="mt-1 btn-sm w-auto m-auto" 
                                @click.prevent="$refs.hiddenAvatarInput.click()">
                                Cambiar foto
                            </button>
                        </div>
                        <div class="col-6">
                            <input type="text" name="first_name" placeholder="* Nombre(s)" required>
                        </div>
                        <div class="col-6">
                            <input type="text" name="last_name" placeholder="* Apellido paterno" required>
                        </div>
                        <div class="col-6">
                            <input type="text" name="second_last_name" placeholder="* Apellido materno" required>
                        </div>
                        <div class="col-6">
                            <select name="gender" required>
                                <option value="">* Género</option>
                                <option value="F">Femenino</option>
                                <option value="M">Masculino</option>
                                <option value="O">No especificado</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <input type="text" name="address_street" placeholder="* Domicilio" required>
                        </div>
                        <div class="col-6">
                            <input type="text" name="phone" placeholder="Teléfono" minlength="10" maxlength="10">
                        </div>
                        <div class="col-6">
                            <input type="date" name="birth_date" placeholder="* Fecha de nacimiento" required>
                        </div>
                        <div class="col-6">
                            <input type="text" name="curp" placeholder="CURP" minlength="18" maxlength="18">
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
    computed: {
        isLoading(){
            return this.$store.state.citizens.loading
        }
    },
    data(){
        return {
            citizenAvatarPreview: null,
        }
    },
    methods: {
        saveCitizen(ev){
            let formData = new FormData(ev.target)

            this.$store.state.citizens.loading = true
            this.$store.dispatch('saveCitizen', formData)
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
                this.citizenAvatarPreview = frEv.target.result
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