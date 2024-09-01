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
                    <input v-model="$store.state.users.filters.term" type="search" placeholder="Buscar..">
                </div>

                <div class="d-block ml-auto float-right">
                    <router-link 
                        v-if="userHasPermissionTo('users.work_shifts')"
                        to="/turnos" 
                        tag="button">
                        🔨 Turnos
                    </router-link>
                    &nbsp;
                    <router-link 
                        v-if="userHasPermissionTo('users.store')"
                        to="/usuarios/nuevo" 
                        tag="button">
                        + Agregar
                    </router-link>
                </div>
            </dynamic-menu>
            <div class="col-12">
                <div class="card">
                    <users-table />
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import viewTitle from '../components/global/viewTitle'
import dynamicMenu from '../components/global/DynamicMenu'
import usersTable from '../components/users/Table'
export default {
    components: {
        viewTitle,
        dynamicMenu,
        usersTable,
    },
    watch: {
        '$store.state.users.filters.term'(v){
            if( this.__searchDebouncer )
                clearTimeout( this.__searchDebouncer )

            this.__searchDebouncer = setTimeout(()=>{
                this.$store.dispatch('getUsers')
            }, 300)
        }
    }
}
</script>
