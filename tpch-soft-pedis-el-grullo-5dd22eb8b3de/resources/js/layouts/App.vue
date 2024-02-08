<template>
    <div class="d-flex p-relative">
        <left-nav></left-nav>
        <main>
            <top-nav></top-nav>
            <div class="content">
                <transition name="component-fade" mode="out-in">
                    <router-view :key="$route.path" v-if="isRouterAlive"></router-view>
                </transition>
            </div>
        </main>
    </div>
</template>

<script>
import leftNav from '../components/global/leftNav'
import topNav from '../components/global/topNav'
export default {
    components: {
        leftNav,
        topNav,
    },
    computed: {
        isRouterAlive(){
            return this.$root.isRouterAlive
        }
    }
}
</script>

<style>

</style>