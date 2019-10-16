export default {
    path: "/dashboard/",
    component: () => import(/* webpackChunkName: "dashboard" */ "../../pages/Dashboard"),
    name: "Dashboard",
    meta: {
        requiresAuth: true,
        breadcrumb: "Dashboard",
    },
}
