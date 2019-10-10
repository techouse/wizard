<script>
    import { mapActions, mapGetters } from "vuex"
    import CreateUser                 from "./create"
    import User                       from "../../models/User"

    export default {
        name: "EditUser",

        extends: CreateUser,

        props: {
            userId: {
                type: [String, Number],
                required: true,
            },
        },

        data() {
            return {
                formRef: "edit-user-form",
                user: new User(),
                rules: {
                    email: [
                        {
                            required: true,
                            message: this.$t("Please enter email address"),
                            trigger: "blur",
                        },
                        {
                            type: "email",
                            message: this.$t("Please enter a valid email address"),
                            trigger: "blur",
                        },
                    ],
                    password: [
                        {
                            validator: (rule, value, callback) => {
                                if (!value) {
                                    callback()
                                } else if (value.length > 0 && value.length < 8) {
                                    callback(new Error(this.$t("Password is too short")))
                                } else {
                                    if (this.user.password_confirmation !== "") {
                                        this.$refs[this.formRef].validateField("password_confirmation")
                                    }
                                    callback()
                                }
                            },
                            trigger: "blur",
                        },
                    ],
                    password_confirmation: [
                        {
                            validator: (rule, value, callback) => {
                                if (!value) {
                                    callback()
                                } else if (value !== this.user.password) {
                                    callback(new Error(this.$t("The password confirmation does not match the password")))
                                } else {
                                    callback()
                                }
                            },
                            trigger: "blur",
                        }],
                    role: [
                        {
                            required: this.currentUserIsAdmin,
                            message: this.$t("Please select a role"),
                            trigger: "blur",
                        },
                    ],
                    name: [
                        {
                            required: true,
                            message: this.$t("Please enter a name"),
                            trigger: "blur",
                        },
                        {
                            min: 3,
                            max: 255,
                            message: this.$t("Length should be between {min} and {max} characters", {
                                min: 3,
                                max: 255,
                            }),
                            trigger: "blur",
                        },
                    ],
                },
            }
        },

        computed: {
            ...mapGetters("user", ["currentUser", "currentUserIsAdmin"]),

            title() {
                return this.user.name
            },
        },

        created() {
            this.$set(this, "loading", true)

            this.getUser(this.userId)
                .then(({ data }) => {
                    this.$set(this, "user", new User(data.data))
                    this.$set(this, "loading", false)
                })
                .catch(({ response }) => {
                    this.$set(this, "loading", false)

                    if (response.status === 403) {
                        this.$router.push({ name: "Dashboard" })
                    }
                })
        },

        methods: {
            ...mapActions("user", ["getUser", "updateUser", "deleteUser"]),

            submit() {
                this.$refs[this.formRef].validate((valid) => {
                    if (valid) {
                        this.updateUser(this.user)
                            .then(() => {
                                this.$set(this.user, "password", null)
                                this.$set(this.user, "password_confirmation", null)
                                this.success(this.$t("User successfully updated"))
                            })
                            .catch(() => {
                                this.error(this.$t("There was an error updating the user: {message}", { message: this.alert.message }))
                            })
                    } else {
                        this.error(this.$t("The form data is invalid!"))
                    }
                })
            },

            remove() {
                this.$confirm(this.$t("Are you sure you want to delete {label}?", { label: this.user.name }), this.$t("Warning"), {
                        confirmButtonText: this.$t("Yes"),
                        cancelButtonText: this.$t("No"),
                        type: "warning",
                    })
                    .then(() => {
                        this.deleteUser(this.user.id)
                            .then(() => {
                                this.$router.push({ name: "Users" })
                                this.success(this.$t("User successfully deleted"))
                            })
                            .catch(() => {
                                this.error(this.$t("There was an error deleting the user: {message}", { message: this.alert.message }))
                            })
                    })
                    .catch(() => {
                        this.info(this.$t("User not deleted"))
                    })
            },
        },

        beforeRouteUpdate(to, from, next) {
            this.getUser(to.params.userId)
                .then(({ data }) => {
                    this.$set(this, "user", new User(data))
                })
            next()
        },
    }
</script>
