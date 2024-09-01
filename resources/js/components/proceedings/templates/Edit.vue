<template>
    <section>
        <div class="row">
            <div class="col-12">
                <view-title/>
            </div>
            <dynamic-menu>
                <router-link to="/expedientes/formatos" tag="button" class="float-right">
                    &larr; Regresar
                </router-link>
            </dynamic-menu>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <loading v-show="isLoading"/>
                    <div class="row">
                        <div class="col-12">
                            <template-builder 
                                ref="builder" 
                                :template-to-edit="template" 
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import viewTitle from '../../global/viewTitle'
import dynamicMenu from '../../global/DynamicMenu'
import loading from '../../global/Loading'
import templateBuilder from './Builder.vue'
export default {
    components: {
        viewTitle,
        dynamicMenu,
        loading,
        templateBuilder,
    },
    computed: {
        isLoading(){
            return this.$store.state.proceedingTemplates.loading
        }
    },
    data(){
        return {
            template: {}
        }
    },
    mounted(){
        let templateId = this.$route.params.id
        this.loadTemplateToEdit( templateId )
    },
    methods: {
        loadTemplateToEdit(id){
            this.$store.state.proceedingTemplates.loading = true
            this.$store.dispatch('getProceedingTemplate', id)
            .then(r=>{
                let res = r.data
                if( res.success)
                    this.template = res.data
            })
            .catch(err=>{
                window.onXHRError(err)
            })
            .finally(()=>this.$store.state.proceedingTemplates.loading = false)
        },
    },
    beforeRouteLeave(to, from, next){
        let confirmFn = ()=>{
            confirm.fire({
                title: 'Dejaste cambios sin guardar',
                text: '¿Salir del editor de formatos?',
                confirmButtonText: 'Si, no importa'
            }).then(r=>{
                if(r.isConfirmed)
                    next()
            })
        }

        if( this.$refs.builder.hasUnsavedChanges )
            confirmFn()
        else
            next()
    }
}
</script>
