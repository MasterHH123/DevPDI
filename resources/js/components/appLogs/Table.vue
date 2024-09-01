<template>
    <div class="table-container">
        <empty-message v-show="isEmpty"/>
        <loading v-show="isLoading"/>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Folio</th>
                        <th>Usuario</th>
                        <th>Descripción</th>
                        <th>Fecha</th>
                        <th>*</th>
                    </tr>
                </thead>
                <tbody>
                    <tr 
                        v-for="entry in appLogsList" 
                        :key="entry.id">
                        <td data-label="Folio">
                            <span class="tag">
                                {{ entry.id }}
                            </span>
                        </td>
                        <td data-label="Usuario">
                            <span class="tag" v-if="entry.related_user">
                                {{ entry.related_user.first_name }}
                                {{ entry.related_user.last_name }}
                            </span>
                            <span class="tag" v-else>
                                -
                            </span>
                        </td>
                        <td data-label="Descripción">
                            <small>
                                {{ entry.description }}
                            </small>
                        </td>
                        <td data-label="Fecha">
                            <span class="tag">
                                {{ entry.readable_created_at }}
                            </span>
                        </td>
                        <td data-label="*">
                            <div class="d-flex">
                                <button 
                                    v-if="entry.payload"
                                    @click.prevent="openDetails(entry)" 
                                    class="btn-sm">
                                    Metadatos
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
        this.$store.dispatch('getAppLogs')
    },
    computed: {
        appLogs(){
            return this.$store.state.appLogs
        },
        appLogsList(){
            return this.appLogs.list
        },
        pagination(){
            return this.appLogs.pagination
        },
        isLoading(){
            return this.appLogs.loading
        },
        isEmpty(){
            return this.appLogsList.length < 1
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
                    'getAppLogs', 
                    {url: this.pagination.prevPageUrl}
                )
        },
        next(){
            if( this.hasNext )
                this.$store.dispatch(
                    'getAppLogs', 
                    {url: this.pagination.nextPageUrl}
                )
        },
        openDetails(entry){
            swal.fire({
                title: `Metadatos del movimiento #${entry.id}`,
                html: `
                    <pre class="text-left bg-dimmed" style="max-height:50vh;overflow:auto">
                        ${JSON.stringify(entry.payload, undefined, 2)}
                    </pre>
                `
            })
        }
    }
}
</script>

<style>

</style>