<template>
    <section>
        <div class="row">
            <div class="col-12">
                <view-title />
            </div>
        </div>
        <div class="row">
            <dynamic-menu>
                <div class="float-left mr-1">
                    <input 
                        v-model="$store.state.proceedingTemplates.filters.term" 
                        type="search" placeholder="Buscar.."
                    >
                </div>

                <div class="ml-auto d-flex">
                    <router-link 
                        to="/expedientes" 
                        tag="button" 
                        class="d-block ml-auto">
                        &larr; Regresar
                    </router-link>
                    <span>&nbsp;</span>
                    <router-link
                        v-if="userHasPermissionTo('proceeding_templates.store')"
                        to="/expedientes/formatos/nuevo" 
                        tag="button" 
                        class="d-block">
                        + Agregar
                    </router-link>
                </div>
            </dynamic-menu>
            <div class="col-12">
                <div class="card">
                    <proceeding-templates-table />
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import viewTitle from '../components/global/viewTitle'
import dynamicMenu from '../components/global/DynamicMenu'
import proceedingTemplatesTable from '../components/proceedings/templates/Table'
export default {
    components: {
        viewTitle,
        dynamicMenu,
        proceedingTemplatesTable,
    },
    watch: {
        '$store.state.proceedingTemplates.filters.term'(v){
            if( this.__searchDebouncer )
                clearTimeout( this.__searchDebouncer )

            this.__searchDebouncer = setTimeout(()=>{
                this.$store.dispatch('getProceedingTemplates')
            }, 300)
        }
    }
}
</script>
