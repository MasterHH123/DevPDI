<template>
    <div class="builder">
        <loading v-show="isLoading"/>
        <select v-model="template.work_shift_id" class="ml-auto d-block mb-1">
            <option value="">Turno</option>
            <option v-for="ws in workShifts" :key="ws.id" :value="ws.id">
                {{ ws.name }}
            </option>
        </select>
        <div class="d-flex" v-if="template.name">
            <div class="sidebar">
                <div class="head text-left">
                    <h4 class="m-auto">Elementos</h4>
                    <small class="text-gray">
                        Arrastra alguno &rarr;
                    </small>
                </div>
                <div class="input-list-selector">
                    <draggable 
                        tag="ul"
                        :list="inputFieldElements"
                        :sort="false"
                        :clone="cloneInputField"
                        :group="{ 
                            name: 'proceeding-template', 
                            pull: 'clone', 
                            put: false 
                        }"
                        class="list-unstyled p0">
                        <li v-for="inputElement in inputFieldElements" :key="inputElement.id">
                            <div class="d-flex input-item">
                                <img :src="inputElement.icon" class="icon-lg">
                                <p>{{ inputElement.name }}</p>
                            </div>
                        </li>
                    </draggable>
                </div>
            </div>
            <div class="preview">
                <div class="sheet">
                    <div class="head">
                        <h1 class="mt-auto" 
                            @focus="updateTemplateMeta('name', $event)"
                            @blur="updateTemplateMeta('name', $event)" 
                            contenteditable>
                            {{ template.name }}
                        </h1>
                        <p class="mt-auto text-gray" 
                            @focus="updateTemplateMeta('description', $event)" 
                            @blur="updateTemplateMeta('description', $event)" 
                            contenteditable>
                            {{ template.description }}
                        </p>
                    </div>
                    <div class="mt-1 text-left bg-yellow-light d-flex">
                        <h1 class="mt-auto mb-auto">⚠️</h1>
                        <small class="ml-1 d-block mt-auto mb-auto">
                            Los campos para capturar los datos personales del entrevistado/victima se cargan automáticamente y no es necesario agregarlos.
                        </small>
                    </div>
                    <draggable 
                        :list="proceedingTemplateFields"
                        tag="ul"
                        handle=".handle"
                        group="proceeding-template" 
                        class="field-list">
                        <li v-for="field in proceedingTemplateFields" :key="field.id">
                            <div 
                                class="field-item"
                                :class="{editing: isSelectedFieldToEdit(field.id)}"
                                v-if="['text', 'number', 'date'].indexOf(field.input_type) !== -1" >
                                <label
                                    title="Editar"
                                    class="pointer no-select"
                                    @click="editField(field)">
                                    {{ field.is_required ? '*' : ''}} {{ field.label }}
                                </label>
                                <input :placeholder="field.placeholder" :type="field.input_type">
                            </div>
                            <div 
                                class="field-item"
                                :class="{editing: isSelectedFieldToEdit(field.id)}"
                                v-else-if="field.input_type == 'select'" >
                                <label
                                    title="Editar"
                                    class="pointer no-select"
                                    @click="editField(field)">
                                    {{ field.is_required ? '*' : ''}} {{ field.label }}
                                </label>
                                <select>
                                    <option value="">{{ field.placeholder }}</option>
                                    <option v-for="opt in field.option_values" :key="opt">
                                        {{ opt }}
                                    </option>
                                </select>
                            </div>
                            <div 
                                class="field-item"
                                :class="{editing: isSelectedFieldToEdit(field.id)}"
                                v-else-if="field.input_type == 'checkbox'" >
                                <label
                                    title="Editar"
                                    @click="editField(field)" 
                                    class="label-checkbox pointer no-select">
                                    <input type="checkbox">
                                    <span>
                                        {{ field.is_required ? '*' : ''}} 
                                        {{ field.label }}
                                        <small class="d-block text-xs text-gray">
                                            {{ field.placeholder }}
                                        </small>
                                    </span>
                                </label>
                            </div>
                            <div class="field-edit-tools">
                                <button class="btn-sm btn-neutral handle" title="Sostén para mover arriba o abajo">&#8597;</button>
                                <button class="btn-sm btn-warning" @click="editField(field)">Editar</button>
                                <button class="btn-sm btn-danger" @click="removeField(field)">Quitar</button>
                            </div>
                        </li>
                        <li v-if="emptyTemplate">
                            <div class="text-center text-gray px-3 pt-3 pb-3">
                                <img src="/img/icons/empty.svg" class="icon-lg">
                                <p class="text-heavy m-auto">¡Formato vacio!</p>
                                <small>&larr; Arrastra aquí algún elemento</small>
                            </div>
                        </li>
                    </draggable>
                </div>
            </div>
            <div class="field-properties-editor" :class="{show: isFieldPropertiesEditorOpen}">
                <div class="head">
                    <img :src="fieldPropertiesToEdit.icon" class="icon-lg">
                    <h4 class="m-auto">
                        <span class="d-block">Propiedades</span>
                        <small class="d-block text-gray text-sm">
                            {{ fieldPropertiesToEdit.label }}
                        </small>
                    </h4>
                </div>
                <div class="properties">
                    <form class="form-block">
                        <label>Etiqueta</label>
                        <input type="text" v-model="fieldPropertiesToEdit.label" maxlength="199">
                        <label>Texto de ayuda</label>
                        <input type="text" v-model="fieldPropertiesToEdit.placeholder" maxlength="199">
                        <div v-if="fieldPropertiesToEdit.input_type == 'select'">
                            <label class="no-select">
                                <button 
                                    @click.prevent="addFieldListOption"
                                    class="btn-sm w-auto float-right m0" 
                                    title="Agregar">
                                +
                                </button>
                                <span>Opciones de la lista</span>
                            </label>
                            <div class="field-list-options">
                                <span 
                                    v-for="opt in fieldPropertiesToEdit.option_values" 
                                    :key="opt" 
                                    class="field-option">
                                    {{opt}}
                                    &nbsp;
                                    <b 
                                        class="ml-05 pointer" 
                                        title="Quitar" 
                                        @click="removeFieldListOption(opt)">
                                        x
                                    </b>
                                </span>
                                <small
                                    class="text-gray d-block pt-2 text-center" 
                                    v-if="fieldPropertiesToEdit.option_values.length < 1">
                                    Vacio!
                                </small>
                            </div>
                        </div>
                        <label class="no-select pointer">
                            <input type="checkbox" v-model="fieldPropertiesToEdit.is_required">
                            <span>
                                ¿Es obligatorio?
                            </span>
                        </label>
                        <button 
                            @click.prevent="isFieldPropertiesEditorOpen = false" 
                            class="btn-success w-100">
                            Listo
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="row mt-1" style="height: 44px">
            <div class="float-right">
                <transition name="fade">
                    <button
                        v-show="!isFieldPropertiesEditorOpen && didChangeFromOriginal" 
                        class="btn-neutral"
                        @click.prevent="restoreOriginalTemplate">
                        Dejar como estaba
                    </button>
                </transition>
                <transition name="fade">
                    <button 
                        v-show="!isFieldPropertiesEditorOpen && hasUnsavedChanges"
                        class="btn-success" 
                        @click.prevent="saveProceedingTemplate">
                        &check; Guardar
                    </button>
                </transition>
            </div>
        </div>
    </div>
