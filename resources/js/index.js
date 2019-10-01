import Vue       from "vue"
import ElementUI from "element-ui"
import api       from "./services/api"
import router    from "./router"
import store     from "./store"
import i18n      from "./i18n"
import App       from "./App"
import "./filters"

// CSRF
const csrfToken = document.head.querySelector("meta[name=\"csrf-token\"]")
if (csrfToken) {
    api.defaults.headers.common["X-CSRF-TOKEN"] = csrfToken.content
} else {
    console.error("CSRF token not found!")
}

// Set Axios as default resource handler
Vue.prototype.$http = api

Vue.use(ElementUI)

new Vue({
    el: "#app",
    router,
    store,
    i18n,
    render: (h) => h(App),
})
