<template>
    <section class="h-100 d-flex">
        <div class="m-auto text-center">
            <img src="/img/icons/lost.svg" class="m-auto d-block icon-xxl">
            <h1 class="title mb-auto mt-auto">
                {{ $route.meta.viewTitle }}
            </h1>
            <h3>
                {{ $route.meta.viewDescription }}
            </h3>
            <button @click="$router.back()">
                &larr; Volver
            </button>
        </div>
    </section>
</template>

<style scoped>
h1{
    font-size: 4em;
}
</style>