import Vue    from "vue"
import Router from "vue-router"
import store  from "../store"

/**
 * PAGES
 */
// Auth
const Auth = () => import(/* webpackChunkName: "auth" */ "../pages/Auth")
const Login = () => import(/* webpackChunkName: "auth-login" */ "../pages/Auth/login.vue")
// Dashboard
const Dashboard = () => import(/* webpackChunkName: "dashboard" */ "../pages/Dashboard")
// Users
const Users = () => import(/* webpackChunkName: "users" */ "../pages/Users")
const ListUsers = () => import(/* webpackChunkName: "users-list" */ "../pages/Users/list")
const CreateUser = () => import(/* webpackChunkName: "users-create" */ "../pages/Users/create")
const EditUser = () => import(/* webpackChunkName: "users-edit" */ "../pages/Users/edit")
// Errors
const Error404 = () => import(/* webpackChunkName: "errors-404" */ "../pages/Errors/404")

const routerOptions = [
    {
        path: "/",
        redirect: { name: "Dashboard" },
    },
    {
        path: "/auth/",
        component: Auth,
        children: [
            {
                path: "login/",
                component: Login,
                name: "Login",
                meta: {
                    auth: true,
                    guest: true,
                },
            },
        ],
    },
    {
        path: "/dashboard/",
        component: Dashboard,
        name: "Dashboard",
        meta: {
            requiresAuth: true,
            breadcrumb: "Dashboard",
        },
    },
    {
        path: "/users/",
        component: Users,
        meta: {
            requiresAuth: true,
            breadcrumb: "Users",
        },
        children: [
            {
                path: "",
                component: ListUsers,
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
                component: EditUser,
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
                component: CreateUser,
                name: "CreateUser",
                meta: {
                    requiresAuth: true,
                    requiresAdmin: true,
                    breadcrumb: "Create",
                },
            },
        ],
    },
    {
        path: "*",
        component: Error404,
        name: "Error404",
        meta: {
            error: true,
        },
    },
]

Vue.use(Router)

const router = new Router(
    {
        routes: routerOptions,
        base: "/admin/",
        mode: "history",
        linkActiveClass: "active",
    },
)

router.beforeEach((to, from, next) => {
    const remember = Number(localStorage.getItem("remember"))
    const user_id = remember ? Number(localStorage.getItem("user_id")) : Number(sessionStorage.getItem("user_id"))
    const user_role = remember ? localStorage.getItem("user_role") : sessionStorage.getItem("user_role")
    const access_token = remember ? localStorage.getItem("access_token") : sessionStorage.getItem("access_token")
    const refresh_token = remember ? localStorage.getItem("refresh_token") : sessionStorage.getItem("refresh_token")
    const expires = remember ? Number(localStorage.getItem("expires")) : Number(sessionStorage.getItem("expires"))

    if (to.matched.some((record) => record.meta.requiresAuth)) {
        if (+new Date() >= expires || !access_token || !refresh_token || !user_id || !user_role) {
            store.commit("auth/clearAuthData")
            next({ name: "Login" })
        } else if (to.matched.some((record) => record.meta.requiresMyselfOrAdmin)) {
            if (("modelId" in to.params && to.params.modelId === user_id) || user_role === "administrator") {
                next()
            } else {
                next({ name: "Dashboard" })
            }
        } else if (to.matched.some((record) => record.meta.requiresAdmin)) {
            if (user_role === "administrator") {
                next()
            } else {
                next({ name: "Dashboard" })
            }
        } else {
            next()
        }
    } else if (to.matched.some((record) => record.meta.guest)) {
        if (+new Date() >= expires || !access_token) {
            next()
        } else {
            next({ name: "Dashboard" })
        }
    } else {
        next()
    }
})

export default router
