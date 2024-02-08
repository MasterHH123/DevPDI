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
                        <th>Formato</th>
                        <th>Aplicado por</th>
                        <th>Fecha de captura</th>
                        <th>*</th>
                    </tr>
                </thead>
                <tbody>
                    <tr 
                        v-for="record in proceedingRecordsList" 
                        :key="record.id" 
                        :class="{inactive: !record.is_active}">
                        <td data-label="Folio">
                            <span class="tag">
                                {{ record.id }}
                            </span>
                        </td>
                        <td data-label="Ciudadano">
                            <span class="tag">
                                {{ `${record.citizen.first_name} ${record.citizen.last_name} ${record.citizen.second_last_name}` }}
                            </span>
                        </td>
                        <td data-label="Formato">
                            <b>
                                {{ record.proceeding_template.name }}
                            </b>
                            <small class="d-block text-gray">
                                {{ `Versión ${record.proceeding_template_tag_version}.0` }} -
                                {{ `Turno ${record.proceeding_template.work_shift}` }}
                            </small>
                        </td>
                        <td data-label="Aplicado por">
                            <span class="tag">
                                {{ `${record.user.first_name} ${record.user.last_name}` }}
                            </span>
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
                                    @click.prevent="$store.dispatch('archiveProceedingRecord', record)" 
                                    class="btn-sm btn-warning">
                                    Archivar
                                </button>
                                <button 
                                    v-else-if="userHasPermissionTo('proceedings.archive')"
                                    @click.prevent="$store.dispatch('restoreProceedingRecord', record)" 
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
import loading from '../../global/Loading'
import emptyMessage from '../../global/Empty'
export default {
    components: {
        loading,
        emptyMessage,
    },
    mounted(){
        this.$store.dispatch('getProceedingRecords')
    },
    computed: {
        proceedingRecords(){
            return this.$store.state.proceedingRecords
        },
        proceedingRecordsList(){
            return this.proceedingRecords.list
        },
        pagination(){
            return this.proceedingRecords.pagination
        },
        isLoading(){
            return this.proceedingRecords.loading
        },
        isEmpty(){
            return this.proceedingRecordsList.length < 1
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
                this.$store.dispatch('getProceedingRecords', this.pagination.prevPageUrl )
        },
        next(){
            if( this.hasNext )
                this.$store.dispatch('getProceedingRecords', this.pagination.nextPageUrl )
        }
    }
}
</script>

<style>

</style>