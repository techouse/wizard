import { addSeconds, differenceInMilliseconds, differenceInMinutes, subMinutes } from "date-fns"
import jwtDecode                                                                 from "jwt-decode"
import router                                                                    from "../../router"
import api                                                                       from "../../services/api"
import i18n                                                                      from "../../i18n"

const state = {
    user_id: null,
    user_role: null,
    remember: 0,
    access_token: null,
    refresh_token: null,
    expires: 0,
    authRefresher: null,
    authRefreshThreshold: 15,
}

const getters = {
    user_id: (state) => Number(state.user_id),

    user_role: (state) => state.user_role,

    access_token: (state) => state.access_token,

    refresh_token: (state) => state.refresh_token,

    remember: (state) => state.remember,

    isAuthenticated: (state) => state.access_token !== null && +new Date() < state.expires,

    isAdministrator: (state) => state.user_role && state.user_role === "administrator",
}

const mutations = {
    setRemember(state, remember = 0) {
        state.remember = Number(remember)
        localStorage.setItem("remember", state.remember)
    },

    setAuthData(state, { user_id, user_role, access_token, refresh_token, expires, refresher }) {
        state.user_id = user_id
        state.user_role = user_role
        state.access_token = access_token
        state.refresh_token = refresh_token
        state.expires = expires
        state.authRefresher = refresher

        if (state.remember) {
            localStorage.setItem("user_id", user_id)
            localStorage.setItem("user_role", user_role)
            localStorage.setItem("access_token", access_token)
            localStorage.setItem("refresh_token", refresh_token)
            localStorage.setItem("expires", expires)
        } else {
            sessionStorage.setItem("user_id", user_id)
            sessionStorage.setItem("user_role", user_role)
            sessionStorage.setItem("access_token", access_token)
            sessionStorage.setItem("refresh_token", refresh_token)
            sessionStorage.setItem("expires", expires)
        }
    },

    setAuthRefresher(state, refresher) {
        state.authRefresher = refresher
    },

    clearAuthData(state) {
        state.user_id = null
        state.user_role = null
        state.remember = false
        state.access_token = null
        state.refresh_token = null
        state.expires = 0
        if (state.authRefresher !== null) {
            window.clearTimeout(state.authRefresher)
            state.authRefresher = null
        }

        localStorage.removeItem("remember")
        localStorage.removeItem("user_id")
        sessionStorage.removeItem("user_id")
        localStorage.removeItem("user_role")
        sessionStorage.removeItem("user_role")
        localStorage.removeItem("access_token")
        sessionStorage.removeItem("access_token")
        localStorage.removeItem("refresh_token")
        sessionStorage.removeItem("refresh_token")
        localStorage.removeItem("expires")
        sessionStorage.removeItem("expires")
    },
}

const actions = {
    rememberMe({ commit }, remember) {
        commit("setRemember", remember)
    },

    login({ commit, dispatch }, { email, password }) {
        dispatch("alert/clear", null, { root: true })

        return new Promise((resolve, reject) => {
            api.post("oauth/token", {
                   grant_type: "password",
                   client_id: process.env.MIX_API_CLIENT_ID,
                   client_secret: process.env.MIX_API_CLIENT_SECRET,
                   username: email,
                   password,
                   scope: "*",
               })
               .then(({ headers, data }) => {
                   const decodedAccessToken = jwtDecode(data.access_token)

                   const authData = {
                       user_id: decodedAccessToken.user.id,
                       user_role: decodedAccessToken.user.role,
                       access_token: data.access_token,
                       refresh_token: data.refresh_token,
                       expires: addSeconds(new Date(headers.date), Number(data.expires_in))
                           .getTime(),
                   }

                   commit("setAuthData", authData)
                   dispatch("refreshToken")

                   resolve(authData)
               })
               .catch((error) => {
                   try {
                       dispatch("alert/error", error.response.data.message, { root: true })
                   } catch (e) {
                       console.error(error)
                   }
                   reject(error)
               })
        })
    },

    autoLogin({ state, commit, dispatch }) {
        return new Promise((resolve, reject) => {
            const remember = Number(localStorage.getItem("remember"))
            commit("setRemember", remember)

            const user_id = remember ? localStorage.getItem("user_id") : sessionStorage.getItem("user_id")
            const user_role = remember ? localStorage.getItem("user_role") : sessionStorage.getItem("user_role")
            const access_token = remember ? localStorage.getItem("access_token") : sessionStorage.getItem("access_token")
            const refresh_token = remember ? localStorage.getItem("refresh_token") : sessionStorage.getItem("refresh_token")
            const expires = remember ? Number(localStorage.getItem("expires")) : Number(sessionStorage.getItem("expires"))

            if (+new Date() >= expires || !access_token || !refresh_token || !user_id || !user_role) {
                dispatch("logout")

                reject(Error(i18n.t("Invalid access and refresh tokens!")))
            } else if (differenceInMinutes(expires, +new Date()) <= state.authRefreshThreshold + 1) {
                api.post("oauth/token", {
                       grant_type: "refresh_token",
                       refresh_token,
                       client_id: process.env.MIX_API_CLIENT_ID,
                       client_secret: process.env.MIX_API_CLIENT_SECRET,
                       scope: "*",
                   })
                   .then(({ headers, data }) => {
                       const decodedAccessToken = jwtDecode(data.access_token)

                       const authData = {
                           user_id: decodedAccessToken.user.id,
                           user_role: decodedAccessToken.user.role,
                           access_token: data.access_token,
                           refresh_token: data.refresh_token,
                           expires: addSeconds(new Date(headers.date), Number(data.expires_in))
                               .getTime(),
                       }

                       commit("setAuthData", authData)
                       dispatch("refreshToken")

                       resolve(authData)
                   })
                   .catch(({ response }) => {
                       if (response.status === 401) {
                           dispatch("logout")
                       }

                       reject(response)
                   })
            } else {
                const authData = {
                    user_id,
                    user_role,
                    access_token,
                    refresh_token,
                    expires,
                }

                commit("setAuthData", authData)
                dispatch("refreshToken")

                resolve(authData)
            }
        })
    },

    refreshToken({ state, commit, dispatch }) {
        const refresher = setTimeout(() => {
                api.post("oauth/token", {
                       grant_type: "refresh_token",
                       refresh_token: state.refresh_token,
                       client_id: process.env.MIX_API_CLIENT_ID,
                       client_secret: process.env.MIX_API_CLIENT_SECRET,
                       scope: "*",
                   })
                   .then(({ headers, data }) => {
                       const decodedAccessToken = jwtDecode(data.access_token)

                       const authData = {
                           user_id: decodedAccessToken.user.id,
                           user_role: decodedAccessToken.user.role,
                           access_token: data.access_token,
                           refresh_token: data.refresh_token,
                           expires: addSeconds(new Date(headers.date), Number(data.expires_in))
                               .getTime(),
                       }

                       commit("setAuthData", authData)
                       dispatch("refreshToken")
                   })
                   .catch(({ response }) => {
                       if (response.status === 401) {
                           dispatch("logout")
                       }
                   })
            },
            Math.min(
                2 ** 31 - 1, // setTimeout uses a 32bit int to store the delay, so this is the absolute maximum
                Math.max(
                    differenceInMilliseconds(
                        subMinutes(new Date(state.expires), state.authRefreshThreshold), new Date(),
                    ), 1000,
                ),
            ))

        commit("setAuthRefresher", refresher)
    },

    logout({ commit }) {
        commit("clearAuthData")
        router.replace({ name: "Login" })
    },
}

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions,
}
