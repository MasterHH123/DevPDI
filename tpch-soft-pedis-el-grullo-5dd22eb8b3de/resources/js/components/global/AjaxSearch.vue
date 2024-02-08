<template>
    <div class="ajax-search">
        <input 
            :class="{
                searching: isLoading, 
                selected: hasSelected
            }"
            type="search" 
            v-model="term" 
            :placeholder="placeholder"
            :disabled="hasSelected">
        <span class="clear" @click="clear" title="Limpiar">
            X
        </span>
        <div class="coincidences">
            <ul>
                <li 
                    @click="onOptionSelection(c)"
                    v-for="c in coincidences" 
                    :key="c.id">
                    <span class="tag">
                        {{ templateFn(c) }}
                    </span>
                </li>
            </ul>
            <div class="no-results" v-if="zeroCoincidences">
                <div>
                    <img src="/img/icons/empty.svg" class="icon-md">
                    <p>{{ noResultsMessage }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        ajaxSource: {
            type: String,
            required: true,
        },
        placeholder: {
            type: String,
            default(){
                return 'Buscar...'
            }
        },
        noResultsMessage: {
            type: String,
            default(){
                return 'Sin resultados'
            }
        },
        searchDebounceMs: {
            type: Number,
            default(){
                return 300
            }
        },
        templateFn: {
            type: Function,
            required: true
        },
        selectedValue: {}
    },
    data(){
        return {
            term: '',
            coincidences: [],
            isLoading: false,
            selected: null,
        }
    },
    computed: {
        zeroCoincidences(){
            return this.coincidences.length < 1
        },
        hasSelected(){
            return this.selected != null
        }
    },
    mounted(){
        if( this.selectedValue )
            this.onOptionSelection(this.selectedValue)
    },
    watch: {
        term(value){
            if( !value )
                return this.coincidences = []

            if( this.__searchTimeout )
                clearTimeout( this.__searchTimeout )

            this.__searchTimeout = setTimeout(()=>{
                this.search(value)
            }, this.searchDebounceMs)
        },
        selected(value){
            this.$emit('select', value)
        },
        selectedValue(value){
            this.onOptionSelection(value)
        }
    },
    methods: {
        search(term){
            this.isLoading = true
            axios.get(this.ajaxSource, {
                params: {
                    filters: {term},
                    paginate: 0,
                }
            })
            .then(r=>{
                let res = r.data
                if( res.success )
                    this.coincidences = res.data
            })
            .catch(err=>{
                window.onXHRError(err)
            })
            .finally(()=>this.isLoading = false)
        },
        clear(){
            this.term = ''
            this.selected = null
        },
        onOptionSelection(option){
            if( !option )
                return this.clear()
                
            this.selected = option
            this.term = this.templateFn(option)
        }
    }
}
</script>

<style scoped lang="scss">
.ajax-search{
    position: relative;

    [type="search"]{
        --border-size: 0px;
        --border-angle: 0turn;
        background-image: conic-gradient(from var(--border-angle), var(--white), var(--white) 50%, var(--white)), conic-gradient(from var(--border-angle), transparent 20%, var(--yellow), var(--primary));
        background-size: calc(100% - (var(--border-size) * 2)) calc(100% - (var(--border-size) * 2)), cover;
        background-position: center center;
        background-repeat: no-repeat;
        -webkit-animation-play-state: paused;
        animation-play-state: paused;
        width: 100%;
    }
    [type="search"].searching{
        --border-size: 2px;
        -webkit-animation: bg-spin 1s linear infinite;
        animation: bg-spin 1s linear infinite;
    }
    [type="search"].selected{
        background: var(--primary-vanished);
        user-select: none;
    }

    [type="search"]:focus ~ .coincidences{
        opacity: 1;
        visibility: visible;
    }

    .clear{
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        display: flex;
        padding: 12px 18px;
        cursor: pointer;
        user-select: none;
        background: var(--vanished);
        border-top-right-radius: 12px;
        border-bottom-right-radius: 12px;
        color: var(--primary);
        font-weight: bold;
        transition: all 0.1s;
    }

    .clear:hover{
        background: var(--primary-dark);
        color: var(--white);
    }

    .coincidences{
        position: absolute;
        background: var(--white);
        z-index: 6;
        box-shadow: 0 12px 48px rgba(0, 0, 0, 0.2);
        border-radius: 12px;
        border: solid 1px var(--vanished);
        padding: 0;
        top: 99%;
        left: 0;
        right: 0;
        width: 100%;
        max-height: 16em;
        overflow: auto;
        transition: all 0.1s;
        opacity: 0;
        visibility: hidden;

        ul{
            list-style-type: none;
            padding: 0;
            margin: 0;

            li{
                padding: 1em 2em;
                transition: all 0.1s;
                user-select: none;
                cursor: pointer;
            }

            li:hover{
                background-color: var(--primary-vanished);
            }
        }

        .no-results{
            display: flex;
            height: 4em;

            > *{
                margin: auto;
                display: inherit;

                img{
                    margin: auto;
                    margin-right: 1em;
                }
            }
        }
    }
}
@-webkit-keyframes bg-spin {
    to {
        --border-angle: 1turn;
    }
}
@keyframes bg-spin {
    to {
        --border-angle: 1turn;
    }
}
@property --border-angle {
    syntax: "<angle>";
    inherits: true;
    initial-value: 0turn;
}
</style>