export default {
    path: "/auth/",
    component: () => import(/* webpackChunkName: "auth" */ "../../pages/Auth"),
    children: [
        {
            path: "login/",
            component: () => import(/* webpackChunkName: "auth-login" */ "../../pages/Auth/login.vue"),
            name: "Login",
            meta: {
                auth: true,
                guest: true,
            },
        },
    ],
}
