<template>
    <h4 class="section-title">
        {{ viewTitle }}
        <small>
            {{ viewDescription }}
        </small>
    </h4>
</template>

<script>
export default {
    computed: {
        viewTitle(){
            return this.$route.meta.viewTitle || 'Sección' 
        },
        viewDescription(){
            return this.$route.meta.viewDescription || '' 
        }
    }
}
</script>
