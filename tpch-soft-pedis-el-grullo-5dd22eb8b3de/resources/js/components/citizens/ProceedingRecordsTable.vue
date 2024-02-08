<template>
    <div class="table-container">
        <empty-message v-show="isEmpty"/>
        <loading v-show="isLoading"/>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Folio</th>
                        <th>Formato</th>
                        <th>Aplicado por</th>
                        <th>Fecha de captura</th>
                        <th>*</th>
                    </tr>
                </thead>
                <tbody>
                    <tr 
                        v-for="record in entriesList" 
                        :key="record.id" 
                        :class="{inactive: !record.is_active}">
                        <td data-label="Folio">
                            <span class="tag">
                                {{ record.id }}
                            </span>
                        </td>
                        <td data-label="Formato">
                            <b>
                                {{ record.proceeding_template.name }}
                            </b>
                            <small class="d-block text-gray">
                                {{ `Versión ${record.proceeding_template_tag_version}.0` }}
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
                                    :to="`/expedientes/${relatedProceeding.id}/registros/${record.id}/ver`" 
                                    tag="button" 
                                    class="btn-sm">
                                    Ver
                                </router-link>
                                &nbsp;
                                <router-link 
                                    v-if="userHasPermissionTo('proceedings.edit')"
                                    :to="`/expedientes/${relatedProceeding.id}/registros/${record.id}/editar`" 
                                    tag="button" 
                                    class="btn-sm btn-info">
                                    Editar
                                </router-link>
                                &nbsp;
                                <button 
                                    v-if="record.is_active && userHasPermissionTo('proceedings.archive')"
                                    @click.prevent="$emit('toggleArchive',record)" 
                                    class="btn-sm btn-warning">
                                    Archivar
                                </button>
                                <button 
                                    v-else-if="userHasPermissionTo('proceedings.archive')"
                                    @click.prevent="$emit('toggleArchive',record)" 
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
                Mostrando registros del {{ entries.from }} al {{ entries.to}}
            </small>
        </div>
    </div>
</template>

<script>
import loading from '../global/Loading'
import emptyMessage from '../global/Empty'
export default {
    props: {
        isLoading: {
            type: Boolean,
            required: true,
        },
        entries: {
            type: Object,
            required: true,
        },
        relatedProceeding: {
            type: Object,
            required: true,
        }
    },
    components: {
        loading,
        emptyMessage,
    },
    computed: {
        entriesList(){
            return this.entries && this.entries.data ? this.entries.data : []
        },
        isEmpty(){
            return this.entriesList.length < 1
        },
        hasPrev(){
            return this.entries.prev_page_url != null
        },
        hasNext(){
            return this.entries.next_page_url != null
        },
    },
    methods: {
        prev(){
            if( this.hasPrev )
                this.$emit('goPrev', this.entries.prev_page_url)
        },
        next(){
            if( this.hasNext )
                this.$emit('goNext', this.entries.next_page_url)
        },
    }
}
</script>

<style>

</style>