<script>
    import { isEmpty } from "lodash-es"
    import MainContent from "../MainContent"
    import AlertMixin  from "../../mixins/AlertMixin"

    export default {
        name: "ListPartial",

        components: {
            MainContent,
        },

        mixins: [AlertMixin],

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
                models: [],
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
            getData() {
                console.warn(this.$t("Implement getData in a child component!"))
            },

            _getData(callback, Model) {
                const newUrl = `${window.location.protocol}//${window.location.host}${window.location.pathname}?${
                    Object.keys(this.params)
                          .filter((k) => !!this.params[k])
                          .map((k) => `${k}=${encodeURIComponent(this.params[k])}`)
                          .join("&")
                }`
                window.history.pushState({ path: newUrl }, "", newUrl)

                return new Promise((resolve, reject) => {
                    callback({ params: this.params })
                        .then(({ data }) => {
                            this.$set(this, "models", data.data.map((el) => new Model(el)))
                            this.$set(this, "totalCount", data.meta.total)
                            resolve()
                        })
                        .catch(({ response }) => {
                            if (response.status === 403) {
                                this.$router.push({ name: "Dashboard" })
                            }

                            reject(response)
                        })
                })
            },

            updateData() {
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
                console.error(this.$t("Not implemented. You must implement bulkRemove in the child component!"))
            },

            _bulkRemove(callback, singularLabel = "vnos", pluralLabel = null) {
                if (typeof callback !== "function") {
                    console.error(this.$t("Invalid callback function provided!"))
                    return
                }
                const count = this.multipleSelection.length
                const label = count > 1 ? pluralLabel || `${singularLabel}${this.$t("plural_suffix")}` : singularLabel

                this.$confirm(this.$t("Are you sure you want to delete {count} {label}?", {
                        count,
                        label,
                    }), this.$t("Warning"), {
                        confirmButtonText: this.$t("Yes"),
                        cancelButtonText: this.$t("No"),
                        type: "warning",
                    })
                    .then(() => {
                        const ids = this.multipleSelection.map((el) => el.id)
                        callback(ids)
                            .then(() => {
                                this.updateData()
                                this.success(this.$t("{count} {label} successfully deleted", { count, label }))
                                this.$set(this, "multipleSelection", this.multipleSelection.filter((el) => !ids.includes(el.id)))
                            })
                            .catch(() => {
                                this.error(this.$t("There was an error deleting the {count} {label}: {message}", {
                                    count,
                                    label,
                                    message: this.alert.message,
                                }))
                            })
                    })
                    .catch(() => {
                        this.info(this.$t("{count} {label} were not deleted.", { count, label }))
                    })
            },

            remove(item = null) {
                console.error(this.$t("Not implemented. You must implement remove in the child component!"))
            },

            _remove(callback, model, label = "record") {
                if (typeof callback !== "function") {
                    console.error(this.$t("Invalid callback function provided!"))
                    return
                }

                if (typeof model !== "object" || model.id === undefined) {
                    console.error(this.$t("Invalid model provided!"))
                    return
                }

                this.$confirm(this.$t("Are you sure you want to delete {label}?", { label }), this.$t("Warning"), {
                        confirmButtonText: this.$t("Yes"),
                        cancelButtonText: this.$t("No"),
                        type: "warning",
                    })
                    .then(() => {
                        callback(model.id)
                            .then(() => {
                                this.updateData()
                                this.success(this.$t("{label} successfully deleted", { label }))
                            })
                            .catch(() => {
                                this.error(this.$t("There was an error deleting the {label}: {message}", {
                                    label,
                                    message: this.alert.message,
                                }))
                            })
                    })
                    .catch(() => {
                        this.info(this.$t("{label} not deleted", { label: this.capitalize(label) }))
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

            _beforeRouteUpdate(to, from, next, callback, Model) {
                if (isEmpty(to.query)) {
                    callback({ params: {} })
                        .then(({ data }) => {
                            this.$set(this, "models", data.data.map((el) => new Model(el)))
                            this.$set(this, "totalCount", data.meta.total)
                        })
                }

                next()
            },
        },
    }
</script>
