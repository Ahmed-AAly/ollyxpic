import services from '../services'

export default {
    'player/FETCH_PLAYER' (context, request) {
        context.commit('player/PLAYER', {})
        context.commit('player/SKILLS', [])
        context.commit('player/MONTHS', [])
        context.commit('player/EXPERIENCE', [])

        return new Promise ((resolve, reject) => {
            services.getPlayer(request.name)
                .then(response => {
                    context.commit('player/PLAYER', response.data)
                    const details = response.data.details

                    context.dispatch('player/FETCH_LEVEL', { name: details.name })
                    context.dispatch('player/FETCH_SKILLS', { name: details.name })
                    resolve()
                })
                .catch(() => reject())
        })
    },

    'player/FETCH_LEVEL' (context, request) {
        services.getPlayerLevel(request.name)
            .then(response => context.commit('player/LEVEL', response.data))
    },

    'player/FETCH_SKILLS' (context, request) {
        services.getPlayerSkills(request.name)
            .then(response => context.commit('player/SKILLS', response.data))
    },

    'player/FETCH_MONTHS' (context) {
        return new Promise ((resolve, reject) => {
            services.getMonths()
                .then(response => {
                    context.commit('player/MONTHS', response.data)
                    // context.dispatch('player/FETCH_EXPERIENCE', {
                    //     name: context.state.player.details.name,
                    //     month: context.state.months[0]
                    // })
                    resolve()
                })
                .catch(() => reject())
        })
    },

    'player/FETCH_EXPERIENCE' (context, request) {
        context.state.experience = []

        return new Promise((resolve, reject) => {
            services.getPlayerExperience(request.name, request.month)
                .then(response => {
                    let experience = response.data
                    experience = experience.map((exp, index) => {
                        const advance = index ? parseInt(exp.experience - experience[index - 1].experience) : 0
                        const up = index ? parseInt(exp.level - experience[index - 1].level) : 0
                        return { ...exp, advance, up }
                    }).filter(exp => moment(exp.updated_at).format('YYYY-MM') == request.month).sort((a, b) => b.id - a.id)

                    context.commit('player/EXPERIENCE', experience)
                    resolve()
                })
                .catch(() => reject())
        })
    },

    'player/FETCH_OVERVIEW' (context, request) {
        context.state.experience = []

        return new Promise((resolve, reject) => {
            services.getPlayerOverview(request.name)
                .then(response => {
                    context.commit('player/EXPERIENCE', response.data)
                    resolve()
                })
                .catch(() => reject())
        })
    },
}