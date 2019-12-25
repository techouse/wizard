<template>
    <div class="items-start justify-center sm:flex">
        <div class="w-full sm:w-56">
            <img src="/images/logo.svg" alt="Logo" class="w-full">
        </div>
        <div>
            <div class="sm:px-4">
                <el-form ref="form" :model="form" :rules="rules" @submit.native.prevent>
                    <el-form-item prop="email">
                        <el-input v-model="form.email" :placeholder="$t('E-mail')" type="email">
                            <i slot="prepend" class="fas fa-at" />
                        </el-input>
                    </el-form-item>
                    <el-form-item class="mt-2 sm:mt-4" prop="password">
                        <el-input v-model="form.password" :placeholder="$t('Password')" type="password">
                            <i slot="prepend" class="fas fa-key" />
                        </el-input>
                    </el-form-item>
                    <el-form-item class="mt-2 sm:mt-4">
                        <el-checkbox v-model="form.remember" @change="rememberChanged">
                            {{ $t("Remember me") }}
                        </el-checkbox>
                    </el-form-item>
                    <el-form-item class="mt-2 sm:mt-4">
                        <el-button @click="submit" type="primary" class="w-full" native-type="submit">
                            {{ $t("Login") }}
                        </el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>
    </div>
</template>

<script>
    import { mapActions, mapGetters } from "vuex"
    import User                       from "@/models/User"

    export default {
        name: "Login",

        data() {
            return {
                currentUser: new User(),
                form: {
                    email: null,
                    password: null,
                    remember: true,
                },
                rules: {
                    email: [
                        {
                            required: true,
                            message: this.$t("Please enter email"),
                            trigger: "blur",
                        },
                        {
                            type: "email",
                            message: this.$t("Please enter valid e-mail"),
                            trigger: "blur",
                        },
                    ],
                    password: [
                        {
                            required: true,
                            message: this.$t("Please enter password"),
                            trigger: "blur",
                        },
                        {
                            min: 8,
                            max: 255,
                            message: this.$t("Minimal length is {min} characters", { min: 8 }),
                            trigger: "blur",
                        },
                    ],
                },
            }
        },

        computed: {
            ...mapGetters("alert", ["alert"]),
        },

        created() {
            this.$set(this.form, "remember", true)
            this.rememberChanged(this.form.remember)
        },

        methods: {
            ...mapActions("auth", ["login", "logout", "rememberMe"]),

            ...mapActions("user", ["getMe", "setCurrentUser"]),

            ...mapActions("alert", ["error"]),

            rememberChanged(remember) {
                this.rememberMe(remember)
            },

            submit() {
                this.$refs.form.validate((valid) => {
                    if (valid) {
                        this.login({
                                email: this.form.email,
                                password: this.form.password,
                            })
                            .then(() => {
                                this.getMe()
                                    .then(({ data }) => {
                                        this.$set(this, "currentUser", new User(data.data))

                                        this.setCurrentUser(this.currentUser)

                                        this.$router.push({ name: "Dashboard" })
                                    })
                                    .catch(({ response }) => {
                                        if (response.status === 401) {
                                            this.logout()
                                        }
                                    })
                            })
                            .catch((error) => {
                                console.log(error)
                            })
                    } else {
                        this.error(this.$t("Please fill out the login form!"))
                    }
                })
            },
        },
    }
</script>
