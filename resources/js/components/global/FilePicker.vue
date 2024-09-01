<template>
    <div>
        <div v-if="readOnly" class="dropzone readonly">
            <div v-if="emptyFiles">
                <img src="/img/icons/file.svg" class="d-block m-auto mb-1 icon-lg">
                <small class="text-primary text-center d-block no-select">{{ emptyPlaceholder }}</small>
            </div>
            <a :href="file.full_path" target="_blank" class="file animated" v-for="file in currentFiles" :title="file.name" :key="file.lastModified">
                <span class="badge bg-red">{{ file.file_ext }}</span>
                <img src="/img/icons/file.svg">
                <small class="file-name">{{ file.name }}</small>
            </a>
        </div>
        <div
            v-else 
            class="dropzone"
            @click.prevent="$refs.hiddenFilePicker.click()"
            @drop.prevent="addFile" 
            @dragover.prevent>
            <div v-if="emptyFiles">
                <img src="/img/icons/file.svg" class="d-block m-auto mb-1 icon-lg">
                <small class="text-primary text-center d-block no-select">{{ emptyPlaceholder }}</small>
            </div>
            <div class="file" v-for="file in currentFiles" :title="file.name" :key="file.lastModified">
                <small class="remove animated" @click.stop.prevent="removeFile(file)">X</small>
                <img src="/img/icons/file.svg">
                <small class="file-name">{{ file.name }}</small>
            </div>
        </div>
        <input type="file" ref="hiddenFilePicker" @change.prevent="addFile" multiple hidden>
    </div>
</template>

<script>
export default {
    props: {
        readOnly: {
            type: Boolean,
            default(){
                return false
            }
        },
        currentFiles: {
            type: Array,
            required: true
        },
        maxFiles: {
            type: Number,
            default(){
                return 6
            }
        },
        emptyPlaceholder: {
            type: String,
            default(){
                return 'Arrastra tus archivos o da click aquí para agregar'
            }
        }
    },
    computed: {
        emptyFiles(){
            return this.currentFiles.length < 1
        }
    },
    methods: {
        addFile(ev){
            let pickedFiles = null

            if( ev.type == 'drop' )
                pickedFiles = ev.dataTransfer.files
            else
                pickedFiles = ev.target.files
            
            if( pickedFiles.length < 1 )return;

            // Check
            if( (pickedFiles.length + this.currentFiles.length) > this.maxFiles )
                return swal.fire(`Puedes subir hasta ${this.maxFiles} archivos`, '', 'info');

            ([...pickedFiles]).forEach(file => {
                this.$emit('added', file)
            });

            if( ev.type !== 'drop' )
                this.$refs.hiddenFilePicker.value = null;
        },
        removeFile(file){
            this.$emit('removed', file)      
        },
    }
}
</script>

<style scoped>
.dropzone{
    border: dashed 2px var(--primary);
    background: var(--primary-vanished);
    padding: 1em;
    border-radius: 3px;
    cursor: pointer;
}
.dropzone.readonly{
    border: solid 2px var(--primary);
    cursor: auto;
}
.dropzone .file{
    width: 6em;
    height: 7em;
    overflow: auto;
    background: var(--gray-dark);
    border-radius: 6px;
    box-shadow: 0 6px 6px rgba(0,0,0,0.2);
    color: var(--white);
    position: relative;
    display: inline-block;
    margin: 3px 9px;
    overflow: hidden;
}
.file img{
    width: 3em;
    height: 3em;
    margin: auto;
    display: block;
    margin-top: 16px;
}
.dropzone.readonly .file img{
    margin-top: 20px;
}
.file .file-name{
    width: 100%;
    display: block;
    text-align: center;
    white-space: nowrap;
    text-overflow: ellipsis;
    overflow: hidden;
    margin-top: 12px;
    padding: 0 12px;
}
.file .remove{
    padding: 3px 9px;
    display: block;
    position: absolute;
    right: 0;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
}
</style>