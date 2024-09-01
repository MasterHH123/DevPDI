<template>
    <section>
        <div class="row">
            <div class="col-12">
                <view-title/>
            </div>
            <dynamic-menu>
                <div class="float-right">
                    <router-link to="/expedientes" tag="button" class="ml-1">
                        &larr; Regresar
                    </router-link>
                </div>
            </dynamic-menu>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <loading v-show="isLoading"/>
                    <form @submit.prevent="saveProceeding" class="row form-block">
                        <div class="col-12">
                            <h4>
                                Carpeta de investigación
                            </h4>
                        </div>
                        <div class="col-6">
                            <ajax-search
                                :selected-value="citizen"
                                @select="(option)=>citizen = option"
                                :ajax-source="'/citizens'"
                                placeholder="Ciudadano"
                                :template-fn="(citizen)=>`${citizen.first_name} ${citizen.last_name} ${citizen.second_last_name}`"
                            />
                        </div>
                        <div class="col-6">
                            <input type="text" name="name" placeholder="* Nombre del expediente" required>
                        </div>
                        <div class="col-12">
                            <input type="text" name="description" placeholder="Descripción del expediente">
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
import AjaxSearch from '../global/AjaxSearch'
import loading from '../global/Loading'
export default {
    components: {
        viewTitle,
        dynamicMenu,
        AjaxSearch,
        loading,
    },
    computed: {
        templates(){
            return this.$store.state.proceedingTemplates.list
        },
        isLoading(){
            return this.$store.state.proceedings.loading
        }
    },
    data(){
        return {
            citizen: null,
        }
    },
    methods: {
        saveProceeding(ev){
            let formData = new FormData(ev.target)

            // Check citizen was selected
            if( !this.citizen )
                return toast.fire('Olvidaste seleccionar al ciudadano', '', 'info')

            formData.append('citizen_id', this.citizen.id)
            formData.append('status', 'Abierto')

            this.$store.state.proceedings.loading = true
            this.$store.dispatch('saveProceeding', {data: formData})
            .then(r=>{
                let res = r.data
                if( res.success ){
                    toast.fire(res.message, '', 'success')
                    this.$router.push(`/expedientes/${res.data.id}/ver`)
                }
            })
            .catch(err=>{
                window.onXHRError(err)
            })
            .finally(()=>this.$store.state.proceedings.loading = false)
        }
    }
}
</script>

<style scoped>
.template-selector{
    max-width: 100%;
}
</style>