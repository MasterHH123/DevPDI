<template>
    <section>
        <div class="row">
            <div class="col-12">
                <view-title/>       
            </div>
            <dynamic-menu>
                <div class="float-right">
                    <router-link 
                        v-if="userHasPermissionTo('proceedings.edit')"
                        :to="`/expedientes/${proceeding.id}/editar`" 
                        tag="button" 
                        class="btn-info">
                        Editar Carpeta
                    </router-link>
                    <router-link 
                        v-if="!proceeding.is_closed" 
                        :to="`/expedientes/${proceeding.id}/registros/nuevo`" 
                        tag="button">
                        + Agregar registro
                    </router-link>
                    <router-link to="/expedientes" tag="button">
                        &larr; Regresar
                    </router-link>
                </div>
            </dynamic-menu>
        </div>
        <div class="row">
            <div class="col-6">
                <div class="card">
                    <loading v-show="isLoading && !didLoadForFirstTime" />
                    <div class="d-flex">
                        <div 
                            class="icon-bg icon-xl bg-primary-vanished round-xl d-block mt-auto mb-auto mr-2" 
                            :style="{backgroundImage: 'url(/img/icons/folder.svg)'}"></div>
                        <div class="mt-auto mb-auto mr-1">
                            <p class="text-xl text-heavy m-auto">
                                Folio {{ proceeding.id }}
                            </p>
                            <small class="d-block text-truncated-3-lines">
                                {{ proceeding.name }}
                            </small>
                            <small class="d-block mb-1 text-gray text-truncated-3-lines">
                                {{ proceeding.description }}
                            </small>
                            <span class="tag">
                                {{ proceeding.readable_created_at }}
                            </span>
                            <span class="badge">
                                {{ proceeding.status_badge }}
                                {{ proceeding.status }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card" v-if="proceeding.citizen">
                    <loading v-show="isLoading && !didLoadForFirstTime" />
                    <div class="d-flex">
                        <div 
                            class="icon-bg icon-xl bg-primary-vanished round-xl d-block mt-auto mb-auto mr-2" 
                            :style="{backgroundImage: `url(${proceeding.citizen.avatar_full_path})`}"></div>
                        <div class="mt-auto mb-auto mr-1">
                            <p class="text-xl text-heavy m-auto">
                                {{ proceeding.citizen.first_name }}
                                {{ proceeding.citizen.last_name }}
                                {{ proceeding.citizen.second_last_name }}
                            </p>
                            <small class="d-block mb-1">{{ proceeding.citizen.address_street }}</small>
                            <span class="tag">
                                {{ proceeding.citizen.age }} años
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">             
                <div class="card">
                    <h4>Registros del expediente #{{ proceeding.id }}</h4>
                    <citizen-proceeding-records-table 
                        @goPrev="(url)=>getProceedingToShow(null, url)"
                        @goNext="(url)=>getProceedingToShow(null, url)"
                        @search="searchProceedingRecords"
                        @toggleArchive="toggleArchivation"
                        :is-loading="isLoading"
                        :entries="proceeding.records || {}"
                        :related-proceeding="proceeding"
                    />
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import viewTitle from '../global/viewTitle'
import dynamicMenu from '../global/DynamicMenu'
import loading from '../global/Loading'
import citizenProceedingRecordsTable from '../citizens/ProceedingRecordsTable'
export default {
    components: {
        viewTitle,
        dynamicMenu,
        loading,
        citizenProceedingRecordsTable
    },
    mounted(){
        let id = this.$route.params.id
        this.getProceedingToShow(id)
    },
    data(){
        return {
            printTrigger: false,
            didLoadForFirstTime: false,
            proceeding: {},
        }
    },
    computed: {
        isLoading(){
            return this.$store.state.proceedings.loading
        }
    },
    methods: {
        getProceedingToShow(id=null, url=null, term=null){
            this.$store.state.proceedings.loading = true
            this.$store.dispatch('getProceeding', {
                id, 
                url,
                params: { 
                    term,
                    with_proceeding_records: 1
                }
            })
            .then(r=>{
                let res = r.data
                if( res.success){
                    this.proceeding = res.data
                }
            })
            .catch(err=>{
                window.onXHRError(err)
            })
            .finally(()=>{
                this.$store.state.proceedings.loading = false
                this.didLoadForFirstTime = true
            })
        },
        searchProceedingRecords(term){
            if( this.__searchDebouncer )
                clearTimeout( this.__searchDebouncer )

            this.__searchDebouncer = setTimeout(()=>{
                this.getProceedingToShow( this.$route.params.id, null, term)
            }, 300)
        },
        toggleArchivation(record){
            let id = this.$route.params.id
            
            record.callbackFn = ()=>{
                this.getProceedingToShow(id)
            }

            if( record.is_active )
                this.$store.dispatch('archiveProceedingRecord', record)
            else
                this.$store.dispatch('restoreProceedingRecord', record)
        }
    }
}
</script>

<style scoped>
.template-selector{
    max-width: 100%;
}
.badge{
    top: -10px;
    left: 0; 
}
</style>