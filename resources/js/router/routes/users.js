export default {
    path: "/users/",
    component: () => import(/* webpackChunkName: "users" */ "../../pages/Users"),
    meta: {
        requiresAuth: true,
        breadcrumb: "Users",
    },
    children: [
        {
            path: "",
            component: () => import(/* webpackChunkName: "users-list" */ "../../pages/Users/list"),
            name: "Users",
            props: (route) => ({
                search: route.query.search,
                page: Number(route.query.page) || 1,
                perPage: Number(route.query.per_page) || 12,
                sort: route.query.sort,
            }),
            meta: {
                requiresAuth: true,
                requiresAdmin: true,
            },
        },
        {
            path: ":modelId(\\d+)/",
            component: () => import(/* webpackChunkName: "users-edit" */ "../../pages/Users/edit"),
            props: true,
            name: "EditUser",
            meta: {
                requiresAuth: true,
                requiresMyselfOrAdmin: true,
                breadcrumb: "Edit",
            },
        },
        {
            path: "new/",
            component: () => import(/* webpackChunkName: "users-create" */ "../../pages/Users/create"),
            name: "CreateUser",
            meta: {
                requiresAuth: true,
                requiresAdmin: true,
                breadcrumb: "Create",
            },
        },
    ],
}
