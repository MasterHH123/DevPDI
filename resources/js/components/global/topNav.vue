<template>
    <nav class="d-flex px-3">
        <div class="mr-auto d-flex">
            <div class="menu-mobile" @click="$store.commit('openLeftNav', true)">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
        <div class="ml-auto d-flex">
            <router-link tag="h4" to="/mi-perfil" class="mt-auto mb-auto mr-1 pointer animated">
                <span>{{ user.first_name }}</span>
                <span>{{ user.last_name }}</span>
            </router-link>
            <router-link tag="div" 
                to="/mi-perfil" 
                class="round-xl icon-bg icon-md d-block mt-auto mb-auto mr-3 pointer animated" 
                :style="{backgroundImage: `url(${user.avatar_full_path})`}">
            </router-link>
            <img src="/img/icons/off.svg" class="d-block mt-auto mb-auto icon-md pointer animated" @click="$store.dispatch('logout')">
        </div>
    </nav>
</template>

<script>
export default {
    computed: {
        user(){
            return this.$store.state.user
        }
    }
}
</script>

<style scoped>
.icon-bg{
    background-color: var(--primary-vanished);
}
</style>