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
                        v-model="$store.state.proceedings.filters.term" 
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
            <div class="col-6">
                <ajax-search
                    :selected-value="$store.state.proceedings.filters.citizen"
                    @select="(option)=>$store.state.proceedings.filters.citizen = option"
                    :ajax-source="'/citizens'"
                    placeholder="Fitrar por ciudadano"
                    :template-fn="(citizen)=>`${citizen.first_name} ${citizen.last_name} ${citizen.second_last_name}`"
                />
            </div>
            <div class="col-6">
                <select 
                    v-model="$store.state.proceedings.filters.status" 
                    class="w-100">
                    <option value="">Filtrar por estatus</option>
                    <option>Abierto</option>
                    <option>Cerrado</option>
                </select>
            </div>
            <div class="col-6">
                <label>Rango de Edad</label>
                <div class="d-flex">
                    <span class="tag d-block m-auto">De</span>
                    <input 
                        type="number" 
                        class="w-50" 
                        v-model="$store.state.proceedings.filters.minAge"
                        placeholder="Edad mínima"
                    >
                    <span class="tag d-block m-auto">a</span>
                    <input 
                        type="number" 
                        class="w-50" 
                        v-model="$store.state.proceedings.filters.maxAge"
                        placeholder="Edad máxima"
                    >
                    <span class="tag d-block m-auto">años</span>
                </div>
            </div>
            <div class="col-6 text-right">
                <h3 class="text-gray">
                    Total: {{ $store.state.proceedings.totalFiltered }}
                </h3>
            </div>
        </dynamic-menu>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <proceedings-table />
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import viewTitle from '../components/global/viewTitle'
import dynamicMenu from '../components/global/DynamicMenu'
import proceedingsTable from '../components/proceedings/Table'
import AjaxSearch from '../components/global/AjaxSearch'
export default {
    components: {
        viewTitle,
        dynamicMenu,
        proceedingsTable,
        AjaxSearch,
    },
    watch: {
        '$store.state.proceedings.filters':{
            deep: true,
            handler(){
                if( this.__searchDebouncer )
                    clearTimeout( this.__searchDebouncer )

                this.__searchDebouncer = setTimeout(()=>{
                    this.$store.dispatch('getProceedings')
                }, 300)
            }
        },

    }
}
</script>