</template>

<script>
import draggable from 'vuedraggable'
import loading from '../../global/Loading.vue'
export default {
    components: {
        draggable,
        loading,
    },
    props: {
        templateToEdit: {
            type: Object,
            default(){
                return {
                    name: 'Título',
                    description: 'Descripción',
                    work_shift_id: '',
                    fields: []
                }
            }
        }
    },
    mounted(){
        this.$store.dispatch('getWorkShifts', {paginate:0})
    },
    watch: {
        templateToEdit(v){
            // Load up template
            this.template.id                        = v.id
            this.template.name                      = v.name
            this.template.description               = v.description
            this.template.work_shift_id             = v.work_shift_id
            this.template.proceedingTemplateFields  = v.fields

            // Create a copy
            this.originalTemplate = JSON.stringify(this.template)
        },
        template: {
            deep: true,
            handler(modification){
                this.editLog.push(modification)
                this.didSaveChanges = false
            }
        },
        isFieldPropertiesEditorOpen(isIt){
            if( !isIt )
                setTimeout(()=>this.fieldPropertiesToEdit = {}, 150)
        }
    },
    computed: {
        proceedingTemplateFields(){
            return this.template.proceedingTemplateFields
        },
        emptyTemplate(){
            return this.proceedingTemplateFields.length < 1
        },
        isLoading(){
            return this.$store.state.proceedingTemplates.loading
        },
        didChangeFromOriginal(){
            return this.editLog.length > 1 && this.originalTemplate !== null
        },
        hasUnsavedChanges(){
            let isEditing = this.template.id !== undefined

            return isEditing ? (
                this.didChangeFromOriginal &&
                !this.didSaveChanges
            )
            : (
                this.editLog.length > 0 && 
                !this.emptyTemplate && 
                !this.didSaveChanges
            )
        },
        workShifts(){
            return this.$store.state.workShifts.list
        },
    },
    data(){
        return {
            originalTemplate: null,
            editLog: [],
            isFieldPropertiesEditorOpen: false,
            didSaveChanges: false,
            inputFieldElements: [
                {
                    id: 'text',
                    icon: '/img/icons/html_text.svg',
                    name: 'Caja de texto',
                    type: 'text',
                },
                {
                    id: 'select',
                    icon: '/img/icons/html_select.svg',
                    name: 'Lista desplegable',
                    type: 'select',
                },
                {
                    id: 'number',
                    icon: '/img/icons/html_number.svg',
                    name: 'Caja númerica',
                    type: 'number',
                },
                {
                    id: 'calendar',
                    icon: '/img/icons/html_calendar.svg',
                    name: 'Selector de fecha',
                    type: 'date',
                },
                {
                    id: 'checkbox',
                    icon: '/img/icons/html_checkbox.svg',
                    name: 'Check boleano',
                    type: 'checkbox',
                },
                /*{
                    id: 'multi_select',
                    icon: '/img/icons/html_selection.svg',
                    name: 'Selección multiple',
                    type: 'multiselect',
                },*/
            ],
            template: {
                name: this.templateToEdit.name,
                description: this.templateToEdit.description,
                work_shift_id: this.templateToEdit.work_shift_id,
                proceedingTemplateFields: this.templateToEdit.fields,
            },
            fieldPropertiesToEdit: {},
        }
    },
    methods: {
        cloneInputField(clonedElement){
            let inmutable = JSON.parse(
                JSON.stringify( clonedElement )
            )

            return {
                id: `field-${window.getRandString()}`, 
                icon: inmutable.icon,
                label: inmutable.name,
                description: null,
                placeholder: null,
                input_type: inmutable.type,
                is_required: true,
                is_disabled: false,
                is_read_only: false,
                max_value: null,
                min_value: null,
                def_value: null,
                option_values: [],
                ajax_source: null,
            }
        },
        updateTemplateMeta(subject, ev){
            let content = ev.target.innerText
            .replace(/(?:^(?:&nbsp;)+)|(?:(?:&nbsp;)+$)/g, '')

            // Fix: if content is '', screen disapears [WTF]
            // so we just put a '-' instead of empty string
            //this.template[subject] = content
            this.template[subject] = content.length < 1 ? '-' : content
        },
        editField(field){
            this.fieldPropertiesToEdit = field
            this.isFieldPropertiesEditorOpen = true
        },
        removeField(field){
            let rmFn = ()=>{
                this.template.proceedingTemplateFields = 
                this.proceedingTemplateFields.filter(f=>
                    f.id !== field.id
                )
            }

            confirm.fire({
                title: `¿Quitar el campo "${field.label}" ?`,
                confirmButtonText: 'Si',
            }).then(r=>{
                if(r.isConfirmed)
                    rmFn()
            })
        },
        addFieldListOption(){
            swal.fire({
                title: 'Agregar opción a la lista',
                input: 'text',
                inputValidator: (v)=>{
                    if( this.fieldPropertiesToEdit.option_values.indexOf(v) !== -1)
                        return `Ya agregaste la opcion "${v}" !`
                    return !v && 'Escribe la opción'
                },
                confirmButtonText: 'Agregar',
                showCancelButton: true,
            }).then(r=>{
                if( r.value )
                    this.fieldPropertiesToEdit
                    .option_values.push(r.value)
            })
        },
        removeFieldListOption(opt){
            this.fieldPropertiesToEdit.option_values =
            this.fieldPropertiesToEdit
            .option_values.filter(o=>o!==opt) 
        },
        saveProceedingTemplate(){
            if( this.emptyTemplate )
                return toast.fire('Agrega al menos un campo al formato!', '', 'info')

            if( !this.template.work_shift_id )
                return toast.fire('Olvidaste definir el turno!', '', 'info')

            let isUpdate = typeof this.template.id == 'number'
            this.$store.state.proceedingTemplates.loading = true
            this.$store.dispatch('saveProceedingTemplate', {data: this.template, isUpdate})
            .then(r=>{
                let res = r.data
                if( res.success){
                    swal.fire(res.message, '', 'success')
                    this.didSaveChanges = true
                    this.$router.push('/expedientes/formatos')
                }
            })
            .catch(err=>{
                window.onXHRError(err)
            })
            .finally(()=>this.$store.state.proceedingTemplates.loading = false)
        },
        isSelectedFieldToEdit(id){
            return this.fieldPropertiesToEdit.id === id
        },
        restoreOriginalTemplate(){
            this.editLog = []
            this.template = JSON.parse( this.originalTemplate )
        }
    },
}
</script>

