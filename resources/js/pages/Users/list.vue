<template>
    <main-content>
        <template v-slot:header>
            <el-page-header class="no-back">
                <template slot="content">
                    <i class="fas fa-users" /> {{ title }}
                </template>
            </el-page-header>
            <div class="inline-block">
                <el-dropdown v-if="multipleSelection.length" @command="handleBulkCommand">
                    <button class="el-button el-button--secondary el-button--small">
                        {{ $t("Bulk actions") }} <i class="el-icon-arrow-down el-icon--right" />
                    </button>
                    <el-dropdown-menu slot="dropdown" size="mini">
                        <el-dropdown-item :command="bulkRemove" icon="far fa-trash-alt">
                            {{ $t("Delete selection") }}
                        </el-dropdown-item>
                        <el-dropdown-item :command="toggleSelection" icon="fas fa-snowplow" divided>
                            {{ $t("Clear selection") }}
                        </el-dropdown-item>
                    </el-dropdown-menu>
                </el-dropdown>
                <router-link :to="{name: 'CreateUser'}" class="el-button el-button--success el-button--small">
                    <i class="fas fa-user-plus" /> {{ $t("Create new user") }}
                </router-link>
            </div>
        </template>
        <template v-slot:body>
            <el-table :ref="tableRef" v-loading="loading" :data="models" @sort-change="orderBy"
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
                                  :placeholder="$t('Type to search')"
                                  @change="searchData"
                                  size="mini"
                                  clearable
                        />
                    </template>
                    <template slot-scope="scope">
                        <el-tooltip :content="$t('View user')" class="item" effect="dark" placement="top-start">
                            <router-link :to="{name: 'ViewUser', params: {modelId: scope.row.id}}"
                                         class="el-button el-button--primary el-button--mini"
                            >
                                <i class="fas fa-eye" />
                            </router-link>
                        </el-tooltip>
                        <el-tooltip v-if="currentUserIsAdmin || currentUser.id === scope.row.id"
                                    :content="$t('Edit user')" class="item" effect="dark" placement="top-start"
                        >
                            <router-link :to="{name: 'EditUser', params: {modelId: scope.row.id}}"
                                         class="el-button el-button--warning el-button--mini"
                            >
                                <i class="fas fa-user-edit" />
                            </router-link>
                        </el-tooltip>
                        <el-tooltip v-if="currentUserIsAdmin" :content="$t('Delete user')" class="item"
                                    effect="dark" placement="top-start"
                        >
                            <el-button :disabled="scope.row.id === currentUserId" @click="remove(scope.row)" size="mini"
                                       type="danger"
                            >
                                <i class="fas fa-user-times" />
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
    import ListPartial                from "@/components/partials/ListPartial"
    import User                       from "@/models/User"

    export default {
        name: "ListUsers",

        extends: ListPartial,

        data() {
            return {
                title: this.$t("Users"),
            }
        },

        computed: {
            ...mapGetters("auth", {
                currentUserId: "user_id",
                currentUserIsAdmin: "isAdministrator",
            }),

            ...mapGetters("user", ["currentUser"]),
        },

        methods: {
            ...mapActions("user", ["getUsers", "deleteUser", "deleteUsers"]),

            getData() {
                return this._getData(this.getUsers, User)
            },

            bulkRemove() {
                this._bulkRemove(this.deleteUsers, this.$t("user"))
            },

            remove(model) {
                this._remove(this.deleteUser, model, model.name)
            },
        },

        beforeRouteUpdate(to, from, next) {
            this._beforeRouteUpdate(to, from, next, this.getUsers, User)
        },
    }
</script>
