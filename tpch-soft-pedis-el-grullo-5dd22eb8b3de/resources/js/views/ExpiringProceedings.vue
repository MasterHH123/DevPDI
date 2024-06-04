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
                        v-model="$store.state.expiringProceedings.filters.term"
                        type="search"
                        placeholder="Buscar..">
                </div>
            </div>
            <div class="col-6">
                <ajax-search
                    :selected-value="$store.state.expiringProceedings.filters.citizen"
                    @select="(option)=>$store.state.expiringProceedings.filters.citizen = option"
                    :ajax-source="'/citizens'"
                    placeholder="Fitrar por ciudadano"
                    :template-fn="(citizen)=>`${citizen.first_name} ${citizen.last_name} ${citizen.second_last_name}`"
                />
            </div>
            <div class="col-6">
                <select
                    v-model="$store.state.expiringProceedings.filters.status"
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
                        v-model="$store.state.expiringProceedings.filters.minAge"
                        placeholder="Edad mínima"
                    >
                    <span class="tag d-block m-auto">a</span>
                    <input
                        type="number"
                        class="w-50"
                        v-model="$store.state.expiringProceedings.filters.maxAge"
                        placeholder="Edad máxima"
                    >
                    <span class="tag d-block m-auto">años</span>
                </div>
            </div>
            <div class="col-6 text-right">
                <h3 class="text-gray">
                    Total: {{ $store.state.expiringProceedings.totalFiltered }}
                </h3>
            </div>
        </dynamic-menu>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <expiring-table />
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import viewTitle from '../components/global/viewTitle'
import dynamicMenu from '../components/global/DynamicMenu'
import expiringTable from '../components/proceedings/ExpiringTable'
import AjaxSearch from '../components/global/AjaxSearch'
export default {
    components: {
        viewTitle,
        dynamicMenu,
        expiringTable,
        AjaxSearch,
    },
    watch: {
        '$store.state.expiringProceedings.filters':{
            deep: true,
            handler(){
                if( this.__searchDebouncer )
                    clearTimeout( this.__searchDebouncer )

                this.__searchDebouncer = setTimeout(()=>{
                    this.$store.dispatch('getExpiringProceedings')
                }, 300)
            }
        },

    }
}
</script>

