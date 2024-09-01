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
                    <input v-model="$store.state.appLogs.filters.term" type="search" placeholder="Buscar..">
                    <input v-model="$store.state.appLogs.filters.from" type="date" placeholder="Desde">
                    <input v-model="$store.state.appLogs.filters.to" type="date" placeholder="Hasta">
                </div>
            </dynamic-menu>
            <div class="col-12">
                <div class="card">
                    <app-logs-table />
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import viewTitle from '../components/global/viewTitle'
import dynamicMenu from '../components/global/DynamicMenu'
import appLogsTable from '../components/appLogs/Table'
export default {
    components: {
        viewTitle,
        dynamicMenu,
        appLogsTable
    },
    watch:{
        '$store.state.appLogs.filters': {
            deep: true,
            handler(v){
                if( this.__searchTimeout )
                    clearTimeout( this.__searchTimeout )

                this.__searchTimeout = setTimeout(()=>{
                    this.$store.dispatch('getAppLogs')
                }, 300)
            }
        }
    }
}
</script>

<style>

</style>