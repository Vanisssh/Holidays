import './bootstrap';
import { createApp } from 'vue';
import router from './router';
import Home from './components/Home.vue';

const app = createApp(Home); // создаем приложение

app.use(router); // подключаем маршруты
app.mount('#app');