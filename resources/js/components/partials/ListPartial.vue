<script>
    import { mapActions, mapGetters } from "vuex"
    import MainContent                from "../MainContent"

    export default {
        name: "ListPartial",

        components: {
            MainContent,
        },

        props: {
            search: {
                type: String,
                required: false,
                default: "",
            },
            page: {
                type: Number,
                required: false,
                default: 1,
            },
            perPage: {
                type: Number,
                required: false,
                default: 12,
            },
            sort: {
                type: String,
                required: false,
                default: "",
            },
        },

        data() {
            return {
                tableRef: "listTable",
                loading: false,
                multipleSelection: [],
                params: {
                    search: this.search,
                    page: this.page,
                    per_page: this.perPage,
                    sort: null,
                },
                pageSizes: [12, 24, 48, 96],
                totalCount: 0,
            }
        },

        computed: {
            ...mapGetters("alert", ["alert"]),
        },

        created() {
            this.$set(this, "loading", true)
            this.updateData()
        },

        mounted() {
            this.$set(this.params, "search", this.search)
            this.$set(this.params, "page", this.page)
            this.$set(this.params, "per_page", this.perPage)
            this.$set(this.params, "sort", this.sort)
        },

        methods: {
            ...mapActions("alert", ["error", "success", "info", "warning"]),

            getData() {
                console.warn("Implement getData in a child component!")
            },

            updateData(image) {
                this.$set(this, "loading", true)

                this.getData()
                    .then(() => {
                        this.$set(this, "loading", false)
                    })
                    .catch(() => {
                        this.$set(this, "loading", false)
                    })
            },

            searchData() {
                this.$set(this.params, "page", 1)

                this.updateData()
            },

            orderBy({ prop, order }) {
                if (prop && order) {
                    const direction = order === "descending" ? "desc" : "asc"
                    this.$set(this.params, "sort", `${prop}|${direction}`)
                } else {
                    this.$set(this.params, "sort", null)
                }

                this.updateData()
            },

            toggleSelection(rows) {
                if (rows && Array.isArray(rows)) {
                    rows.forEach((row) => {
                        this.$refs[this.tableRef].toggleRowSelection(row)
                    })
                } else {
                    this.$refs[this.tableRef].clearSelection()
                }
            },

            handleSelectionChange(val) {
                this.$set(this, "multipleSelection", val)
            },

            bulkRemove() {
                console.error("Not implemented. You must implement bulkRemove in the child component!")
            },

            _bulkRemove(callback, singularLabel = "vnos", pluralLabel = null) {
                if (typeof callback !== "function") {
                    console.error("Invalid callback function provided!")
                    return
                }
                const count = this.multipleSelection.length
                const label = count > 1 ? pluralLabel || `${singularLabel}i` : singularLabel

                this.$confirm(`Ali ste prepričani, da želite izbrisati ${count} ${label}?`, "Pozor", {
                        confirmButtonText: "Da",
                        cancelButtonText: "Ne",
                        type: "warning",
                    })
                    .then(() => {
                        const ids = this.multipleSelection.map((el) => el.id)
                        callback(ids)
                            .then(() => {
                                this.updateData()
                                this.success(`${count} ${label} uspešno izbrisani`)
                                this.$set(this, "multipleSelection", this.multipleSelection.filter((el) => !ids.includes(el.id)))
                            })
                            .catch(() => {
                                this.error(`Prišlo je do napake pri brisanju ${count} ${label}: ${this.alert.message}`)
                            })
                    })
                    .catch(() => {
                        this.info(`${count} ${label} niso bili izbrisani.`)
                    })
            },

            remove(item = null) {
                console.error("Not implemented. You must implement remove in the child component!")
            },

            _remove(callback, model, label = "record") {
                if (typeof callback !== "function") {
                    console.error("Invalid callback function provided!")
                    return
                }

                if (typeof model !== "object" || model.id === undefined) {
                    console.error("Invalid model provided!")
                    return
                }

                this.$confirm(`Ali ste prepričani, da želite izbrisati ${label}?`, "Pozor", {
                        confirmButtonText: "Da",
                        cancelButtonText: "Ne",
                        type: "warning",
                    })
                    .then(() => {
                        callback(model.id)
                            .then(() => {
                                this.updateData()
                                this.success(`${label} uspešno izbrisan`)
                            })
                            .catch(() => {
                                this.error(`Prišlo je do napake pri brisanju ${label}: ${this.alert.message}`)
                            })
                    })
                    .catch(() => {
                        this.info(`${this.capitalize(label)} ni bil izbrisan`)
                    })
            },

            handleBulkCommand(command) {
                command()
            },

            capitalize: (s) => {
                if (typeof s !== "string") return ""
                return s.charAt(0)
                        .toUpperCase() + s.slice(1)
            },
        },
    }
</script>
