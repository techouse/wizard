<template>
    <error>
        <template v-slot:error_code>
            <i class="fad fa-tombstone"/> 500
        </template>
        <template v-slot:title>
            Ojej, problem ...
        </template>
        <template v-slot:content>
            Spletna stran zaradi tehničnih težav trenutno ni na voljo. <i class="fal fa-dizzy"/>
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
