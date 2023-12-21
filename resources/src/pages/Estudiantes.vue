<template>
  <div class="container">
    <div class="card">
      <div class="card-header">
        Cargar Estudiantes
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-4">
            <label for="">Archivo</label>
            <input type="file" class="form-control" accept=".xlsx, .xls" name="file" id="file" ref="file">
          </div>
          <div class="col-md-2">
            <label for="">.</label>
            <button class="btn btn-primary form-control" @click="cargar()">Cargar</button>
          </div>
        </div>
      </div>
    </div>
    <div class="card" style="margin-top: 2%;">
      <div class="card-header">
        Estudiantes Cargados
      </div>
      <div class="card-body">
        <div>
          <div class="row">
            <div class="col-md-6">
              <DataTable style="border: 5px;" class="table table-hover table-responsive" ref="table" :data="estudiantes" :columns="columns">
                  <thead class="table-light">
                      <tr>
                          <th>Identificación</th>
                          <th>Nombre</th>
                          <th>Direccion</th>
                      </tr>
                  </thead>
              </DataTable>
            </div>
            <div class="col-md-6">
                <div :style="{'width': '100%', 'height': '100%'}" class="google-map" ref="googleMap" id="map"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div v-for="error in errores" :key="error" class="alert alert-warning" role="alert">
      {{ error.msg }}
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
        file: '',
        estudiantes: [],
        columns:[
          { data: 'identificacion', title: 'Identificacion' },
          { data: 'nombres', title: 'Nombres' },
          { data: 'direccion', title: 'Dirección' },
        ],
        map: null,
        geocoder: null,
        responseDiv: null,
        response: null,
        errores: []
      }
    },
  async mounted() {
    this.getEstudiantes();
    window.initMap = this.initMap();
  },
  methods: {
    initMap() {
      let map = new google.maps.Map(document.getElementById("map"), {
        zoom: 12,
        center: { lat: 4.695157, lng: -74.079675 },
        mapTypeControl: false,
      });
      this.geocoder = new google.maps.Geocoder();
      this.marker = new google.maps.Marker({
        map,
      });
      this.map = map;
      return false;
      const inputText = document.createElement("input");

      inputText.type = "text";
      inputText.placeholder = "Enter a location";

      const submitButton = document.createElement("input");

      submitButton.type = "button";
      submitButton.value = "Geocode";
      submitButton.classList.add("button", "button-primary");

      const clearButton = document.createElement("input");

      clearButton.type = "button";
      clearButton.value = "Clear";
      clearButton.classList.add("button", "button-secondary");
      response = document.createElement("pre");
      response.id = "response";
      response.innerText = "";
      responseDiv = document.createElement("div");
      responseDiv.id = "response-container";
      responseDiv.appendChild(response);

      const instructionsElement = document.createElement("p");

      instructionsElement.id = "instructions";
      instructionsElement.innerHTML =
        "<strong>Instructions</strong>: Enter an address in the textbox to geocode or click on the map to reverse geocode.";
      map.controls[google.maps.ControlPosition.TOP_LEFT].push(inputText);
      map.controls[google.maps.ControlPosition.TOP_LEFT].push(submitButton);
      map.controls[google.maps.ControlPosition.TOP_LEFT].push(clearButton);
      map.controls[google.maps.ControlPosition.LEFT_TOP].push(instructionsElement);
      map.controls[google.maps.ControlPosition.LEFT_TOP].push(responseDiv);
      marker = new google.maps.Marker({
        map,
      });
      map.addListener("click", (e) => {
        geocode({ location: e.latLng });
      });
      submitButton.addEventListener("click", () =>
        geocode({ address: inputText.value }),
      );
      clearButton.addEventListener("click", () => {
        clear();
      });
      clear();
    },
    geocode(request){
      this.geocoder
      .geocode(request)
      .then((result) => {
        const { results } = result;
        //this.map.setCenter(results[0].geometry.location);
        //this.marker.setPosition(results[0].geometry.location);
        //this.marker.setMap(this.map);
        let response = JSON.stringify(results[0]);
        response = JSON.parse(response);
        //console.log(this.response.geometry.location.lat)
        //console.log(JSON.stringify(results[0].geometry.location));
        return results;
      })
      .catch((e) => {
        alert("Geocode was not successful for the following reason: " + e);
      });
    },
    deleteMarkers(markersArray){
      for (let i = 0; i < markersArray.length; i++) {
        markersArray[i].setMap(null);
      }

      markersArray = [];
    },
    cargar(){
      this.$swal.showLoading();
      this.file = this.$refs.file.files[0];
      let formData = new FormData();
      formData.append('file', this.file);
      axios.post('/estudiantes/cargar',
      formData,
      {
        headers: {
            'Content-Type': 'multipart/form-data'
        }
      }
      ).then((response)=> {
        this.$swal.hideLoading();
        this.$swal.fire({
          position: "top-end",
          icon: "success",
          title: "Importación completa",
          showConfirmButton: false,
          timer: 1500
        });
      })
      .catch((error)=> {
        this.$swal.hideLoading();
        console.log('FAILURE!!');
      });
    },
    getEstudiantes(){
      axios.get('/estudiantes/leer').then((response)=> {
        this.estudiantes = response.data.estudiantes;
        console.log(this.estudiantes);
        let map = this.map;
        this.estudiantes.forEach(element => {
          this.geocoder
          .geocode({ 'address': element.direccion})
          .then((result) => {
            const { results } = result;
            //this.map.setCenter(results[0].geometry.location);
            //this.marker.setPosition(results[0].geometry.location);
            //this.marker.setMap(this.map);
            new google.maps.Marker({
              position: results[0].geometry.location,
              map,
              title: element.nombres,
            });
            let response = JSON.stringify(results[0]);
            response = JSON.parse(response);
            //console.log(this.response.geometry.location.lat)
            console.log(JSON.stringify(results[0].geometry.location));
          })
          .catch((e) => {
            this.errores.push({
              msg: 'No se encontro una localización para la dirección ' + element.direccion + ', para el estudiante ' + element.nombres + ' identificado con numero ' + element.identificacion
            });
          });
        });
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