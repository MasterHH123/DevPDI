<template>
    <section>
        <div class="row">
            <div class="col-12">
                <view-title/>
            </div>
            <dynamic-menu>
                <router-link to="/usuarios" tag="button" class="float-right">
                    &larr; Regresar
                </router-link>
            </dynamic-menu>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <loading v-show="isLoading"/>
                    <form @submit.prevent="saveUser" class="row form-block">
                        <input type="hidden" name="_method" value="PUT">
                        <div class="col-12">
                            <h4>
                                Datos generales
                            </h4>
                        </div>
                        <div class="col-6">
                            <input type="text" :value="user.first_name" name="first_name" placeholder="* Nombre(s)" required>
                        </div>
                        <div class="col-6">
                            <input type="text" :value="user.last_name" name="last_name" placeholder="* Apellido paterno" required>
                        </div>
                        <div class="col-6">
                            <input type="text" :value="user.second_last_name" name="second_last_name" placeholder="Apellido materno" >
                        </div>
                        <div class="col-6">
                            <input type="email" :value="user.email" name="email" placeholder="* Correo electrónico" required>
                        </div>
                        <div class="col-6">
                            <input type="text" :value="user.username" name="username" placeholder="* Alias para iniciar sesión" required>
                        </div>
                        <div class="col-6 d-flex">
                            <label class="checkbox w-33 mr-1">
                                <input type="checkbox" name="change_password" v-model="willChangePass">
                                <span class="checkmark"></span>
                                Cambiar clave
                            </label>
                            <input type="password" name="password" placeholder="* Clave de acceso" minlength="8" autocomplete="new-password" :disabled="!willChangePass" :required="willChangePass">
                        </div>
                        <div class="col-12">
                            <h4>Turnos de trabajo</h4>
                            <div class="row">
                                <div class="col-3" v-for="ws in workShifts" :key="ws.id">
                                    <label class="checkbox">
                                        <input type="checkbox" name="work_shifts[]" :value="ws.id">
                                        <span class="checkmark"></span>
                                        {{ ws.name }}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <h4>
                                Permisos
                            </h4>
                        </div>
                        <div class="col-12">
                            <permissions-table-selection 
                                :user-permissions="user.permissions || []" 
                                :is-super-admin="user.is_admin"
                            />
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
import permissionsTableSelection from '../permissions/SelectionTable'
export default {
    components: {
        viewTitle,
        dynamicMenu,
        loading,
        permissionsTableSelection
    },
    mounted(){
        let userId = this.$route.params.id
        this.$store.dispatch('getWorkShifts', {paginate:0})
        this.loadUserToEdit( userId )
    },
    data(){
        return  {
            user: {},
            willChangePass: false
        }
    },
    computed: {
        isLoading(){
            return this.$store.state.users.loading
        },
        workShifts(){
            return this.$store.state.workShifts.list
        },
    },
    methods: {
        loadUserToEdit(id){
            this.$store.state.users.loading = true
            this.$store.dispatch('getUser', id)
            .then(r=>{
                let res = r.data
                if( res.success)
                {
                    this.user = res.data

                    // Map work shifts
                    setTimeout(()=>{
                        this.user.work_shifts.forEach(ws=>{
                            let el = document.querySelector(`[name="work_shifts[]"][value="${ws.id}"]`)
                            if(el)el.checked = true
                        })
                    }, 300)
                }
            })
            .catch(err=>{
                window.onXHRError(err)
            })
            .finally(()=>this.$store.state.users.loading = false)
        },
        saveUser(ev){
            // Check at least 1 shift
            let shifts = 0

            document.querySelectorAll('[name="work_shifts[]"]')
            .forEach(chk=>{
                if( this.user.is_admin )
                    chk.checked = true;
                
                if( chk.checked )
                    shifts++
            })

            if( shifts == 0 )
                return toast.fire('Elige almenos un turno de trabajo', '', 'info')

            let formData = new FormData(ev.target)
            this.$store.state.users.loading = true
            this.$store.dispatch('updateUser', {
                id: this.$route.params.id,
                data: formData
            })
            .then(r=>{
                let res = r.data
                if( res.success ){
                    toast.fire(res.message, '', 'success')
                    this.$router.push('/usuarios')
                }
            })
            .catch(err=>{
                window.onXHRError(err)
            })
            .finally(()=>this.$store.state.users.loading = false)
        }
    }
}
</script>

<style>

</style>