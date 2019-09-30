<template>
    <main-form :ref="formRef" :form-ref="formRef" :loading="loading" :model="user" :rules="rules"
               :label-width="labelWidth"
    >
        <template v-slot:header>
            <el-page-header title="Nazaj" :content="title" @back="goBack" />
            <div v-if="user.id" class="card-header-actions">
                <el-button size="small" type="danger" :disabled="deleteButtonDisabled" @click="remove">
                    <i class="fal fa-trash-alt" /> Izbriši uporabnika
                </el-button>
            </div>
        </template>
        <template v-slot:body>
            <div class="flex flex-col flex-col-reverse lg:flex-row">
                <div class="lg:w-1/2">
                    <el-form-item label="E-pošta" prop="email">
                        <el-input v-model="user.email" type="email" required />
                    </el-form-item>
                    <el-form-item label="Geslo" prop="password">
                        <el-input v-model="user.password" type="password" :required="!user.id" />
                    </el-form-item>
                    <el-form-item label="Ponovi geslo" prop="password_confirmation">
                        <el-input v-model="user.password_confirmation" type="password" :required="!user.id" />
                    </el-form-item>
                    <el-form-item v-if="currentUserIsAdmin" label="Vloga" prop="role">
                        <el-select v-model="user.role" placeholder="Vloga uporabnika" required>
                            <el-option v-for="role in roles"
                                       :key="role.id"
                                       :label="role.name"
                                       :value="role.id"
                            />
                        </el-select>
                    </el-form-item>
                </div>
                <div class="lg:w-1/2">
                    <el-form-item label="Ime" prop="name">
                        <el-input v-model="user.name" type="text" required />
                    </el-form-item>
                </div>
            </div>
        </template>
        <template v-slot:footer>
            <el-button type="success" native-type="submit" @click="submit">
                {{ user.id ? "Posodobi" : "Ustvari" }}
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
                        name: "administrator",
                    },
                    {
                        id: "user",
                        name: "uporabnik",
                    },
                ],
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
                            required: true,
                            message: "Vnesite geslo",
                            trigger: "blur",
                        },
                        {
                            validator: (rule, value, callback) => {
                                if (!value) {
                                    callback(new Error("Prosim vnesite geslo"))
                                } else if (value.length > 0 && value.length < 8) {
                                    callback(new Error("Geslo je prekratko"))
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
                            message: "Prosim ponovite geslo",
                            trigger: "blur",
                        },
                        {
                            validator: (rule, value, callback) => {
                                if (!value) {
                                    callback(new Error("Prosim ponovite geslo"))
                                } else if (value !== this.user.password) {
                                    callback(new Error("Gesli se ne ujemata"))
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

            deleteButtonDisabled() {
                if (this.currentUser && "id" in this.currentUser) {
                    return this.user.id === this.currentUser.id
                }

                return false
            },

            title() {
                return "Nov uporabnik"
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
                                this.success("Uporabnik uspešno ustvarjen")
                                this.$router.push({
                                    name: "EditUser",
                                    params: { userId: data.data.id },
                                })
                            })
                            .catch(() => {
                                this.error(`Pri ustvarjanju novega uporabnika je prišlo do napake: ${this.alert.message}`)
                            })
                    } else {
                        this.error("Podatki v obrazcu niso veljavni!")
                        return false
                    }
                })
            },
        },
    }
</script>
