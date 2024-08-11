<template>
  <div class="container">
        <div class="row">
            <div class="col-md-8">
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
        </div>
        <div class="row">
            <div class="col-md-8 mt-3">
                <div class="card">
                    <div class="card-header">
                        Buses
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-md-5">
                                <input type="text" placeholder="Buscar" class="form-control" v-model="searchValue">
                            </div>
                        </div>
                        <EasyDataTable
                            buttons-pagination
                            :headers="headers"
                            :items="buses"
                            theme-color="#0C81E6"
                            :loading="loading"
                            :search-value="searchValue"
                            alternating
                        >
                            <template #item-hab="item">
                                <div class="form-check form-switch" v-if="item.habilitado == 1  || item.habilitado == '1'">
                                    <input 
                                        style="height: 25px; width: 40px;" 
                                        type="checkbox" 
                                        class="form-check-input" 
                                        checked="checked"
                                        @change="cambiarEstado(item.id)"
                                    >
                                </div>
                                <div class="form-check form-switch" v-if="item.habilitado == 0 || item.habilitado == '0'">
                                    <input 
                                        style="height: 25px; width: 40px;" 
                                        type="checkbox" 
                                        class="form-check-input"
                                        @change="cambiarEstado(item.id)"
                                    >
                                </div>
                            </template>
                            <template #item-inhdia="item">
                                <VDatePicker v-model="item.fechas" color="blue" class="my-calendar">
                                    <template #default="{ togglePopover, inputValue }">
                                        <div class="input-group mb-3">
                                            <button 
                                            class="btn btn-outline-primary"
                                            @click="() => togglePopover()"><i class="fa-regular fa-calendar"></i></button>
                                            <input 
                                            type="text" 
                                            class="form-control"
                                            style="border: 1px solid blue;"
                                            disabled 
                                            :value="inputValue"
                                            >
                                            <button 
                                            class="btn btn-primary"
                                            ><i class="fa-solid fa-floppy-disk"></i></button>
                                        </div>
                                    </template>
                                </VDatePicker>
                            </template>
                            <template #item-btn="">
                                <VDatePicker color="blue" class="my-calendar" :attributes="attributes">
                                    <template #default="{ togglePopover }">
                                        <div class="input-group mb-3">
                                            <button 
                                            class="btn btn-outline-primary"
                                            @click="() => togglePopover()">Bus Calendar</button>
        
                                        </div>
                                    </template>
                                </VDatePicker>
                            </template>
                        </EasyDataTable>

                    </div>
                </div>
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
        form:{
            placa: '',
            cantidad: '',
        },
        buses: [],
        columns:[
          { data: 'placa', title: 'Placa' },
          { data: 'cantidad', title: 'Capacidad' },
        ],
        headers:[
            { text: "Habilitar", value: "hab" },
            { text: "Placa", value: "placa" },
            { text: "Capacidad", value: "cantidad" },
            { text: "Inhabilitar día", value: "inhdia" },
            { text: "...", value: "btn" },
        ],
        attributes: [
            {
                highlight: 'red',
                dates: [
                new Date(2022, 12, 2),
                new Date(2022, 12, 7),
                new Date(2022, 12, 17),
                new Date(2022, 12, 23),
                ],
                popover: {
                    label: 'En dia de pico y placa',
                },
            },
            {
                highlight: 'orange',
                dates: [
                new Date(2022, 12, 3),
                new Date(2022, 12, 9),
                new Date(2022, 12, 20),
                new Date(2022, 12, 31),
                ],
                popover: {
                    label: 'En mantenimiento',
                },
            },
        ],
        loading: false,
        searchValue: ''
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
        this.loading = true;
        axios.get('/buses/leer').then((response)=> {
        this.buses = response.data.buses;
        this.loading = false;
      })
      .catch((error)=> {
        this.loading = false;
        console.log('FAILURE!!');
      });  
    },
    cambiarEstado(id){
        const Toast = this.$swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        didOpen: (toast) => {
            toast.onmouseenter = this.$swal.stopTimer;
            toast.onmouseleave = this.$swal.resumeTimer;
        }
        });
        axios.get(`/buses/actualizar/${id}`).then((response)=> {
            if(response.data.status == 200){
                Toast.fire({
                icon: "success",
                title: response.data.msg
                });
                this.getBuses();
            }else if(response.data.status == 300){
                Toast.fire({
                icon: "warning",
                title: response.data.msg
                });
            }else{
                Toast.fire({
                icon: "error",
                title: "Error de sistema"
                });
            }
        //this.loading = false;
      })
      .catch((error)=> {
        //this.loading = false;
        console.log('FAILURE!!');
      });  
    }
  }
}
</script>

<style>
.my-calendar .vc-weekday-1, .my-calendar .vc-weekday-7 {
  color: #6366f1;
}
</style>