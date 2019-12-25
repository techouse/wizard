<script>
    import MainForm   from "@/components/MainForm"
    import AlertMixin from "@/mixins/AlertMixin"

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
                modelOriginal: {},
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

            restoreModel() {
                this.$set(this, "model", this.modelOriginal)
            },

            goBack() {
                this.$router.back()
            },
        },
    }
</script>