<style lang="scss" scoped>
.builder{
    min-height: 45vh;

    .sidebar{
        min-width: 180px;
        padding: 0 0.5em;
        overflow: auto;
        height: 100%;
        position: relative;

        ul li{
            margin: 0.5em 0;
        }

        .input-item{
            transition: 0.05s all;
            user-select: none;
            cursor: pointer;
            background: var(--primary-vanished);
            border-radius: 6px;
            padding: 12px 10px;

            img, p{
                display: block;
                margin-top: auto;
                margin-bottom: auto;
            }
            p{
                margin-left: 1em;
                width: 100%;
            }
        }

        .input-item:hover{
            background: var(--primary);
            color: var(--white);
        }
    }

    .preview{
        width: 80%;
        background: var(--background);
        display: flex;
        position: relative;
        height: 36em;
        overflow: auto;
        border: solid 1px var(--vanished);
        padding: 2em 0;  
    }

    .field-properties-editor{
        position: absolute;
        top: 9px;
        bottom: 24px;
        right: 20px;
        width: 16em;
        padding: 0em 1em 1em;
        background: var(--dimmed);
        border-left: solid 1px var(--vanished);
        visibility: hidden;
        transition: all 0.2s;
        opacity: 0;
        z-index: 1;
        box-shadow: -6px 0 3px rgba(0,0,0,0.05);
        overflow-y: auto;
        overflow-x: hidden;
        height: 35.9em;

        .head{
            position: sticky;
            top: 0;
            text-align: left;
            border-bottom: solid 2px var(--vanished);
            background: inherit;
            display: flex;

            h4{
                padding: 1em;
            }

            img{
                display: block;
                margin: auto;
            }
        }

        .field-list-options{
            min-height: 4em;
            border: solid 1px var(--gray-light);
            border-radius: 6px;

            .field-option{
                background: var(--gray-light);
                color: var(--white);
                padding: 1px 8px;
                font-size: var(--font-size-md);
                border-radius: 3px;
                display: inline-block;
                margin: 3px;
            }
        }
    }

    .field-properties-editor.show{
        visibility: visible;
        opacity: 1;
    }
}
</style>