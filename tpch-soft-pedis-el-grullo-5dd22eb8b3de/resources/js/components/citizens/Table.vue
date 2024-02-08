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
                        <th>Edad</th>
                        <th>Teléfono</th>
                        <th>Domicilio</th>
                        <th>*</th>
                    </tr>
                </thead>
                <tbody>
                    <tr 
                        :class="{inactive: !citizen.is_active}"
                        v-for="citizen in citizensList" 
                        :key="citizen.id">
                        <td data-label="Folio">
                            <span class="tag">
                                {{ citizen.id }}
                            </span>
                        </td>
                        <td data-label="Foto">
                            <div 
                                class="icon-bg icon-lg bg-primary-vanished round-xl d-block m-auto" 
                                :style="{backgroundImage: `url(${citizen.avatar_full_path})`}">
                            </div>
                        </td>
                        <td data-label="Nombre">
                            <span class="text-heavy">
                                {{ citizen.first_name }} {{ citizen.last_name }} {{ citizen.second_last_name }}
                            </span>
                        </td>
                        <td data-label="Edad">
                            <span class="tag">
                                {{ citizen.age}} años
                            </span>
                        </td>
                        <td data-label="Teléfono">
                            <a class="tag" 
                                :href="`tel:${citizen.phone}`" 
                                target="_blank">
                                {{ citizen.phone || '-' }}
                            </a>
                        </td>
                        <td data-label="Domicilio">
                            <small>
                                {{ citizen.address_street }}
                            </small>
                        </td>
                        <td data-label="*">
                            <div class="d-flex">
                                <router-link 
                                    v-if="citizen.is_active && userHasPermissionTo('citizens.update')"
                                    :to="`/ciudadanos/${citizen.id}/editar`" 
                                    tag="button" 
                                    class="btn-sm btn-info">
                                    Editar
                                </router-link>
                                &nbsp;
                                <button 
                                    v-if="citizen.is_active && userHasPermissionTo('citizens.archive')"
                                    @click.prevent="$store.dispatch('deleteCitizen', citizen)" 
                                    class="btn-sm btn-warning">
                                    Archivar
                                </button>
                                <button 
                                    v-else-if="userHasPermissionTo('citizens.archive')"
                                    @click.prevent="$store.dispatch('restoreCitizen', citizen)" 
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
        this.$store.dispatch('getCitizens')
    },
    computed: {
        citizens(){
            return this.$store.state.citizens
        },
        citizensList(){
            return this.citizens.list
        },
        pagination(){
            return this.citizens.pagination
        },
        isLoading(){
            return this.citizens.loading
        },
        isEmpty(){
            return this.citizensList.length < 1
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
                    'getCitizens', 
                    this.pagination.prevPageUrl
                )
        },
        next(){
            if( this.hasNext )
                this.$store.dispatch(
                    'getCitizens', 
                    this.pagination.nextPageUrl
                )
        }
    }
}
</script>

<style>

</style>