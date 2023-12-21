<template>
  <div class="container">
        <h3>Buses</h3>
        <div class="row">
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header">
                        Crear Bus
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-5">
                                <label for="">Placa</label>
                                <input type="text" class="form-control" v-model="form.placa">
                            </div>
                            <div class="col-md-4">
                                <label for="">Capacidad de pasajeros</label>
                                <input type="number" class="form-control" v-model="form.cantidad">
                            </div>
                            <div class="col-md-2">
                                <label for="" style="color: white;">.</label>
                                <button class="form-control btn btn-primary" @click="save()">Crear</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <DataTable class="display table table-striped" ref="table" :data="buses" :columns="columns">
                    <thead>
                        <tr>
                            <th>Placa</th>
                            <th>Cantidad</th>
                        </tr>
                    </thead>
                </DataTable>
            </div>
        </div>
  </div>
</template>

<script>
import DataTable from 'datatables.net-vue3';
export default {
    components:{
    DataTable
  },
  data(){
      return {
        form:{
            placa: '',
            cantidad: '',
        },
        buses: [],
        columns:[
          { data: 'placa', title: 'Placa' },
          { data: 'cantidad', title: 'Capacidad' },
        ],
      }
    },
  async mounted() {
    this.getBuses();
  },
  methods: {
    save(){
        this.$swal.showLoading();
        let formData = new FormData();
        formData.append('placa', this.form.placa);
        formData.append('cantidad', this.form.cantidad);
        axios.post('/buses/save', formData).then((response)=> {
            this.getBuses();
            this.$swal.hideLoading();
            this.$swal.fire({
            position: "top-end",
            icon: "success",
            title: "Creado correctamente",
            showConfirmButton: false,
            timer: 1500
            });
        })
        .catch((error)=> {
            this.$swal.hideLoading();
            console.log('FAILURE!!');
        });
    },
    getBuses(){
        axios.get('/buses/leer').then((response)=> {
        this.buses = response.data.buses;
      })
      .catch((error)=> {
        console.log('FAILURE!!');
      });  
    }
  }
}
</script>

<style>

</style>