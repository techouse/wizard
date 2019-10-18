export default {
    props: {
        modelId: {
            type: [String, Number],
            required: true,
        },
    },

    computed: {
        title() {
            return this.model.name
        },
    },

    methods: {
        _getModel(callback, Model) {
            return new Promise((resolve, reject) => {
                callback(this.modelId)
                    .then(({ data }) => {
                        const model = new Model(data.data)
                        this.$set(this, "model", model)
                        this.$set(this, "modelOriginal", new Model(data.data))
                        this.$set(this, "loading", false)

                        resolve(model)
                    })
                    .catch((error) => {
                        this.$set(this, "loading", false)

                        if ("response" in error && error.response.status === 403) {
                            this.$router.push({ name: "Dashboard" })
                        }

                        reject(error)
                    })
            })
        },

        goBack() {
            this.restoreModel()
            this.$router.back()
        },

        _beforeRouteUpdate(to, from, next, callback, Model) {
            callback(to.params.modelId)
                .then(({ data }) => {
                    this.$set(this, "model", new Model(data))
                })

            next()
        },
    },
}
