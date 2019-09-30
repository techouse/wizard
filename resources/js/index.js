import Vue       from "vue"
import ElementUI from "element-ui"
import api       from "./services/api"
import router    from "./router"
import store     from "./store"
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
    render: (h) => h(App),
})
