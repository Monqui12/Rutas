<template>
  <!--<div class="container">
    <EasyDataTable
    buttons-pagination
    :headers="headers"
    :items="datos"
    theme-color="#0C81E6"
    :loading="loading"
    :search-value="searchValue"
    :body-row-class-name="bodyRowClassNameFunction"
    alternating
                        >
        <template #item-condicion="item">
                <div v-if="item.condicion == 1">
                    <span class="badge rounded-pill text-bg-success">
                        <i class="fa-solid fa-check"></i>
                    </span>
                </div>
                <div v-if="item.condicion == 0">
                    <span class="badge rounded-pill text-bg-danger">
                        <i class="fa-solid fa-xmark"></i>
                    </span>
                </div>
            </template>                
    </EasyDataTable>
  </div>-->
  <div class="container">
      <div class="row">
        <div class="col-md-4" v-for="items in datos" :key="items">
            <h3 class=" row justify-content-center">Vehiculo: {{ items[0].placa }}</h3>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Tiempo de recorrido</th>
                        <th>Selección</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in items" :key="item">
                        <td> {{ item.duracion }}</td>
                        <td>
                            <div v-if="item.condicion == 1">
                                <span class="badge rounded-pill text-bg-success">
                                    <i class="fa-solid fa-check"></i>
                                </span>
                            </div>
                            <div v-if="item.condicion == 0">
                                <span class="badge rounded-pill text-bg-danger">
                                    <i class="fa-solid fa-xmark"></i>
                                </span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
      </div>
  </div>
</template>

<script>

export default {
    components:{
        
    },
    data(){
      return {
        datos: [],
        loading: false,
        columns:[
          { data: 'placa', title: 'Placa' },
          { data: 'cantidad', title: 'Capacidad' },
        ],
        headers:[
            { text: "Placa", value: "placa" },
            { text: "Tiempo de recorrido", value: "duracion" },
            { text: "Selección", value: "condicion" }
        ],
        searchValue: ''
      }
    },
    async mounted() {
        this.getData();
    },
    methods: {
        getData(){
            this.loading = true;
            axios.get('/rutas/reporte').then((response)=> {
                this.datos = response.data.datos;
                this.loading = false;
            })
            .catch((error)=> {
                this.loading = false;
                console.log('FAILURE!!');
            });  
        },
        BodyRowClassNameFunction(item){
            if (item.condicion = 0){
                return 'rojo';
            }else{
                return 'verde';
            }
        }
    }
}
</script>

<style>
    .rojo{
        background-color: red;
    }
    .verde{
        background-color: green;
    }
</style>