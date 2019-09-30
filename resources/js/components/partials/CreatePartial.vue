<script>
    import { mapActions, mapGetters } from "vuex"
    import MainForm                   from "../MainForm"

    export default {
        name: "CreatePartial",

        components: { MainForm },

        data() {
            return {
                labelWidth: "140px",
                formRef: "create-form",
                rules: {},
                loading: false,
            }
        },

        computed: {
            ...mapGetters("alert", ["alert"]),

            title() {
                return "Create"
            },
        },

        methods: {
            ...mapActions("alert", ["error", "success", "info", "warning"]),

            submit() {},

            remove() {},

            goBack() {
                this.$router.go(-1)
            },
        },
    }
</script>
