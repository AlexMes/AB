export default [
    {
        path:'/facebook/pages',
        name:'profile-pages.index',
        component: ()=> import(/* webpackChunkName: "adsets-index" */'./Index.vue')
    }
]
