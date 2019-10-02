<template>
    <main-content>
        <template v-slot:header>
            <el-page-header :content="title" class="no-back" />
            <div class="inline-block">
                <el-dropdown v-if="multipleSelection.length" @command="handleBulkCommand">
                    <button class="el-button el-button--secondary el-button--small">
                        {{ $t("Bulk actions") }} <i class="el-icon-arrow-down el-icon--right" />
                    </button>
                    <el-dropdown-menu slot="dropdown" size="mini">
                        <el-dropdown-item :command="bulkRemove" icon="fal fa-trash-alt">
                            {{ $t("Delete selection") }}
                        </el-dropdown-item>
                        <el-dropdown-item :command="toggleSelection" icon="fal fa-snowplow" divided>
                            {{ $t("Clear selection") }}
                        </el-dropdown-item>
                    </el-dropdown-menu>
                </el-dropdown>
                <router-link :to="{name: 'CreateUser'}" class="el-button el-button--success el-button--small">
                    <i class="far fa-user-plus" /> {{ $t("Create new user") }}
                </router-link>
            </div>
        </template>
        <template v-slot:body>
            <el-table :ref="tableRef" v-loading="loading" :data="users" @sort-change="orderBy"
                      @selection-change="handleSelectionChange"
                      class="w-full"
            >
                <el-table-column type="selection" width="55" />
                <el-table-column label="#" prop="id" width="60" sortable="custom" />
                <el-table-column :label="$t('Name')" prop="name" sortable="custom" />
                <el-table-column :label="$t('E-mail')" prop="email" sortable="custom" />
                <el-table-column :label="$t('Role')" prop="role" align="center" width="130" sortable="custom">
                    <template slot-scope="scope">
                        <el-tag :type="scope.row.role === 'administrator' ? 'warning' : 'info'" size="small">
                            {{ $t(scope.row.role) }}
                        </el-tag>
                    </template>
                </el-table-column>
                <el-table-column :label="$t('Created at')" align="center" width="160" prop="created_at"
                                 sortable="custom"
                >
                    <template slot-scope="scope">
                        <time :datetime="scope.row.created_at">{{ scope.row.created_at|localeDateString }}</time>
                    </template>
                </el-table-column>
                <el-table-column align="right">
                    <template slot="header" slot-scope="scope">
                        <el-input v-model="params.search"
                                  :placeholder="$t('Type to search name or email address')"
                                  @change="searchData"
                                  size="mini"
                                  clearable
                        />
                    </template>
                    <template slot-scope="scope">
                        <el-tooltip :content="$t('Edit user')" class="item" effect="dark" placement="top-start">
                            <router-link :to="{name: 'EditUser', params: {userId: scope.row.id}}"
                                         class="el-button el-button--secondary el-button--small"
                            >
                                <i class="far fa-user-edit" />
                            </router-link>
                        </el-tooltip>
                        <el-tooltip class="item" effect="dark" :content="$t('Delete user')" placement="top-start">
                            <el-button :disabled="scope.row.id === user_id" @click="remove(scope.row)" size="small"
                                       type="danger"
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
                               @size-change="updateData"
                               @current-change="updateData"
                               layout="prev, pager, next, sizes"
                               background
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
                title: this.$t("Users"),
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
                this._bulkRemove(this.deleteUsers, this.$t("user"))
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