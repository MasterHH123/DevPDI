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
                            <template-builder ref="builder"/>
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