<template>
    <error>
        <template v-slot:error_code>
            <i class="fad fa-map-signs"/> 404
        </template>
        <template v-slot:title>
            Opala, ste se morda zgubili?
        </template>
        <template v-slot:content>
            Stran, ki jo iščete, žal ne obstaja. <i class="fal fa-frown"/>
        </template>
    </error>
</template>

<script>
    import Error from "../../components/Error"

    export default {
        name: "Error404",

        components: { Error },
    }
</script>
