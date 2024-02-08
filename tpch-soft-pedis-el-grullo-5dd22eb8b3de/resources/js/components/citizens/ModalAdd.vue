<template>
    <div class="modal modal-add-citizen" :class="{open: toggle}">
        <div class="content">
            <loading v-show="isLoading"/>
            <div class="head">
                <h4>
                    Registrar Ciudadano
                </h4>
                <span class="close" @click="close">
                    X
                </span>
            </div>
            <div class="body">
                <form class="form-block" @submit.prevent="save" ref="frm">
                    <input type="text" name="first_name" placeholder="* Nombre(s)" required>
                    <input type="text" name="last_name" placeholder="* Apellido Paterno" required>
                    <input type="text" name="second_last_name" placeholder="* Apellido Materno" required>
                    <select name="gender" required>
                        <option value="">* Género</option>
                        <option value="F">Femenino</option>
                        <option value="M">Masculino</option>
                        <option value="O">No especificado</option>
                    </select>
                    <input type="text" name="address_street" placeholder="* Domicilio" required>
                    <input type="text" name="phone" placeholder="Teléfono" minlength="10" maxlength="10">
                    <input type="date" name="birth_date" placeholder="* Fecha de nacimiento" required>
                    <input type="text" name="curp" placeholder="CURP" minlength="18" maxlength="18">
                    <button class="btn-success">
                        &check; Guardar
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import loading from '../global/Loading'
export default {
    props: {
        toggle: {
            type: Boolean,
            required: true,
        }
    },
    components: {
        loading,
    },
    data(){
        return {
            isLoading: false,
        }
    },
    mounted(){
        window.addEventListener('keydown', this.closeByEsc, false)
    },
    destroyed(){
        window.removeEventListener('keydown', this.closeByEsc, false)
    },
    methods: {
        close(){
            this.$emit('close')
        },
        closeByEsc(ev){
            if(ev.keyCode == 27)
                this.close()
        },
        save(ev){
            let form = ev.target
            let formData = new FormData(form)

            this.isLoading = true
            this.$store.dispatch('saveCitizen', formData)
            .then(r=>{
                let res = r.data
                if( res.success){
                    toast.fire(res.message, '', 'success')
                    this.$refs.frm.reset()
                    this.close()
                }
            })
            .catch((err)=>window.onXHRError(err))
            .finally(()=>this.isLoading = false)
        }
    }
}
</script>

<style>

</style>