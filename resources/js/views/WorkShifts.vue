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
                    <input v-model="$store.state.workShifts.filters.term" type="search" placeholder="Buscar..">
                </div>

                <div class="d-block ml-auto float-right">
                    <button 
                        v-if="userHasPermissionTo('users.work_shifts')"
                        @click="$store.dispatch('addWorkShift')">
                        + Agregar
                    </button>
                    &nbsp;
                    <button @click="$router.back()">
                        &larr; Regresar
                    </button>
                </div>
            </dynamic-menu>
            <div class="col-12">
                <div class="card">
                    <workShifts-table />
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import viewTitle from '../components/global/viewTitle'
import dynamicMenu from '../components/global/DynamicMenu'
import workShiftsTable from '../components/users/workShifts/Table'
export default {
    components: {
        viewTitle,
        dynamicMenu,
        workShiftsTable,
    },
    watch: {
        '$store.state.workShifts.filters.term'(v){
            if( this.__searchDebouncer )
                clearTimeout( this.__searchDebouncer )

            this.__searchDebouncer = setTimeout(()=>{
                this.$store.dispatch('getWorkShifts', {paginate: 1})
            }, 300)
        }
    }
}
</script>
