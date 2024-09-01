<template>
    <section>
        <div class="row">
            <div class="col-12">
                <view-title/>
            </div>
            <dynamic-menu>
                <div class="float-right">
                    <router-link 
                        v-if="proceeding" 
                        :to="`/expedientes/${proceeding.id}/ver`" 
                        tag="button" 
                        class="ml-1">
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
                        <div class="col-12">
                            <input v-model="proceeding.name" maxlength="100" type="text" name="name" placeholder="* Nombre del expediente" required>
                        </div>
                        <div class="col-12">
                            <textarea v-model="proceeding.description" 
                                type="text" 
                                name="description" 
                                maxlength="200"
                                placeholder="Descripción del expediente">
                            </textarea>
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
            return this.$store.state.proceedings.loading
        }
    },
    data(){
        return {
            proceeding: null,
        }
    },
    mounted(){
        this.getProceedingToEdit()
    },
    methods: {
        saveProceeding(ev){
            this.$store.state.proceedings.loading = true
            this.$store.dispatch('saveProceeding', {isUpdate: true, data: this.proceeding})
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
        },
        getProceedingToEdit(){
            let id = this.$route.params.id

            this.$store.state.proceedings.loading = true
            this.$store.dispatch('getProceeding', {id})
            .then(r=>{
                let res = r.data
                if( res.success){
                    this.proceeding = res.data
                }
            })
            .catch(err=>{
                window.onXHRError(err)
            })
            .finally(()=>{
                this.$store.state.proceedings.loading = false
            })
        }
    }
}
</script>

<style scoped>
.template-selector{
    max-width: 100%;
}
</style>