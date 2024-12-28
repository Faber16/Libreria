import { createApp } from 'vue';
import App from './components/App.vue';
import router from './router';

document.addEventListener('DOMContentLoaded', () => {
    const target = document.getElementById('app');

    const app = createApp(App);
    app.use(router);
    app.mount(target);
})