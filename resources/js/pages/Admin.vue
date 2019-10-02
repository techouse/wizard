<template>
    <div id="admin" class="min-h-screen w-full">
        <el-container direction="horizontal" class="bg-gray-800 text-white h-16 flex items-center justify-between">
            <div class="flex items-center justify-start h-full w-52 pl-2">
                <router-link :to="{name: 'Dashboard'}" class="flex items-center justify-center">
                    <img class="h-10 w-10" src="/images/logo.svg" alt="Logo">
                    <span v-if="!showCollapsedMenu" class="text-2xl font-bold ml-2">Wizard</span>
                </router-link>
            </div>
            <el-header height="" class="flex items-center justify-end text-right text-sm h-full pl-0 pr-4">
                <el-dropdown @command="handleDropdownCommand">
                    <div class="flex items-center justify-center text-white">
                        <el-avatar class="mr-4 flex items-center justify-center">
                            <img v-if="currentUserIsAdmin" src="/images/crown.svg" class="w-3/4">
                            <img v-else src="/images/baby.svg" class="w-3/4">
                        </el-avatar>
                        <span v-if="currentUser">{{ currentUser.name || currentUser.email }}</span>
                    </div>
                    <el-dropdown-menu slot="dropdown">
                        <el-dropdown-item :command="editMyself" icon="fad fa-user-edit">
                            {{ $t("Edit") }}
                        </el-dropdown-item>
                        <el-dropdown-item :command="logout" icon="fad fa-sign-out-alt">
                            {{ $t("Logout") }}
                        </el-dropdown-item>
                    </el-dropdown-menu>
                </el-dropdown>
            </el-header>
        </el-container>
        <el-container id="admin-container">
            <el-aside width="" class="bg-gray-300">
                <el-menu :router="true" :default-active="$router.currentRoute.path" :collapse="showCollapsedMenu"
                         class="main-menu"
                >
                    <el-menu-item index="/dashboard">
                        <i class="far fa-tachometer-alt-fast" />
                        <span slot="title">{{ $t("Dashboard") }}</span>
                    </el-menu-item>
                    <el-submenu v-if="currentUserIsAdmin" index="users">
                        <template slot="title">
                            <i class="fad fa-users" />
                            <span slot="title">{{ $t("Users") }}</span>
                        </template>
                        <el-menu-item-group>
                            <span slot="title">
                                {{ $t("Edit users") }}
                            </span>
                            <el-menu-item index="/users">
                                <i class="fal fa-list" />
                                <span slot="title">{{ $t("List") }}</span>
                            </el-menu-item>
                            <el-menu-item index="/users/new">
                                <i class="fal fa-user-plus" />
                                <span slot="title">{{ $t("Create new user") }}</span>
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

        data() {
            return {
                clientWidth: null,
                clientHeight: null,
            }
        },

        computed: {
            ...mapGetters("user", ["currentUser", "currentUserIsAdmin"]),

            showCollapsedMenu() {
                return this.clientWidth !== null && this.clientWidth < 1024
            },
        },

        beforeMount() {
            this.handleWindowResize()
        },

        mounted() {
            window.addEventListener("resize", this.handleWindowResize)

            if (!this.currentUser) {
                this.autoLogin()
                    .then(() => {
                        this.getMe()
                            .then(({ data }) => {
                                this.setCurrentUser(new User(data.data))
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

            handleWindowResize() {
                this.$set(this, "clientWidth", document.documentElement.clientWidth)
                this.$set(this, "clientHeight", document.documentElement.clientHeight)
            },
        },
    }
</script>
