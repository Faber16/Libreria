import { createRouter, createWebHashHistory } from 'vue-router';
import HomeView from './components/views/HomeView.vue';
import AuthorsView from './components/views/AuthorsView.vue';
import BooksView from './components/views/BooksView.vue';
import GenresView from './components/views/GenresView.vue';

const routes = [
    { path: '/', name: 'home', component: HomeView },
    { path: '/authors', name: 'authors', component: AuthorsView },
    { path: '/books', name: 'books', component: BooksView },
    { path: '/genres', name: 'genres', component: GenresView },
];

const router = createRouter({
    history: createWebHashHistory(),
    routes,
});

export default router;
