<template>
    <section>
        <div class="row">
            <div class="col-12">
                <view-title />
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <div class="card bg-yellow">
                    <h1 class="text-xxxxl m-auto">
                        🗃️ {{ stats.data.proceedings.total }}
                    </h1>
                    <small>Total de expedientes</small>
                </div>
            </div>
            <div class="col-6">
                <router-link :to="`expedientes/vencimiento`">
                    <div class="card bg-red pointer animated">
                        <h1 class="text-xxxxl m-auto">
                            🚨
                        </h1>
                        <small style="color: black">Expedientes próximos a vencer</small>
                        <small>HI</small>
                    </div>
                </router-link>
            </div>
            <div class="col-4">
                <div class="card">
                    <h1 class="text-xl m-auto">
                        👩🏻 {{ stats.data.proceedings.female_count }}
                    </h1>
                    <small>Expedientes <br>de mujeres</small>
                </div>
            </div>
            <div class="col-4">
                <div class="card">
                    <h1 class="text-xl m-auto">
                        👨🏻 {{ stats.data.proceedings.male_count }}
                    </h1>
                    <small>Expedientes <br>de hombres</small>
                </div>
            </div>
            <div class="col-4">
                <div class="card">
                    <h1 class="text-xl m-auto">
                        🌈 {{ stats.data.proceedings.undefined_count }}
                    </h1>
                    <small>Expedientes <br>sin género</small>
                </div>
            </div>
        </div>
        <div class="row mt-1">
            <div class="col-12">
                <div class="card text-left">
                    <small class="last-proceeding float-right d-block mb-1">Ultimo reporte</small>
                    <div v-if="stats.data.proceedings.latest" class="d-flex">
                        <div class="d-block mt-auto mb-auto mr-2">
                            <div
                                class="icon-bg icon-xl bg-primary-vanished round-xl"
                                :style="{backgroundImage: `url(${stats.data.proceedings.latest.citizen.avatar_full_path })`}">
                            </div>
                        </div>
                        <div>
                            <router-link
                                :to="`/expedientes/${stats.data.proceedings.latest.proceeding.id}/registros/${stats.data.proceedings.latest.id}/ver`"
                                class="m-auto text-lg text-heavy text-primary">
                                {{ stats.data.proceedings.latest.citizen.first_name }}
                                {{ stats.data.proceedings.latest.citizen.last_name }}
                                {{ stats.data.proceedings.latest.citizen.second_last_name }}
                            </router-link>
                            <p class="text-heavy m-auto">
                                {{ stats.data.proceedings.latest.proceeding_template.name }}
                            </p>
                            <small class="d-block text-gray mb-1">
                                Versión {{ stats.data.proceedings.latest.proceeding_template.tag_version }}.0
                            </small>
                            <span class="tag">
                                {{ stats.data.proceedings.latest.readable_created_at }}
                            </span>
                        </div>
                    </div>
                    <div v-else>
                        <p class="text-gray">
                            Nada para mostrar
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-1">
            <div class="col-6">
                <div class="card">
                    <h1 class="text-xxxxl m-auto">
                        🤸🏻 {{ stats.data.citizens.total }}
                    </h1>
                    <small>Ciudadanos registrados</small>
                </div>
            </div>
            <div class="col-6">
                <div class="card">
                    <h1 class="text-xxxxl m-auto">
                        👮🏻 {{ stats.data.users.total }}
                    </h1>
                    <small>Cuentas de usuarios activos</small>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import viewTitle from '../components/global/viewTitle'
export default {
    components: {
        viewTitle
    },
    mounted(){
        this.$store.dispatch('getStats')
    },
    computed: {
        stats(){
            return this.$store.state.stats
        },
    }
}
</script>

<style scoped>
.card{
    text-align: center;
}
@media (max-width: 719px) {
    .last-proceeding{
        float: none!important;
        text-align: center;
        margin-bottom: 2em!important;
    }
}
</style>
