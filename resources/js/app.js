import './bootstrap';
import { createApp } from 'vue';
import { createRouter, createWebHashHistory } from 'vue-router'
import DataTable from 'datatables.net-vue3';
import DataTablesLib from 'datatables.net-bs5';
import DataTablesCore from 'datatables.net-bs5';
import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
import 'datatables.net-select-bs5';

const app = createApp({});

import ExampleComponent from './components/ExampleComponent.vue';
//app.component('example-component', ExampleComponent);

import EstudiantesPage from './../src/pages/Estudiantes.vue';
import BusesPage from './../src/pages/buses.vue';
import RutasPage from './../src/pages/rutas.vue';
//app.component('estudiantes-page', EstudiantesPage);

const routes = [
    { path: '/', component: '' },
    { path: '/home', component: ExampleComponent },
    { path: '/estudiantes', component: EstudiantesPage },
    { path: '/buses', component: BusesPage },
    { path: '/rutas', component: RutasPage }
]

const router = createRouter({
    history: createWebHashHistory(),
    routes
});
app.use(router);
DataTable.use(DataTablesLib);
DataTable.use(DataTablesCore); 
app.use(DataTable);
app.use(VueSweetalert2);
app.mount('#app');
