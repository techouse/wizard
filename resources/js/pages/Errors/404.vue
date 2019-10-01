<template>
    <error>
        <template v-slot:error_code>
            <i class="fad fa-map-signs"/> 404
        </template>
        <template v-slot:title>
            {{ $t("Oops! You're lost.") }}
        </template>
        <template v-slot:content>
            {{ $t("The page you are looking for was not found.") }}
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
