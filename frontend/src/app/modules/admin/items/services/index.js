export default {
    getItemsFromCategory (id) {
        return window.axios.get(`admin/items/${id}`)
    },

    getCategories () {
        return window.axios.get(`admin/categories`)
    },

    toggleUsable (id) {
        return window.axios.get(`admin/items/${id}/usable`)
    }
}