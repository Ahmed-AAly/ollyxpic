export default {
    fetchWorlds () {
        return window.axios.get(`admin/worlds`)
    },

    save (action, data) {
        return window.axios.post(action, data)
    },

    find (id) {
        return window.axios.get(`admin/worlds/${id}`)
    },

    remove (id) {
        return window.axios.post(`admin/worlds/${id}`, { _method: 'DELETE' })
    },

    addCurrency (id, currency) {
        return window.axios.post(`admin/worlds/${id}/currencies`, currency)
    }
}