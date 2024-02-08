<template>
    <section>
        <div class="row">
            <div class="col-12">
                <view-title/>
            </div>
            <dynamic-menu>
                <div class="float-right">
                    <select class="template-selector" v-model="templateId">
                        <option value="">Selecciona un formato</option>
                        <option 
                            :value="tpl.id"
                            v-for="tpl in templates" 
                            :key="tpl.id">
                            {{ tpl.name }}
                        </option>
                    </select>
                    <button @click="$router.back()" class="ml-1">
                        &larr; Regresar
                    </button>
                </div>
            </dynamic-menu>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <template-loader :template-id="templateId"/>
            </div>
        </div>
    </section>
</template>

<script>
import viewTitle from '../../global/viewTitle'
import dynamicMenu from '../../global/DynamicMenu'
import templateLoader from '../templates/Loader'
export default {
    components: {
        viewTitle,
        dynamicMenu,
        templateLoader,
    },
    computed: {
        templates(){
            return this.$store.state.proceedingTemplates.list
        }
    },
    mounted(){
        this.$store.dispatch('getProceedingTemplates', 
        {
            paginate: 0, 
            onlyActive: 1
        })
    },
    data(){
        return {
            templateId: '',
        }
    },
}
</script>

<style scoped>
.template-selector{
    max-width: 100%;
}
</style>