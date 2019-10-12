import { mapActions, mapGetters } from "vuex"

export default {
    computed: {
        ...mapGetters("alert", ["alert"]),
    },

    methods: {
        ...mapActions("alert", ["error", "success", "info", "warning"]),
    },
}
