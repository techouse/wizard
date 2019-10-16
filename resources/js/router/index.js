import Vue       from "vue"
import Router    from "vue-router"
import store     from "../store"
import auth      from "./routes/auth"
import dashboard from "./routes/dashboard"
import users     from "./routes/users"
import error404  from "./routes/error404"

const routerOptions = [
    {
        path: "/",
        redirect: { name: "Dashboard" },
    },
    auth,
    dashboard,
    users,
    error404,
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
