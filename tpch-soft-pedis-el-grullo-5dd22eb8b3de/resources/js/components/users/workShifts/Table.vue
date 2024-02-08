<template>
    <div class="table-container">
        <empty-message v-show="isEmpty"/>
        <loading v-show="isLoading"/>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Folio</th>
                        <th>Nombre</th>
                        <th>*</th>
                    </tr>
                </thead>
                <tbody>
                    <tr 
                        v-for="workShift in workShiftsList" 
                        :key="workShift.id">
                        <td data-label="Folio">
                            <span class="tag">
                                {{ workShift.id }}
                            </span>
                        </td>
                        <td data-label="Nombre">
                            <span class="text-heavy">
                                {{ workShift.name }}
                            </span>
                        </td>
                        <td data-label="*">
                            <div class="text-center">
                                <button 
                                    @click.prevent="$store.dispatch('updateWorkShift', workShift)" 
                                    class="btn-sm btn-info">
                                    Renombrar
                                </button>
                                &nbsp;
                                <button 
                                    @click.prevent="$store.dispatch('deleteWorkShift', workShift)" 
                                    class="btn-sm btn-danger">
                                    Eliminar
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
        this.$store.dispatch('getWorkShifts', {paginate:1})
    },
    computed: {
        workShifts(){
            return this.$store.state.workShifts
        },
        workShiftsList(){
            return this.workShifts.list
        },
        pagination(){
            return this.workShifts.pagination
        },
        isLoading(){
            return this.workShifts.loading
        },
        isEmpty(){
            return this.workShiftsList.length < 1
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
                    'getWorkShifts', 
                    {
                        url: this.pagination.prevPageUrl,
                        paginate: 1,
                    }
                )
        },
        next(){
            if( this.hasNext )
                this.$store.dispatch(
                    'getWorkShifts', 
                    {
                        url: this.pagination.nextPageUrl,
                        paginate: 1,
                    }
                )
        }
    }
}
</script>

<style>

</style>