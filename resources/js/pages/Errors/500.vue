<template>
    <error>
        <template v-slot:error_code>
            <i class="fad fa-tombstone"/> 500
        </template>
        <template v-slot:title>
            {{ $t("Houston, we have a problem!") }}
        </template>
        <template v-slot:content>
            {{ $t("The page you are looking for is temporarily unavailable.") }}
        </template>
    </error>
</template>

<script>
    import Error from "../../components/Error"

    export default {
        name: "Error500",

        components: { Error },
    }
</script>
