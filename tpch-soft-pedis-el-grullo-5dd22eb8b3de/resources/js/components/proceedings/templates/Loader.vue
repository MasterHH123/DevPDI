<template>
    <section>
        <div v-if="templateNotChosen" class="d-flex pt-3">
            <div class="m-auto pt-3 text-center">
                <img src="/img/icons/form.svg" class="icon-xxl d-block ml-auto mr-auto">
                <h4>
                    {{
                        hasRecordData ?
                        'Cargando formato...' :
                        'Selecciona un formato para llenar'
                    }}
                </h4>
            </div>
        </div>
        <div v-else class="sheet" ref="printable">
            <loading v-show="isLoading" />
            <div class="head">
                <h1 class="m-auto text-xxxl">
                    {{ templateToFill.name }}
                </h1>
                <p class="d-block mt-auto text-gray">
                    {{ templateToFill.description }}
                </p>
                <small class="tag" v-if="hasRecordData">
                    {{ `Versión ${recordData.proceeding_template_tag_version}.0` }}
                </small>
                <small v-else class="tag">
                    {{ `Versión ${templateToFill.tag_version}.0` }}
                </small>
            </div>
            <div class="citizen">
                <h4 class="mt-auto mb-1">Ciudadano</h4>
                <div v-if="!hasRecordData && proceeding.id">
                    <router-link
                        tag="p"
                        :to="`/ciudadanos/${proceeding.citizen.id}/editar`"
                        class="tag m-auto animated pointer no-select"
                        title="Editar"
                    >
                        {{ proceeding.citizen.first_name }}
                        {{ proceeding.citizen.last_name }}
                        {{ proceeding.citizen.second_last_name }}
                    </router-link>
                    <small class="tag">
                        Del expediente #{{ proceeding.id }}
                    </small>
                </div>
                <div v-else class="d-flex">
                    <div
                        :style="{backgroundImage: `url(${recordData.citizen.avatar_full_path})`}"
                        class="icon-bg icon-xl round-xl bg-primary-vanished d-block mt-auto mb-auto">
                    </div>
                    <div class="d-block mt-auto mb-auto ml-1">
                        <p class="text-heavy m-auto">
                            {{ recordData.citizen.first_name }}
                            {{ recordData.citizen.last_name }}
                            {{ recordData.citizen.second_last_name }}
                        </p>
                        <small class="d-block mb-1">
                            {{ recordData.citizen.address_street }}
                        </small>
                        <span class="tag">
                            {{ recordData.citizen.age }} años
                        </span>
                    </div>
                </div>
            </div>
            <div class="attachments">
                <h4 class="mt-auto mb-1">Archivos</h4>
                <div v-if="hasRecordData">
                    <file-picker
                        :current-files="attachments"
                        :read-only="!willEdit"
                        @added="onFileAdded"
                        @removed="onFileRemoved"
                        :empty-placeholder="willEdit ?
                            'Arrastra tus archivos o da click aquí para agregar' :
                            'Sin archivos para mostrar'
                        "
                    />
                </div>
                <div v-else>
                    <file-picker
                        :current-files="attachments"
                        @added="onFileAdded"
                        @removed="onFileRemoved"
                    />
                </div>
            </div>
            <div v-if="hasRecordData" class="body read-only">
                <h4 class="mt-auto">General</h4>
                <form
                    class="form-blok"
                    v-if="willEdit"
                    @submit.prevent="updateProceedingRecord">
                    <ul class="field-list m-auto p0">
                        <li
                            v-for="field in templateToFill.fields"
                            :key="field.id"
                            class="field-item">

                            <div
                                v-if="['text', 'number', 'date'].indexOf(field.input_type) !== -1" >
                                <label
                                    title="Editar"
                                    class="pointer no-select">
                                    {{ field.is_required ? '*' : ''}} {{ field.label }}
                                </label>
                                <input
                                    v-once
                                    :value="getRecordDataFieldEntryValue(field)"
                                    :data-field-id="field.id"
                                    :name="`proceeding_template_field_ids[${field.id}]`"
                                    :placeholder="field.placeholder"
                                    :type="field.input_type"
                                    :required="field.is_required"
                                    maxlength="255"
                                >
                            </div>
                            <div
                                v-else-if="field.input_type == 'select'" >
                                <label
                                    title="Editar"
                                    class="pointer no-select">
                                    {{ field.is_required ? '*' : ''}} {{ field.label }}
                                </label>
                                <select
                                    v-once
                                    :value="getRecordDataFieldEntryValue(field)"
                                    :data-field-id="field.id"
                                    :name="`proceeding_template_field_ids[${field.id}]`"
                                    :required="field.is_required">
                                    <option value="">{{ field.placeholder }}</option>
                                    <option v-for="opt in field.option_values" :key="opt">
                                        {{ opt }}
                                    </option>
                                </select>
                            </div>
                            <div
                                v-else-if="field.input_type == 'checkbox'" >
                                <label
                                    class="label-checkbox pointer no-select">
                                    <input
                                        v-once
                                        type="hidden"
                                        value="0"
                                        :name="`proceeding_template_field_ids[${field.id}]`"
                                    >
                                    <input
                                        v-once
                                        :checked="getRecordDataFieldEntryValue(field) == 1"
                                        type="checkbox"
                                        value="1"
                                        :data-field-id="field.id"
                                        :name="`proceeding_template_field_ids[${field.id}]`"
                                        :required="field.is_required"
                                    >
                                    <div>
                                        <span>
                                        {{ field.is_required ? '*' : ''}}
                                        {{ field.label }}
                                    </span>
                                    <small class="d-block text-xs text gray">
                                        {{ field.placeholder }}
                                    </small>
                                    </div>
                                </label>
                            </div>
                        </li>
                        <li>
                            <button type="submit" class="btn-success d-block ml-auto">
                                &check; Actualizar
                            </button>
                        </li>
                    </ul>
                </form>
                <ul v-else class="field-list m-auto p0">
                    <li
                        v-for="field in templateToFill.fields"
                        :key="field.id"
                        class="field-item">

                        <div v-if="getRecordDataFieldEntryValue(field) != null">
                            <div
                                v-if="['text', 'number', 'date', 'select'].indexOf(field.input_type) !== -1 " >
                                <label>
                                    {{ field.is_required ? '*' : ''}} {{ field.label }}
                                </label>
                                <div class="input-value">
                                    <p>
                                        {{ getRecordDataFieldEntryValue(field) }}
                                    </p>
                                </div>
                            </div>
                            <div
                                v-else-if="field.input_type == 'checkbox'" >
                                <label
                                    class="label-checkbox">
                                    <span>
                                        {{ field.is_required ? '*' : ''}}
                                        {{ field.label }}
                                    </span>
                                    <span class="bool-mark" v-if="getRecordDataFieldEntryValue(field) == 1">
                                        SÍ
                                    </span>
                                    <span class="bool-mark" v-else>
                                        NO
                                    </span>
                                </label>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            <div v-else class="body">
                <h4 class="mt-auto">General</h4>
                <form class="form-blok" @submit.prevent="saveProceedingRecord">
                    <ul class="field-list m-auto p0">
                        <li
                            v-for="field in templateToFill.fields"
                            :key="field.id"
                            class="field-item">

                            <div
                                v-if="['text', 'number', 'date'].indexOf(field.input_type) !== -1" >
                                <label
                                    title="Editar"
                                    class="pointer no-select">
                                    {{ field.is_required ? '*' : ''}} {{ field.label }}
                                </label>
                                <input
                                    :data-field-id="field.id"
                                    :name="`proceeding_template_field_ids[${field.id}]`"
                                    :placeholder="field.placeholder"
                                    :type="field.input_type"
                                    :required="field.is_required"
                                    maxlength="255"
                                >
                            </div>
                            <div
                                v-else-if="field.input_type == 'select'" >
                                <label
                                    title="Editar"
                                    class="pointer no-select">
                                    {{ field.is_required ? '*' : ''}} {{ field.label }}
                                </label>
                                <select
                                    :data-field-id="field.id"
                                    :name="`proceeding_template_field_ids[${field.id}]`"
                                    :required="field.is_required">
                                    <option value="">{{ field.placeholder }}</option>
                                    <option v-for="opt in field.option_values" :key="opt">
                                        {{ opt }}
                                    </option>
                                </select>
                            </div>
                            <div
                                v-else-if="field.input_type == 'checkbox'" >
                                <label
                                    class="label-checkbox pointer no-select">
                                    <input
                                        v-once
                                        type="hidden"
                                        value="0"
                                        :name="`proceeding_template_field_ids[${field.id}]`"
                                    >
                                    <input
                                        type="checkbox"
                                        value="1"
                                        :data-field-id="field.id"
                                        :name="`proceeding_template_field_ids[${field.id}]`"
                                        :required="field.is_required">
                                    <div>
                                        <span>
                                        {{ field.is_required ? '*' : ''}}
                                        {{ field.label }}
                                    </span>
                                    <small class="d-block text-xs text gray">
                                        {{ field.placeholder }}
                                    </small>
                                    </div>
                                </label>
                            </div>
                        </li>
                    </ul>
                    <button
                        type="submit"
                        class="w-auto d-block ml-auto btn-success">
                        &check; Guardar
                    </button>
                </form>
            </div>
            <div v-if="hasRecordData" class="tracking">
                <h4 class="mt-auto mb-1">Registrado</h4>
                En
                <span class="tag">
                    {{ recordData.readable_created_at }}
                </span>
                por
                <span class="tag">
                    {{ recordData.user.first_name }}
                    {{ recordData.user.last_name }}
                </span>
            </div>
        </div>
    </section>
