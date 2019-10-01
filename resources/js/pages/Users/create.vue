<template>
    <main-form :ref="formRef" :form-ref="formRef" :loading="loading" :model="user" :rules="rules"
               :label-width="labelWidth"
    >
        <template v-slot:header>
            <el-page-header :title="$t('Back')" :content="title" @back="goBack" />
            <div v-if="user.id" class="card-header-actions">
                <el-button :disabled="deleteButtonDisabled" @click="remove" size="small" type="danger">
                    <i class="fal fa-trash-alt" /> {{ $t("Delete user") }}
                </el-button>
            </div>
        </template>
        <template v-slot:body>
            <div class="flex flex-col flex-col-reverse lg:flex-row">
                <div class="lg:w-1/2">
                    <el-form-item :label="$t('E-mail')" prop="email">
                        <el-input v-model="user.email" type="email" required />
                    </el-form-item>
                    <el-form-item :label="$t('Password')" prop="password">
                        <el-input v-model="user.password" :required="!user.id" type="password" />
                    </el-form-item>
                    <el-form-item :label="$t('Repeat password')" prop="password_confirmation">
                        <el-input v-model="user.password_confirmation" :required="!user.id" type="password" />
                    </el-form-item>
                    <el-form-item v-if="currentUserIsAdmin" :label="$t('Role')" prop="role">
                        <el-select v-model="user.role" :placeholder="$t('User role')" required>
                            <el-option v-for="role in roles"
                                       :key="role.id"
                                       :label="role.name"
                                       :value="role.id"
                            />
                        </el-select>
                    </el-form-item>
                </div>
                <div class="lg:w-1/2">
                    <el-form-item :label="$t('Name')" prop="name">
                        <el-input v-model="user.name" type="text" required />
                    </el-form-item>
                </div>
            </div>
        </template>
        <template v-slot:footer>
            <el-button @click="submit" type="success" native-type="submit">
                {{ user.id ? $t("Update") : $t("Create") }}
            </el-button>
        </template>
    </main-form>
</template>

<script>
    import { mapActions, mapGetters } from "vuex"
    import CreatePartial              from "../../components/partials/CreatePartial"
    import User                       from "../../models/User"

    export default {
        name: "CreateUser",

        extends: CreatePartial,

        data() {
            return {
                formRef: "create-user-form",
                user: new User(),
                roles: [
                    {
                        id: "administrator",
                        name: this.$t("administrator"),
                    },
                    {
                        id: "user",
                        name: this.$t("user"),
                    },
                ],
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
                            required: true,
                            message: this.$t("Please enter a password"),
                            trigger: "blur",
                        },
                        {
                            validator: (rule, value, callback) => {
                                if (!value) {
                                    callback(new Error(this.$t("Please enter a password")))
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
                            required: true,
                            message: this.$t("Please re-enter the password"),
                            trigger: "blur",
                        },
                        {
                            validator: (rule, value, callback) => {
                                if (!value) {
                                    callback(new Error(this.$t("Please re-enter the password")))
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
                            message: this.$t("Length should be between {min} and {max} characters", { min: 3, max: 255 }),
                            trigger: "blur",
                        },
                    ],
                },
            }
        },

        computed: {
            ...mapGetters("user", ["currentUser", "currentUserIsAdmin"]),

            deleteButtonDisabled() {
                if (this.currentUser && "id" in this.currentUser) {
                    return this.user.id === this.currentUser.id
                }

                return false
            },

            title() {
                return this.$t("Create new user")
            },
        },

        mounted() {
            if (!this.user.role) {
                this.$set(this.user, "role", "user")
            }
        },

        methods: {
            ...mapActions("user", ["createUser"]),

            submit() {
                this.$refs[this.formRef].validate((valid) => {
                    if (valid) {
                        this.createUser(this.user)
                            .then(({ data }) => {
                                this.success(this.$t("User successfully created"))
                                this.$router.push({
                                    name: "EditUser",
                                    params: { userId: data.data.id },
                                })
                            })
                            .catch(() => {
                                this.error(this.$t("There was an error creating the user: {message}", { message: this.alert.message }))
                            })
                    } else {
                        this.error(this.$t("The form data is invalid!"))
                        return false
                    }
                })
            },
        },
    }
</script>
