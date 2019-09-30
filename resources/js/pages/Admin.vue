<template>
    <div id="admin" class="min-h-screen w-full">
        <el-container direction="horizontal" class="bg-gray-800 text-white h-16 flex items-center justify-between">
            <div class="flex items-center justify-center h-full w-52">
                <router-link class="h-10 w-10" :to="{name: 'Dashboard'}">
                    <img src="/images/logo.svg" alt="Logo">
                </router-link>
            </div>
            <el-header height="" class="flex items-center justify-end text-right text-sm h-full pl-0 pr-4">
                <el-dropdown @command="handleDropdownCommand">
                    <div class="flex items-center justify-center text-white">
                        <el-avatar v-if="currentUserIsAdmin" icon="fas fa-user-crown" class="mr-4" />
                        <el-avatar v-else icon="fas fa-user" class="mr-4" />
                        <span v-if="currentUser">{{ currentUser.name || currentUser.email }}</span>
                    </div>
                    <el-dropdown-menu slot="dropdown">
                        <el-dropdown-item icon="fad fa-user-edit" :command="editMyself">
                            Urejanje
                        </el-dropdown-item>
                        <el-dropdown-item icon="fad fa-sign-out-alt" :command="logout">
                            Odjava
                        </el-dropdown-item>
                    </el-dropdown-menu>
                </el-dropdown>
            </el-header>
        </el-container>
        <el-container id="admin-container">
            <el-aside width="" class="w-52 bg-gray-300">
                <el-menu :router="true" :default-active="$router.currentRoute.path">
                    <el-menu-item index="/dashboard">
                        <i class="far fa-tachometer-alt-fast" /> Nadzorna plošča
                    </el-menu-item>
                    <el-submenu v-if="currentUserIsAdmin" index="users">
                        <template slot="title">
                            <i class="fad fa-users" /> Uporabniki
                        </template>
                        <el-menu-item-group>
                            <template slot="title">
                                Urejanje uporabnikov
                            </template>
                            <el-menu-item index="/users">
                                <i class="fal fa-list" /> Seznam
                            </el-menu-item>
                            <el-menu-item index="/users/new">
                                <i class="fal fa-user-plus" /> Nov uporabnik
                            </el-menu-item>
                        </el-menu-item-group>
                    </el-submenu>
                </el-menu>
            </el-aside>

            <el-container>
                <el-main>
                    <router-view />
                </el-main>
            </el-container>
        </el-container>
    </div>
</template>

<script>
    import { mapActions, mapGetters } from "vuex"
    import User                       from "../models/User"

    export default {
        name: "Admin",

        computed: {
            ...mapGetters("user", ["currentUser", "currentUserIsAdmin"]),
        },

        mounted() {
            if (!this.currentUser) {
                this.autoLogin()
                    .then(() => {
                        this.getMe()
                            .then(({ data }) => {
                                this.setCurrentUser(new User(data.data))
                            })
                    })
                    .catch((error) => {
                        console.log(error)
                    })
            }
        },

        methods: {
            ...mapActions("auth", ["logout", "autoLogin"]),

            ...mapActions("user", ["getMe", "setCurrentUser"]),

            editMyself() {
                this.$router.push({
                    name: "EditUser",
                    params: { userId: Number(this.currentUser.id) },
                })
            },

            handleDropdownCommand(command) {
                command()
            },
        },
    }
</script>
