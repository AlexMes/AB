export default [
    {
        path:'/facebook/adsets',
        name:'adsets.index',
        component: ()=> import(/* webpackChunkName: "adsets-index" */'./Index.vue')
    }
]
