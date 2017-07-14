import Calculators from './Calculators'
import { routes as waste } from './waste'
import { routes as hunt } from './hunt'
import { routes as lootCount } from './loot-count'
import { routes as lootAcumulator } from './loot-acumulator'
import { routes as bless } from './bless'
import { routes as speedboost } from './speedboost'
import { routes as imbuements } from './imbuements'

export default [
    {
        name: 'calculators.index',
        path: '/calculators',
        component: Calculators
    },

    ...waste,

    ...hunt,

    ...lootCount,
    
    ...lootAcumulator,

    ...bless,

    ...speedboost,

    ...imbuements,
]
