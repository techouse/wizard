const state = {
    type: null,
    message: null,
}

const getters = {
    alert(state) {
        return state.message ? {
            message: state.message,
            type: state.type,
        } : null
    },
}

const mutations = {
    success(state, message) {
        state.type = "success"
        state.message = message
    },
    warning(state, message) {
        state.type = "warning"
        state.message = message
    },
    error(state, message) {
        state.type = "error"
        state.message = message
    },
    info(state, message) {
        state.type = "info"
        state.message = message
    },
    clear(state) {
        state.type = null
        state.message = null
    },
}

const actions = {
    success: ({ commit }, message) => commit("success", message),
    warning: ({ commit }, message) => commit("warning", message),
    error: ({ commit }, message) => commit("error", message),
    info: ({ commit }, message) => commit("info", message),
    clear: ({ commit }) => commit("clear"),
}

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions,
}
