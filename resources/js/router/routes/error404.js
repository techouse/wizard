export default {
    path: "*",
    component: () => import(/* webpackChunkName: "errors-404" */ "@/pages/Errors/404"),
    name: "Error404",
    meta: {
        error: true,
    },
}
