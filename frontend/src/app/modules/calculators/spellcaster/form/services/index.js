export default {
    getCreatures (query) {
        return window.axios.get('creatures/search', { params: { query } })
    },

    getCreature (id) {
        return window.axios.get(`creatures/${id}`)
    }
}