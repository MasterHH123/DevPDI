 <template>
    <div class="table-container">
        <empty-message v-show="isEmpty"/>
        <loading v-show="isLoading"/>
        <div 
            class="row table-wrapper overflow-x-hidden px-2 pt-2 pb-2 bg-dimmed" 
            v-if="showAsTiles">
            <div 
                class="col-3" 
                v-for="template in proceedingTemplatesList" 
                :class="{inactive: !template.is_active}"
                :key="template.id">
                <router-link 
                    v-if="userHasPermissionTo('proceeding_templates.update')"
                    tag="div" 
                    :to="`/expedientes/formatos/${template.id}/editar`"  
                    class="sheet">
                    <div class="head">
                        <small class="text-xs">
                            {{ `Versión ${template.tag_version}.0` }}
                        </small>
                        <h1 class="m-auto text-truncated-2-lines" :title="template.name">
                            {{ template.name }}
                        </h1>
                        <p class="mt-auto text-truncated-2-lines" :title="template.description">
                            {{ template.description }}
                        </p>
                        <small class="text-dark text-truncated">
                            Creado {{ template.readable_created_at }}
                        </small>
                        <span class="d-block mt-1 tag text-center">
                            {{ template.fields_count }} preguntas
                        </span>
                        <div class="actions" v-if="userHasPermissionTo('proceeding_templates.archive')">
                            <button 
                                v-if="template.is_active"
                                @click.prevent="$store.dispatch('disableProceedingTemplate', template)" 
                                class="btn-sm btn-warning">
                                Desactivar
                            </button>
                            <button 
                                v-else
                                @click.prevent="$store.dispatch('enableProceedingTemplate', template)" 
                                class="btn-sm btn-success">
                                Activar
                            </button>
                        </div>
                    </div>
                </router-link>
                <div class="sheet" v-else>
                    <div class="head">
                        <small class="text-xs">
                            {{ `Versión ${template.tag_version}.0` }}
                        </small>
                        <h1 class="m-auto text-truncated-2-lines" :title="template.name">
                            {{ template.name }}
                        </h1>
                        <p class="mt-auto text-truncated-2-lines" :title="template.description">
                            {{ template.description }}
                        </p>
                        <small class="text-dark text-truncated">
                            Creado {{ template.readable_created_at }}
                        </small>
                        <span class="d-block mt-1 tag text-center">
                            {{ template.fields_count }} preguntas
                        </span>
                        <div class="actions" v-if="userHasPermissionTo('proceeding_templates.archive')">
                            <button 
                                v-if="template.is_active"
                                @click.prevent="$store.dispatch('disableProceedingTemplate', template)" 
                                class="btn-sm btn-warning">
                                Desactivar
                            </button>
                            <button 
                                v-else
                                @click.prevent="$store.dispatch('enableProceedingTemplate', template)" 
                                class="btn-sm btn-success">
                                Activar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else class="table-wrapper">
            <table >
                <thead>
                    <tr>
                        <th>Folio</th>
                        <th>Versión</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Turno</th>
                        <th>Creado</th>
                        <th>*</th>
                    </tr>
                </thead>
                <tbody>
                    <tr 
                        v-for="template in proceedingTemplatesList" 
                        :class="{inactive: !template.is_active}"
                        :key="template.id">
                        <td data-label="Folio">
                            <span class="tag">
                                {{ template.id }}
                            </span>
                        </td>
                        <td data-label="Versión">
                            <span class="tag">
                                {{ `${template.tag_version}.0` }}
                            </span>
                        </td>
                        <td data-label="Nombre">
                            <span class="text-heavy">
                                {{ template.name }}
                            </span>
                        </td>
                        <td data-label="Descripción">
                            <small>
                                {{ template.description }}
                            </small>
                        </td>
                        <td data-label="Turno">
                            <small class="tag" v-if="template.work_shift">
                                {{ template.work_shift.name }}
                            </small>
                        </td>
                        <td data-label="Creado">
                            <span class="tag" :title="template.created_at.substr(0, 19)">
                                {{ template.readable_created_at }}
                            </span>
                        </td>
                        <td data-label="*">
                            <div class="d-flex">
                                <router-link 
                                    v-if="template.is_active && userHasPermissionTo('proceeding_templates.update')"
                                    :to="`/expedientes/formatos/${template.id}/editar`" 
                                    tag="button" 
                                    class="btn-sm btn-info">
                                    Editar
                                </router-link>
                                &nbsp;
                                <button 
                                    v-if="template.is_active && userHasPermissionTo('proceeding_templates.archive')"
                                    @click.prevent="$store.dispatch('disableProceedingTemplate', template)" 
                                    class="btn-sm btn-warning">
                                    Desactivar
                                </button>
                                <button 
                                    v-else-if="userHasPermissionTo('proceeding_templates.archive')"
                                    @click.prevent="$store.dispatch('enableProceedingTemplate', template)" 
                                    class="btn-sm btn-success">
                                    Activar
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
        this.$store.dispatch('getProceedingTemplates')
    },
    computed: {
        proceedingTemplates(){
            return this.$store.state.proceedingTemplates
        },
        showAsTiles(){
            return this.proceedingTemplates.showAsTiles
        },
        proceedingTemplatesList(){
            return this.proceedingTemplates.list
        },
        pagination(){
            return this.proceedingTemplates.pagination
        },
        isLoading(){
            return this.proceedingTemplates.loading
        },
        isEmpty(){
            return this.proceedingTemplatesList.length < 1
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
                this.$store.dispatch(
                    'getProceedingTemplates', 
                    {url: this.pagination.prevPageUrl}
                )
        },
        next(){
            if( this.hasNext )
                this.$store.dispatch(
                    'getProceedingTemplates', 
                    {url: this.pagination.nextPageUrl}
                )
        }
    }
}
</script>

<style scoped>
.col-3.inactive{
    opacity: 0.5;
}
.sheet{
    min-height: auto;
    min-width: auto;
    width: 12em;
    height: 16em;
    padding: 12px 16px;
    cursor: pointer;
    transition: all 0.1s;
    background: var(--dimmed);
    color: var(--primary-dark);
}
.sheet:hover{
    transform: scale3d(1.05, 1.05, 1.05);
    box-shadow: 0 6px 24px rgba(0,0,0,0.2);
    background: var(--primary-dimmed);
}
.sheet .actions{
    position: absolute;
    top: -24px;
    left: -24px;
}
</style>