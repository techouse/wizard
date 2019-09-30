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
                            message: "Prosim vnesite e-poštni naslov",
                            trigger: "blur",
                        },
                        {
                            type: "email",
                            message: "Prosim vnesite veljaven e-poštni naslov",
                            trigger: "blur",
                        },
                    ],
                    password: [
                        {
                            validator: (rule, value, callback) => {
                                if (!value) {
                                    callback()
                                } else if (value.length > 0 && value.length < 8) {
                                    callback(new Error("Geslo je prekratko."))
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
                                    callback(new Error("Gesli se ne ujemata!"))
                                } else {
                                    callback()
                                }
                            },
                            trigger: "blur",
                        }],
                    role: [
                        {
                            required: this.currentUserIsAdmin,
                            message: "Prosim izberite vlogo",
                            trigger: "blur",
                        },
                    ],
                    name: [
                        {
                            required: true,
                            message: "Prosim vnesite ime",
                            trigger: "blur",
                        },
                        {
                            min: 3,
                            max: 255,
                            message: "Dolžina naj bo med 3 in 255 znakov",
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
                                this.success("Uporabnik uspešno posodobljen!")
                            })
                            .catch(() => {
                                this.error(`Pri posodabljanju uporabnika je prišlo do napake: ${this.alert.message}`)
                            })
                    } else {
                        this.error("Podatki obrazca niso veljavni!")
                        return false
                    }
                })
            },

            remove() {
                this.$confirm(`Ali ste prepričani, da hočete izbrisati ${this.user.name}?`, "Pozor", {
                        confirmButtonText: "Da",
                        cancelButtonText: "Ne",
                        type: "warning",
                    })
                    .then(() => {
                        this.deleteUser(this.user.id)
                            .then(() => {
                                this.$router.push({ name: "Users" })
                                this.success("Uporabnik uspešno izbrisan!")
                            })
                            .catch(() => {
                                this.error(`Pri brisanju uporabnika je prišlo do napake: ${this.alert.message}`)
                            })
                    })
                    .catch(() => {
                        this.info("Uporabnik ni bil izbrisan!")
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
