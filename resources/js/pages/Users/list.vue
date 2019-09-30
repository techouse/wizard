<template>
    <main-content>
        <template v-slot:header>
            <el-page-header class="no-back" :content="title" />
            <div class="inline-block">
                <el-dropdown v-if="multipleSelection.length" @command="handleBulkCommand">
                    <button class="el-button el-button--secondary el-button--small">
                        Masovne operacije <i class="el-icon-arrow-down el-icon--right" />
                    </button>
                    <el-dropdown-menu slot="dropdown" size="mini">
                        <el-dropdown-item icon="fal fa-trash-alt" :command="bulkRemove">
                            Izbriši izbor
                        </el-dropdown-item>
                        <el-dropdown-item icon="fal fa-snowplow" :command="toggleSelection" divided>
                            Počisti izbor
                        </el-dropdown-item>
                    </el-dropdown-menu>
                </el-dropdown>
                <router-link :to="{name: 'CreateUser'}" class="el-button el-button--success el-button--small">
                    <i class="far fa-user-plus" /> Nov uporabnik
                </router-link>
            </div>
        </template>
        <template v-slot:body>
            <el-table :ref="tableRef" v-loading="loading" :data="users" class="w-full" @sort-change="orderBy"
                      @selection-change="handleSelectionChange"
            >
                <el-table-column type="selection" width="55" />
                <el-table-column label="#" prop="id" width="60" sortable="custom" />
                <el-table-column label="Ime" prop="name" sortable="custom" />
                <el-table-column label="E-pošta" prop="email" sortable="custom" />
                <el-table-column label="Vloga" prop="role" align="center" width="130" sortable="custom">
                    <template slot-scope="scope">
                        <el-tag v-if="scope.row.role === 'administrator'" size="small" type="warning">
                            administrator
                        </el-tag>
                        <el-tag v-else size="small" type="info">
                            uporabnik
                        </el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="Ustvarjen" align="center" width="160" prop="created_at" sortable="custom">
                    <template slot-scope="scope">
                        <time :datetime="scope.row.created_at">{{ scope.row.created_at|localeDateString }}</time>
                    </template>
                </el-table-column>
                <el-table-column align="right">
                    <template slot="header" slot-scope="scope">
                        <el-input v-model="params.search"
                                  size="mini"
                                  placeholder="Išči po imenu ali e-pošti"
                                  clearable
                                  @change="searchData"
                        />
                    </template>
                    <template slot-scope="scope">
                        <el-tooltip class="item" effect="dark" content="Uredi uporabnika" placement="top-start">
                            <router-link :to="{name: 'EditUser', params: {userId: scope.row.id}}"
                                         class="el-button el-button--secondary el-button--small"
                            >
                                <i class="far fa-user-edit" />
                            </router-link>
                        </el-tooltip>
                        <el-tooltip class="item" effect="dark" content="Odstrani uporabnika" placement="top-start">
                            <el-button size="small" type="danger" :disabled="scope.row.id === user_id"
                                       @click="remove(scope.row)"
                            >
                                <i class="far fa-user-times" />
                            </el-button>
                        </el-tooltip>
                    </template>
                </el-table-column>
            </el-table>
            <div class="flex items-center justify-center mt-2">
                <el-pagination :current-page.sync="params.page"
                               :page-sizes="pageSizes"
                               :page-size.sync="params.per_page"
                               :total="totalCount"
                               layout="prev, pager, next, sizes"
                               background
                               @size-change="updateData"
                               @current-change="updateData"
                />
            </div>
        </template>
    </main-content>
</template>

<script>
    import { mapActions, mapGetters } from "vuex"
    import { isEmpty }                from "lodash"
    import ListPartial                from "../../components/partials/ListPartial"
    import User                       from "../../models/User"

    export default {
        name: "ListUsers",

        extends: ListPartial,

        data() {
            return {
                title: "Uporabniki",
                users: [],
            }
        },

        computed: {
            ...mapGetters("auth", ["user_id"]),

            ...mapGetters("user", ["currentUser"]),
        },

        methods: {
            ...mapActions("user", ["getUsers", "deleteUser", "deleteUsers"]),

            getData() {
                const newUrl = `${window.location.protocol}//${window.location.host}${window.location.pathname}?${
                    Object.keys(this.params)
                          .filter((k) => !!this.params[k])
                          .map((k) => `${k}=${encodeURIComponent(this.params[k])}`)
                          .join("&")
                }`
                window.history.pushState({ path: newUrl }, "", newUrl)

                return new Promise((resolve, reject) => {
                    this.getUsers({ params: this.params })
                        .then(({ data }) => {
                            this.$set(this, "users", data.data.map((user) => new User(user)))
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

            edit(user) {
                this.$router.push({
                    name: "EditUser",
                    params: { userId: user.id },
                })
            },

            bulkRemove() {
                this._bulkRemove(this.deleteUsers, "uporabnik")
            },

            remove(user) {
                this._remove(this.deleteUser, user, user.name)
            },
        },

        beforeRouteUpdate(to, from, next) {
            if (isEmpty(to.query)) {
                this.getUsers({ params: {} })
                    .then(({ data }) => {
                        this.$set(this, "users", data.data.map((user) => new User(user)))
                        this.$set(this, "totalCount", data.meta.total)
                    })
            }

            next()
        },
    }
</script>
