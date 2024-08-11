import './bootstrap';
import { createApp } from 'vue';
import { createRouter, createWebHashHistory } from 'vue-router'
import DataTable from 'datatables.net-vue3';
import DataTablesLib from 'datatables.net-bs5';
import DataTablesCore from 'datatables.net-bs5';
import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
import 'datatables.net-select-bs5';
import Vue3EasyDataTable from 'vue3-easy-data-table';
import 'vue3-easy-data-table/dist/style.css';
import { setupCalendar, Calendar, DatePicker } from 'v-calendar';
import 'v-calendar/style.css';


const app = createApp({});

import ExampleComponent from './components/ExampleComponent.vue';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
//app.component('example-component', ExampleComponent);

import EstudiantesPage from './../src/pages/Estudiantes.vue';
import BusesPage from './../src/pages/buses.vue';
import RutasPage from './../src/pages/rutas.vue';
import ReportePage from './../src/pages/reporte.vue';

const routes = [
    { path: '/', component: '' },
    { path: '/home', component: ExampleComponent },
    { path: '/estudiantes', component: EstudiantesPage },
    { path: '/buses', component: BusesPage },
    { path: '/rutas', component: RutasPage },
    { path: '/reporte', component: ReportePage }
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
app.component('EasyDataTable', Vue3EasyDataTable);
app.component('VueDatePicker', VueDatePicker);
app.use(setupCalendar, {})
app.component('VCalendar', Calendar)
app.component('VDatePicker', DatePicker)
app.mount('#app');
