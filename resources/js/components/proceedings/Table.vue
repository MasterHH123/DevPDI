<template>
    <div class="table-container">
        <empty-message v-show="isEmpty"/>
        <loading v-show="isLoading"/>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Folio</th>
                        <th>Ciudadano</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Estatus</th>
                        <th>Registros</th>
                        <th>Fecha de creación</th>
                        <th>*</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="record in proceedingsList"
                        :key="record.id"
                        :class="{inactive: !record.is_active}">
                        <td data-label="Folio">
                            <span class="tag">
                                {{ record.id }}
                            </span>
                        </td>
                        <td data-label="Ciudadano">
                            <span class="tag" v-if="record.citizen">
                                {{ `${record.citizen.first_name} ${record.citizen.last_name} ${record.citizen.second_last_name}` }}
                            </span>
                        </td>
                        <td data-label="Nombre">
                            <small class="text-truncated-3-lines" :title="record.name">
                                {{ record.name }}
                            </small>
                        </td>
                        <td data-label="Descripción">
                            <small class="text-truncated-3-lines" :title="record.description">
                                {{ record.description || '-' }}
                            </small>
                        </td>
                        <td data-label="Estatus">
                            <b>
                                {{ record.status_badge }} {{ record.status }}
                            </b>
                        </td>
                        <td data-label="Registros">
                            <b>
                                {{ record.records_count }}
                            </b>
                        </td>
                        <td data-label="Fecha">
                            <span class="tag">
                                {{ record.readable_created_at }}
                            </span>
                        </td>
                        <td data-label="*">
                            <div class="d-flex">
                                <router-link
                                    v-if="userHasPermissionTo('proceedings.show')"
                                    :to="`/expedientes/${record.id}/ver`"
                                    tag="button"
                                    class="btn-sm">
                                    Ver
                                </router-link>
                                &nbsp;
                                <button
                                    v-if="record.is_active && userHasPermissionTo('proceedings.archive')"
                                    @click.prevent="$store.dispatch('archiveProceeding', record)"
                                    class="btn-sm btn-warning">
                                    Archivar
                                </button>
                                <button
                                    v-else-if="userHasPermissionTo('proceedings.archive')"
                                    @click.prevent="$store.dispatch('restoreProceeding', record)"
                                    class="btn-sm btn-success">
                                    Desarchivar
                                </button>
                                &nbsp;
                                <button
                                    v-if="!record.is_closed && userHasPermissionTo('proceedings.close')"
                                    @click.prevent="$store.dispatch('closeProceeding', record)"
                                    class="btn-sm btn-danger">
                                    Cerrar
                                </button>
                                <button
                                    v-else-if="userHasPermissionTo('proceedings.close')"
                                    @click.prevent="$store.dispatch('openProceeding', record)"
                                    class="btn-sm btn-success">
                                    Abrir
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
        this.$store.dispatch('getProceedings')
    },
    computed: {
        proceedings(){
            return this.$store.state.proceedings
        },
        proceedingsList(){
            return this.proceedings.list
        },
        pagination(){
            return this.proceedings.pagination
        },
        isLoading(){
            return this.proceedings.loading
        },
        isEmpty(){
            return this.proceedingsList.length < 1
        },
        hasPrev(){
            return this.pagination.prevPageUrl != null
        },
        hasNext(){
            return this.pagination.nextPageUrl != null
        }
    },
    methods: {
        prev(){
            if( this.hasPrev )
                this.$store.dispatch('getProceedings', this.pagination.prevPageUrl )
        },
        next(){
            if( this.hasNext )
                this.$store.dispatch('getProceedings', this.pagination.nextPageUrl )
        }
    }
}
</script>

<style>

</style>
