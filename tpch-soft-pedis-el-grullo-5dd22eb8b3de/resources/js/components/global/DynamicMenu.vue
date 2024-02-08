<template>
    <div class="col-12 mobile-action-bar" :class="{opened: isOpen}">
        <div class="head">
            <h5 class="m-auto">Menú</h5>
            <button class="btn-sm toggle" @click="isOpen = !isOpen">
                {{ isOpen ? 'Cerrar' : 'Abrir' }}
            </button>
        </div>
        <div class="pt-1 pb-1 px-1">
            <slot></slot>
        </div>
    </div>
</template>

<script>
export default {
    data(){
        return {
            isOpen: false,
        }
    }
}
</script>
