<template>
    <div class="table-container">
        <empty-message v-show="isEmpty"/>
        <loading v-show="isLoading"/>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Folio</th>
                        <th>Foto</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Alias</th>
                        <th>Turnos</th>
                        <th>*</th>
                    </tr>
                </thead>
                <tbody>
                    <tr 
                        :class="{inactive: !user.is_active}"
                        v-for="user in usersList" 
                        :key="user.id">
                        <td data-label="Folio">
                            <span class="tag">
                                {{ user.id }}
                            </span>
                        </td>
                        <td data-label="Foto">
                            <div 
                                class="icon-bg icon-lg bg-primary-vanished round-xl d-block m-auto" 
                                :style="{backgroundImage: `url(${user.avatar_full_path})`}">
                            </div>
                        </td>
                        <td data-label="Nombre">
                            <span class="text-heavy">
                                {{ user.first_name }} {{ user.last_name }}
                            </span>
                        </td>
                        <td data-label="Correo">
                            <span class="tag">
                                {{ user.email }}
                            </span>
                        </td>
                        <td data-label="Usuario">
                            <span class="tag">
                                {{ user.username }}
                            </span>
                        </td>
                        <td data-label="Turnos">
                            <ul class="list-unstyled p0">
                                <span class="tag" v-if="user.is_admin">
                                    Todos
                                </span>
                                <div v-else>
                                    <li class="tag" v-for="ws in user.work_shifts" :key="ws.id">
                                        {{ ws.name }}
                                    </li>
                                </div> 
                            </ul>
                        </td>
                        <td data-label="*">
                            <div class="d-flex">
                                <router-link 
                                    v-if="user.is_active && userHasPermissionTo('users.update')"
                                    :to="`/usuarios/${user.id}/editar`" 
                                    tag="button" 
                                    class="btn-sm btn-info">
                                    Editar
                                </router-link>
                                &nbsp;
                                <button 
                                    v-if="!user.is_admin && user.is_active && userHasPermissionTo('users.archive')"
                                    @click.prevent="$store.dispatch('deleteUser', user)" 
                                    class="btn-sm btn-warning">
                                    Archivar
                                </button>
                                <button 
                                    v-else-if="!user.is_admin && !user.is_active && userHasPermissionTo('users.archive')"
                                    @click.prevent="$store.dispatch('restoreUser', user)" 
                                    class="btn-sm btn-success">
                                    Desarchivar
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="table-pagination">
            <div class="d-block">
                <button 
                    class="btn-sm btn-neutral" 
                    @click="prev" 
                    :disabled="!hasPrev">
                    &larr; Anterior
                </button>
                <button 
                    class="btn-sm btn-neutral" 
                    @click="next" 
                    :disabled="!hasNext">
                    Siguiente &rarr;
                </button>
            </div>
            <small class="text-gray">
                Mostrando registros del {{ pagination.from }} al {{ pagination.to}}
            </small>
        </div>
    </div>
</template>

<script>
import loading from '../global/Loading'
import emptyMessage from '../global/Empty'
export default {
    components: {
        loading,
        emptyMessage,
    },
    mounted(){
        this.$store.dispatch('getUsers')
    },
    computed: {
        users(){
            return this.$store.state.users
        },
        usersList(){
            return this.users.list
        },
        pagination(){
            return this.users.pagination
        },
        isLoading(){
            return this.users.loading
        },
        isEmpty(){
            return this.usersList.length < 1
        },
        hasPrev(){
            return this.pagination.prevPageUrl != null
        },
        hasNext(){
            return this.pagination.nextPageUrl != null
        },
    },
    methods: {
        prev(){
            if( this.hasPrev )
                this.$store.dispatch(
                    'getUsers', 
                    this.pagination.prevPageUrl
                )
        },
        next(){
            if( this.hasNext )
                this.$store.dispatch(
                    'getUsers', 
                    this.pagination.nextPageUrl
                )
        }
    }
}
</script>

<style>

</style>