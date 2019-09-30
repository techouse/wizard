import axios from "axios"

const api = axios.create({
    baseURL: "/api/",
    headers: {
        common: {
            "X-Requested-With": "XMLHttpRequest",
        },
    },
})

api.CancelToken = axios.CancelToken
api.isCancel = axios.isCancel

/**
 * The interceptor here ensures that we check for the token in local storage every time an ajax request is made
 */
api.interceptors.request
   .use((config) => {
           const remember = Number(localStorage.getItem("remember"))
           const access_token = remember ? localStorage.getItem("access_token") : sessionStorage.getItem("access_token")

           if (access_token) {
               const authorisedConfig = { ...config }
               authorisedConfig.headers.Authorization = `Bearer ${access_token}`
               return authorisedConfig
           }

           return config
       },

       (error) => Promise.reject(error))

export default api
