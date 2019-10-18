const state = {
    previous: null,
    current: null,
}

const getters = {
    previous: (state) => state.previous,

    current: (state) => state.current,

    noPreviousHistory: (state) => state.previous && state.previous.path === "/" && state.previous.name === null,
}

const mutations = {
    setPrevious(state, route) {
        state.previous = route
    },

    setCurrent(state, route) {
        state.current = route
    },
}

const actions = {
    setPrevious({ commit }, route) {
        commit("setPrevious", route)
    },

    setCurrent({ commit }, route) {
        commit("setCurrent", route)
    },
}

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions,
}
