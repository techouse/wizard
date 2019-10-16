<script>
    import MainForm   from "../MainForm"
    import AlertMixin from "../../mixins/AlertMixin"

    export default {
        name: "CreatePartial",

        components: { MainForm },

        mixins: [AlertMixin],

        props: {
            locked: {
                type: Boolean,
                default: false,
            },
        },

        data() {
            return {
                labelWidth: "140px",
                formRef: "create-form",
                model: {},
                rules: {},
                loading: false,
            }
        },

        computed: {
            title() {
                return "Create"
            },
        },

        methods: {
            submit() {
            },

            remove() {
            },

            goBack() {
                this.$router.go(-1)
            },
        },
    }
</script>
