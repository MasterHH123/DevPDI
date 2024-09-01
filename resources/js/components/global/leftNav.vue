<template>
    <aside :class="{opened: isLeftNavOpen}" @click="$store.commit('openLeftNav', false)">
        <ul class="list-unstyled text-center p0 m0">
            <li class="p0">
                <img src="/img/branding/logo.png" alt="" class="icon-xxl">
            </li>
            <router-link v-if="userHasPermissionTo('proceedings.stats')" to="/" tag="li">
                <img src="/img/icons/chart.svg" alt="" class="icon-lg">
                <h4 class="m-auto">
                    Inicio
                </h4>
            </router-link>
            <router-link v-if="userHasPermissionTo('proceedings.show')" to="/expedientes" tag="li">
                <img src="/img/icons/docs.svg" alt="" class="icon-lg">
                <h4 class="m-auto">
                    Expedientes
                </h4>
            </router-link>
            <router-link v-if="userHasPermissionTo('citizens.show')" to="/ciudadanos" tag="li">
                <img src="/img/icons/users.svg" alt="" class="icon-lg">
                <h4 class="m-auto">
                    Ciudadanos
                </h4>
            </router-link>
            <router-link v-if="userHasPermissionTo('users.show')" to="/usuarios" tag="li">
                <img src="/img/icons/police.svg" alt="" class="icon-lg">
                <h4 class="m-auto">
                    Usuarios
                </h4>
            </router-link>
            <router-link v-if="userHasPermissionTo('locations.show')" to="/ubicaciones" tag="li">
                <img src="/img/icons/map-gps.svg" alt="" class="icon-lg">
                <h4 class="m-auto">
                    Mapa
                </h4>
            </router-link>
            <router-link v-if="userHasPermissionTo('app_logs.show')" to="/bitacora" tag="li">
                <img src="/img/icons/log.svg" alt="" class="icon-lg">
                <h4 class="m-auto">
                    Registro de Actividad
                </h4>
            </router-link>
        </ul>
        <div class="copy text-center p-absolute bottom-0 px-1 pb-1 opacity-05 w-100">
            <small>
                2024 &copy; Plataforma Digital de Inteligencia y Seguridad
            </small>
        </div>
    </aside>
</template>

<script>
export default {
    computed: {
        isLeftNavOpen(){
            return this.$store.state.app.isLeftNavOpen
        }
    },
}
</script>
