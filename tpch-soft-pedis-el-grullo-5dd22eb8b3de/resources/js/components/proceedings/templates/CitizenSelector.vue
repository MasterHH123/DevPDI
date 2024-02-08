<template>
    <div class="citizen-selector">
        <ajax-search
            class="w-100"
            @select="(option)=>this.$emit('select', option)"
            :ajax-source="'/citizens'" 
            :template-fn="(citizen)=>`${citizen.first_name} ${citizen.last_name} ${citizen.second_last_name || ''}`"
        />
        <button @click="openModalToAddCitizen">+ Nuevo</button>
        <modal-add-citizen 
            :toggle="isOpenModalAddCitizen"
            @close="isOpenModalAddCitizen = false"
        />
    </div>
</template>

<script>
import ajaxSearch from '../../global/AjaxSearch'
import modalAddCitizen from '../../citizens/ModalAdd'
export default {
    props: {
        selectedCitizen: {

        }
    },
    components: {
        ajaxSearch,
        modalAddCitizen,
    },
    data(){
        return {
            isOpenModalAddCitizen: false,
        }
    },
    methods: {
        openModalToAddCitizen(){
            this.isOpenModalAddCitizen = true
        }
    }
}
</script>

<style scoped lang="scss">
.citizen-selector{
    display: flex;

    button{
        margin: auto;
        margin-left: 1em;
        width: 9em;
    }
}
</style>