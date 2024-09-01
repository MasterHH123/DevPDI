<template>
    <section>
        <div class="row">
            <div class="col-12">
                <view-title/>
                
            </div>
            <dynamic-menu>
                <div class="float-right">
                    <button @click="$router.back()">
                        &larr; Regresar
                    </button>
                    <button 
                        v-if="canPrint && userHasPermissionTo('proceedings.print')"
                        @click="printTrigger = true">
                        🖨️ Imprimir
                    </button>
                </div>
            </dynamic-menu>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <template-loader 
                    @finishPrinting="printTrigger = false"
                    :print-trigger="printTrigger"
                    :template-id="recordToShow.proceeding_template_id"
                    :record-data="recordToShow"
                />
                <loading v-show="isLoading"/>
            </div>
        </div>
    </section>
</template>

<script>
import viewTitle from '../../global/viewTitle'
import dynamicMenu from '../../global/DynamicMenu'
import templateLoader from '../templates/Loader'
import loading from '../../global/Loading'
export default {
    components: {
        viewTitle,
        dynamicMenu,
        templateLoader,
        loading
    },
    computed: {
        templates(){
            return this.$store.state.proceedingTemplates.list
        },
        canPrint(){
            return this.recordToShow.id !== undefined
        },
    },
    mounted(){
        let id = this.$route.params.record_id
        this.getRecordToShow(id)
    },
    data(){
        return {
            printTrigger: false,
            isLoading: false,
            recordToShow: {},
        }
    },
    methods: {
        getRecordToShow(id){
            this.isLoading = true
            this.$store.dispatch('getProceedingRecord', id)
            .then(r=>{
                let res = r.data
                if( res.success ){
                    this.recordToShow = res.data
                }
            })
            .catch(err=>window.onXHRError(err))
            .finally(()=>this.isLoading = false)
        }
    }
}
</script>

<style scoped>
.template-selector{
    max-width: 100%;
}
</style>