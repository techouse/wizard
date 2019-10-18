<script>
    import { mapActions } from "vuex"
    import CreateUser     from "./create"
    import EditMixin      from "../../mixins/EditMixin"
    import User           from "../../models/User"

    export default {
        name: "EditUser",

        extends: CreateUser,

        mixins: [EditMixin],

        data() {
            return {
                formRef: "edit-user-form",
                model: new User(),
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
                                    if (this.model.password_confirmation !== "") {
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
                                } else if (value !== this.model.password) {
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

        created() {
            this.$set(this, "loading", true)

            this._getModel(this.getUser, User)
        },

        methods: {
            ...mapActions("user", ["getUser", "updateUser", "deleteUser"]),

            submit() {
                this.$refs[this.formRef].validate((valid) => {
                    if (valid) {
                        this.updateUser(this.model)
                            .then(() => {
                                this.$set(this.model, "password", null)
                                this.$set(this.model, "password_confirmation", null)
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
                this.$confirm(this.$t("Are you sure you want to delete {label}?", { label: this.model.name }), this.$t("Warning"), {
                        confirmButtonText: this.$t("Yes"),
                        cancelButtonText: this.$t("No"),
                        type: "warning",
                    })
                    .then(() => {
                        this.deleteUser(this.model.id)
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
    }
</script>
