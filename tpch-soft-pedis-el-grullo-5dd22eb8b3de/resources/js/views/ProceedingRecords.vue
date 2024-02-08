<template>
    <section>
        <div class="row">
            <div class="col-12">
                <view-title />
            </div>
        </div>
        <dynamic-menu class="row">
            <div class="col-12">
                <div class="float-left mr-1">
                    <input 
                        class=""
                        v-model="$store.state.proceedingRecords.filters.term" 
                        type="search" 
                        placeholder="Buscar..">
                </div>

                <div class="float-right d-flex">
                    <router-link 
                        v-if="userHasPermissionTo('proceeding_templates.show')"
                        to="/expedientes/formatos" 
                        tag="button" 
                        class="btn-yellow d-block">
                        📁 Formatos
                    </router-link>
                    <span>&nbsp;</span>
                    <router-link 
                        v-if="userHasPermissionTo('proceedings.store')"
                        to="/expedientes/nuevo" 
                        tag="button" 
                        class="d-block">
                        + Registrar
                    </router-link>
                </div>
            </div> 
            <div class="col-4">
                <ajax-search
                    :selected-value="$store.state.proceedingRecords.filters.citizen"
                    @select="(option)=>$store.state.proceedingRecords.filters.citizen = option"
                    :ajax-source="'/citizens'"
                    placeholder="Fitrar por ciudadano"
                    :template-fn="(citizen)=>`${citizen.first_name} ${citizen.last_name} ${citizen.second_last_name}`"
                />
            </div>
            <div class="col-4">
                <ajax-search
                    :selected-value="$store.state.proceedingRecords.filters.template"
                    @select="(option)=>$store.state.proceedingRecords.filters.template = option"
                    :ajax-source="'/proceeding-templates'"
                    placeholder="Fitrar por formato"
                    :template-fn="(template)=>`${template.name}`"
                />
            </div>
            <div class="col-4">
                <ajax-search
                    :selected-value="$store.state.proceedingRecords.filters.user"
                    @select="(option)=>$store.state.proceedingRecords.filters.user = option"
                    :ajax-source="'/users'"
                    placeholder="Fitrar por aplicador"
                    :template-fn="(user)=>`${user.first_name} ${user.last_name}`"
                />
            </div>
            <div class="col-6">
                <label>Rango de Edad</label>
                <div class="d-flex">
                    <span class="tag d-block m-auto">De</span>
                    <input 
                        type="number" 
                        class="w-50" 
                        v-model="$store.state.proceedingRecords.filters.minAge"
                        placeholder="Edad mínima"
                    >
                    <span class="tag d-block m-auto">a</span>
                    <input 
                        type="number" 
                        class="w-50" 
                        v-model="$store.state.proceedingRecords.filters.maxAge"
                        placeholder="Edad máxima"
                    >
                    <span class="tag d-block m-auto">años</span>
                </div>
            </div>
            <div class="col-6 text-right">
                <h3 class="text-gray">
                    Resultados: {{ $store.state.proceedingRecords.totalFiltered }}
                </h3>
            </div>
        </dynamic-menu>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <proceeding-records-table />
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import viewTitle from '../components/global/viewTitle'
import dynamicMenu from '../components/global/DynamicMenu'
import proceedingRecordsTable from '../components/proceedings/records/Table'
import AjaxSearch from '../components/global/AjaxSearch'
export default {
    components: {
        viewTitle,
        dynamicMenu,
        proceedingRecordsTable,
        AjaxSearch,
    },
    watch: {
        '$store.state.proceedingRecords.filters':{
            deep: true,
            handler(){
                if( this.__searchDebouncer )
                    clearTimeout( this.__searchDebouncer )

                this.__searchDebouncer = setTimeout(()=>{
                    this.$store.dispatch('getProceedingRecords')
                }, 300)
            }
        },

    }
}
</script>
