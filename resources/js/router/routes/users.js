import makeCrudRoutes from "@/utils/makeCrudRoutes"

export default makeCrudRoutes({
    name: "Users",
    components: {
        index: () => import(/* webpackChunkName: "users" */ "@/pages/Users"),
        list: {
            component: () => import(/* webpackChunkName: "users-list" */ "@/pages/Users/list"),
            meta: { requiresMyselfOrAdmin: true },
        },
        edit: {
            component: () => import(/* webpackChunkName: "users-edit" */ "@/pages/Users/edit"),
            meta: { requiresMyselfOrAdmin: true },
        },
        create: {
            component: () => import(/* webpackChunkName: "users-create" */ "@/pages/Users/create"),
            meta: { requiresAdmin: true },
        },
    },
})
