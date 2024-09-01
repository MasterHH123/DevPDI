<template>
    <section>
        <div class="row">
            <div class="col-12">
                <view-title />
            </div>
        </div>
        <div class="row">
            <dynamic-menu>
                <div class="float-left">
                    <input v-model="$store.state.citizens.filters.term" type="search" placeholder="Buscar..">
                </div>

                <router-link 
                    v-if="userHasPermissionTo('citizens.store')"
                    to="/ciudadanos/nuevo" 
                    tag="button" 
                    class="d-block ml-auto">
                    + Agregar
                </router-link>
            </dynamic-menu>
            <div class="col-12">
                <div class="card">
                    <citizens-table />
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import viewTitle from '../components/global/viewTitle'
import dynamicMenu from '../components/global/DynamicMenu'
import citizensTable from '../components/citizens/Table'
export default {
    components: {
        viewTitle,
        dynamicMenu,
        citizensTable,
    },
    watch: {
        '$store.state.citizens.filters.term'(v){
            if( this.__searchDebouncer )
                clearTimeout( this.__searchDebouncer )

            this.__searchDebouncer = setTimeout(()=>{
                this.$store.dispatch('getCitizens')
            }, 300)
        }
    }
}
</script>