</template>

<script>
import loading from '../../global/Loading'
import filePicker from '../../global/FilePicker'
export default {
    components: {
        loading,
        filePicker,
    },
    props: {
        printTrigger: {
            type: Boolean,
            default(){
                return false
            }
        },
        templateId: {},
        recordData: {
            type: Object,
            default(){
                return {}
            }
        },
        willEdit: {
            type: Boolean,
            default(){
                return false
            }
        },
    },
    computed: {
        templateNotChosen(){
            return !this.templateId
        },
        hasRecordData(){
            return this.recordData.id !== undefined
        }
    },
    watch: {
        templateId(id){
            if( id )
                this.getTemplateToFill(id)
        },
        printTrigger(active){
            if( active )
                this.print()
        },
    },
    data(){
        return {
            isLoading: false,
            templateToFill: {},
            proceeding: {},
            attachments: []
        }
    },
    mounted(){
        this.getRelatedProceeding()
    },
    methods: {
        getRelatedProceeding(){
            this.$store.state.proceedings.loading = true
            this.$store.dispatch('getProceeding', {id: this.$route.params.id})
            .then(r=>{
                let res = r.data
                if( res.success)
                    this.proceeding = res.data
            })
            .catch(err=>{
                window.onXHRError(err)
            })
            .finally(()=>{
                this.$store.state.proceedings.loading = false
                this.didLoadForFirstTime = true
            })
        },
        getTemplateToFill(id){
            if( this.hasRecordData ){
                this.attachments = this.recordData.attachments
                return this.templateToFill = this.recordData.original_template
            }
            this.isLoading = true
            this.$store.dispatch('getProceedingTemplate', id)
            .then(r=>{
                let res = r.data
                if( res.success )
                    this.templateToFill = res.data
            })
            .catch(err=>{
                window.onXHRError(err)
            })
            .finally(()=>this.isLoading = false);
        },
        saveProceedingRecord(ev){
            let form = ev.target
            let formData = new FormData(form)
            let proceedingId = this.proceeding.id

            if( !this.proceeding.id )
                return swal.fire('No es posible guardar el registro, el expediente no existe!', '', 'info')

            this.isLoading = true
            formData.append('citizen_id', this.proceeding.citizen.id)
            formData.append('proceeding_id', proceedingId)
            formData.append('proceeding_template_id', this.templateToFill.id)
            formData.append('proceeding_template_tag_version', this.templateToFill.tag_version)

            // Add attachments
            this.attachments.forEach(file=>{
                formData.append('attachments[]', file)
            })

            this.$store.dispatch('saveProceedingRecord', {
                data: formData
            })
            .then(r=>{
                let res = r.data
                if( res.success ){
                    swal.fire(res.message, '', 'success')
                    this.$router.push(`/expedientes/${proceedingId}/ver`)
                }
            })
            .catch(err=>window.onXHRError(err))
            .finally(()=>this.isLoading = false)
        },
        updateProceedingRecord(ev){
            let form = ev.target
            let formData = new FormData(form)
            let proceedingId = this.proceeding.id

            if( !this.proceeding.id )
                return swal.fire('No es posible guardar el registro, el expediente no existe!', '', 'info')

            this.isLoading = true
            formData.append('_method', 'PUT')
            formData.append('citizen_id', this.proceeding.citizen.id)
            formData.append('proceeding_id', proceedingId)
            formData.append('proceeding_template_id', this.templateToFill.id)
            formData.append('proceeding_template_tag_version', this.templateToFill.tag_version)

            // Add attachments
            this.attachments.forEach(file=>{
                if( file instanceof File )
                    formData.append('attachments[]', file)
            })

            // Existing attachments
            formData.append('existing_attachments', JSON.stringify(
                this.attachments.filter(f=>f.id!=undefined)
            ))

            this.$store.dispatch('saveProceedingRecord', {
                id: this.recordData.id,
                data: formData,
                isUpdate: true,
            })
            .then(r=>{
                let res = r.data
                if( res.success ){
                    swal.fire(res.message, '', 'success')
                    this.$router.push(`/expedientes/${proceedingId}/ver`)
                }
            })
            .catch(err=>window.onXHRError(err))
            .finally(()=>this.isLoading = false)
        },
        getRecordDataFieldEntryValue(field){
            let entry = this.recordData.field_entries.find(e=>{
                return e.proceeding_template_field_id == field.id
            })

            return entry ? entry.field_value : null
        },
        print(){
            const htmlToPrint = this.$refs.printable.outerHTML;

            // Get all stylesheets HTML
            let stylesHtml = '';
            for (const node of [...document.querySelectorAll('link[rel="stylesheet"], style')])
                stylesHtml += node.outerHTML;

            // Open the print window
            const printWindow = window.open('', '', 'left=0,top=0,width=800,height=900,toolbar=0,scrollbars=0,status=0');

            printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>PDIS - Registro No.${this.recordData.id}</title>
                ${stylesHtml}
                <style>
                    @media print {
                        body {-webkit-print-color-adjust: exact;}
                    }
                    .sheet{
                        width: 100%!important;
                        max-width: 100%!important;
                        padding: 3em!important;
                        margin: auto!important;
                        box-shadow: none!important;
                    }
                    .sheet:after,
                    .sheet:before{
                        content: none;
                    }
                </style>
            </head>
            <body>
                ${htmlToPrint}
            </body>
            </html>`);

            printWindow.document.close();
            printWindow.focus();

            printWindow.onload = ()=>{
                printWindow.print();
                setTimeout(()=>{
                    printWindow.close();
                }, 100)
            }

            this.$emit('finishPrinting', true)
        },
        // Files
        onFileAdded(file){
            if( this.fileExists(file) )
                return toast.fire('Ya agregaste ese archivo!', '', 'info')

            this.attachments.push(file)
        },
        onFileRemoved(file){
            this.attachments = this.attachments.filter(f=>{
                if( file.id )
                    return f.id != file.id
                else
                    return f.size != file.size &&
                        f.lastModified != file.lastModified
            })
        },
        fileExists(file){
            return this.attachments.find(f=>{
                return f.lastModified == file.lastModified &&
                    f.size == file.size
            }) !== undefined
        },
    }
}
</script>

<style scoped lang="scss">
.sheet{
    width: 90%;
    max-width: 100%;
    padding: 6em;

    .read-only [disabled],
    .read-only [readonly]{
        opacity: 1;
        background: var(--primary-vanished);
        border: solid 2px;
    }

    .bool-mark{
        font-size: var(--font-size-xxl);
        line-height: 0;
        color: var(--primary-dark);
    }

    .input-value{
        padding: 12px;
        background: var(--primary-vanished);
        border: solid 2px var(--primary);
        border-radius: 12px;
        color: var(--primary);

        p{
            margin: 0;
            text-align: justify;
        }
    }
}
</style>
