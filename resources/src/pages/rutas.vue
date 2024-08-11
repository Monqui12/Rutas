<template>
  <div class="container" style="width: 100%; height: 100%;">
    <div>
        <h2 class="row justify-content-center">Rutas Óptimas</h2>
    </div>
    <div class="btn-group" role="group" aria-label="Basic mixed styles example">
        <button type="button" class="btn btn-danger" @click="generar()">Generar Rutas</button>
        <button type="button" class="btn btn-warning" @click="pintar()">Pintar Rutas</button>
        <button type="button" class="btn btn-success" @click="cargarRutasManual()">Cargar Rutas Manual</button>
    </div>
    <!--<div v-for="estudiante in estudiantes" :key="estudiante">
        <div v-if="estudiante.length <= 24">{{ estudiante[0].localidad }} <p :id="'valor' + estudiante[0].id "></p></div>
        <div class="row" v-if="estudiante.length <= 24">
            <div class="col-md-6">
                <div :style="{'width': '100%', 'height': '600px'}" class="google-map" ref="googleMap" :id="'map' + estudiante[0].id"></div>
            </div>
            <div class="col-md-6">
                <div :style="{'width': '100%'}"  :id="'indicators'+ estudiante[0].id"></div>
            </div>
        </div>
    </div>-->
    <div v-for="ruta in rutas" :key="ruta">
        <div class="card mt-3">
            <div class="card-body">
                <h3 class="card-title text-center">Vehículo: <b>{{ ruta[0].placa }}</b></h3>
                <div class="row" >
                    <div class="col-md-6">
                        <div :style="{'width': '100%', 'height': '600px'}" class="google-map" ref="googleMap" :id="'map' + ruta[0].id_bus"></div>
                    </div>
                    <div class="col-md-6">
                        <div style="display: none">{{  i = 1 }}</div>
                        <div :id="'comentarios' + ruta[0].id_bus"></div>
                        <p :id="'valor' + ruta[0].id_bus "></p>
                        <div class="alert alert-primary" role="alert" :style="{'width': '100%', 'overflow-y': 'scroll', 'height':'300px'}">
                            <div v-for="item in ruta" :key="item.id">
                                Parada {{ i++ }}: {{ item.nombres }} ir a dirección {{ item.direccion }}
                                <br/>
                            </div>
                        </div>
                        <div :style="{'width': '100%', 'overflow-y': 'scroll', 'height':'250px'}"  :id="'indicators'+ ruta[0].id_bus"></div>
                        <!--<div :style="{'width': '100%', 'height': '600px'}" class="google-map" ref="googleMap" :id="'mapSeg' + ruta[0].id_bus"></div>-->
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>
</template>
<script>
export default {
  data(){
      return {
        map: null,
        geocoder: new google.maps.Geocoder(),
        responseDiv: null,
        response: null,
        directionsService: new google.maps.DirectionsService(),
        directionsDisplay: null,
        origin: { lat: 4.658383846282959, lng: -74.09394073486328 },
        destination: { lat: 4.676802158355713, lng: -74.04825592041016 },
        wayPoints: [],
        estudiantes: [],
        buses: [],
        busEstudiante: [],
        rutas: [],
        buscarIgualmente: 0,
        valorTiempo: 0
      }
    },
    async mounted() {
        //window.initMap = this.initMap();
        //this.getEstudiantes();
        //this.calcularRuta();
        //this.leerRutas();
    },
    methods: {
        generar(){
            this.calcularRuta();
        },
        pintar(){
            this.leerRutas();
        },
        initMap() {
        let map = new google.maps.Map(document.getElementById("map"), {
            zoom: 12,
            center: this.origin,
            mapTypeControl: false,
        });
        this.marker = new google.maps.Marker({
            map,
        });
        this.map = map;
        this.directionsDisplay = new google.maps.DirectionsRenderer({map:map});
        this.directionsDisplay.setPanel(document.getElementById("indicators"));
        
        },
        calculateRoute(wayPoints, id) {
            let map = new google.maps.Map(document.getElementById("map" + id), {
                zoom: 12,
                center: this.origin,
                mapTypeControl: false,
            });
            this.marker = new google.maps.Marker({
                map,
            });
            this.directionsService.route({
                origin: this.origin,
                destination: this.origin,
                waypoints: wayPoints,
                optimizeWaypoints: true,
                travelMode: google.maps.TravelMode.DRIVING,
            }, (response, status)  => {
                let valor = 0;
                response.routes[0].legs.forEach(element => {
                    valor = element.duration.value + valor;
                });
                if (status === google.maps.DirectionsStatus.OK) {
                    let directionsDisplay = new google.maps.DirectionsRenderer({map:map});
                    directionsDisplay.setDirections(response);
                    directionsDisplay.setPanel(document.getElementById("indicators" + id));
                    let html = document.getElementById("valor" + id).innerHTML = 'Duración en ruta: ' + valor;

                } else {
                    alert('Could not display directions due to: ' + status);
                }
            });
        },
        geocode(request){
        this.geocoder
        .geocode(request)
        .then((result) => {
            const { results } = result;
            let response = JSON.stringify(results[0]);
            response = JSON.parse(response);
            console.log(response);
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
        getEstudiantes(){
        axios.get('/rutas/estudiantes').then((response)=> {
            this.estudiantes = response.data.estudiantes;
            Object.entries(this.estudiantes).forEach(([key, value]) => {
                if(value.length <= 60){
                    setTimeout(()=> {
                        //this.initMap1(value[0].id);
                        this.getWayPoints(value, value[0].id);
                    }, 2000);
                }
            });
        })
        .catch((error)=> {
            console.log('FAILURE!! ' + error);
        });
        },
        getWayPoints(value, id){
            let wayPoints = [];
            value.forEach((element, index) => {
            this.geocoder
            .geocode({ 'address': element.direccion})
            .then((result) => {
                const { results } = result;
                let response = JSON.stringify(results[0]);
                response = JSON.parse(response);
                response.address_components.forEach(data => {
                    //console.log(data.long_name, element.localidad);
                    if(element.localidad == 'CHAPINERO'){
                        console.log(this.removeAccents(data.long_name.toLowerCase().replace(" ", "")));
                    }
                    if(this.removeAccents(data.long_name.toLowerCase().replace(" ", "")) == this.removeAccents(element.localidad.toLowerCase().replace(" ", ""))){
                        wayPoints.push({
                            location: { lat: response.geometry.location.lat, lng: response.geometry.location.lng },
                            stopover: true,
                        });
                    }
                });
                if(value.length -1 == index){
                    this.calculateRoute(wayPoints, id);
                }
            })
            .catch((e) => {
                console.log("Geocode was not successful for the following reason: " + e);
                //alert("Geocode was not successful for the following reason: " + e);
            });
        });
        },
        initMap1(id) {
        let map = new google.maps.Map(document.getElementById("map" + id), {
            zoom: 12,
            center: this.origin,
            mapTypeControl: false,
        });
        },
        removeAccents(str){
            return str.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
        },
        async calcularRutaValidado(response){
            //console.log(response);
            this.estudiantes = response.data.estudiantes;
            this.buses = response.data.buses;
            let array = [];
            console.log("123");
            await this.resolveAfter2Seconds();
            console.log(this.estudiantes);
            //return false;
            for (let i = 0; i < this.buses.length; i++) {
                this.valorTiempo = 0;
                await this.calcularColegioPunto().then(async res => {
                    console.log('Primer punto', Object.keys(res).length);
                    if(Object.keys(res).length > 0){
                        this.estudiantes = this.estudiantes.filter(r => r.id != res.estudiante.id);
                        this.busEstudiante.push({
                            estudiante: res.estudiante,
                            bus: this.buses[i],
                            duration: res.distancia
                        });
                        this.valorTiempo = this.valorTiempo + res.distancia;
                        console.log(this.valorTiempo);
                        for (let p = 0; p < this.buses[i].cantidad; p++) {
                            await this.calcularTiempo().then(estudianteCercano=> {
                                console.log('Cercano', Object.keys(estudianteCercano).length);
                                console.log('Estudiante' ,this.estudiantes.length);
                                if(this.estudiantes.length > 0 && Object.keys(estudianteCercano).length > 0){
                                    this.estudiantes = this.estudiantes.filter(r => r.id != estudianteCercano.estudiante.id);
                                    this.busEstudiante.push({
                                        estudiante: estudianteCercano.estudiante,
                                        bus: this.buses[i],
                                        duration: estudianteCercano.distancia
                                    });
                                    this.valorTiempo += estudianteCercano.distancia;
                                }
                                //console.log(estudianteCercano.distancia);
                            });
                            console.log(this.valorTiempo, "=> ", this.buses[i]);
                            if(this.valorTiempo >= 7200){
                                //console.log("Termina por tiempo ", this.valorTiempo);
                                break;
                            }
                        }
                    }
                });
            }
            this.cargarRutas();
            return false;
            /*this.estudiantes.unshift({
                'location': {'lat': 4.804691030951561, 'lng': -74.06343538028327},
                'id': 1
            });*/
            let inicio = {'location': {'lat': 4.804691030951561, 'lng': -74.06343538028327}};
            
            //4.804691030951561, -74.06343538028327
        },
        async calcularRuta(){
            this.busEstudiante = [];
            axios.get('/rutas/estudiantes').then(async (response)=> {
                if(response.data.status == 200){
                    this.calcularRutaValidado(response);
                }else if(response.data.status == 300){
                    this.$swal.fire({
                    title: response.data.msg,
                    //showDenyButton: true,
                    //showCancelButton: true,
                    confirmButtonText: "Aceptar"
                    }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        //Swal.fire("Saved!", "", "success");
                    } else if (result.isDenied) {
                        //Swal.fire("Changes are not saved", "", "info");
                    }
                    });
                }else if(response.data.status == 400){
                    this.$swal.fire({
                    title: response.data.msg + ", Desea continuar con el proceso",
                    showDenyButton: true,
                    confirmButtonText: "Si",
                    denyButtonText: `No`
                    }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        this.calcularRutaValidado(response);
                    } else if (result.isDenied) {
                        this.buscarIgualmente = 0;
                    }
                    });
                }
            })
            .catch((error)=> {
                console.log('FAILURE!! ' + error);
            });
         
        }, 
        cargarRutasManual(){
            this.cargarRutas();
        },
        cargarRutas(){
            this.$swal.showLoading();
            let formData = new FormData();
            formData.append('buses', this.busEstudiante);
            formData.append('estudiantes', this.estudiantes);
            axios.post('/rutas/cargar', {'buses': this.busEstudiante}).then((response)=> {
                this.$swal.hideLoading();
                this.$swal.fire({
                position: "top-end",
                icon: "success",
                title: "Rutas Creadas Correctamente",
                showConfirmButton: false,
                timer: 1500
                });
            })
            .catch((error)=> {
                this.$swal.hideLoading();
                console.log('FAILURE!!');
            });
        },
        async calcularTiempo(inicio){
            return new Promise(async (resolve) => {
                let punto = [];
                if(this.estudiantes.length == 0){
                    resolve(punto);
                }
                for (let i = 0; i < this.estudiantes.length; i++) {
                    //if(this.estudiantes[i].location){
                        try {
                            await this.directionsService.route({
                                origin: this.busEstudiante[this.busEstudiante.length - 1].estudiante.location,
                                destination: this.estudiantes[i].location,
                                //waypoints: wayPoints,
                                optimizeWaypoints: true,
                                travelMode: google.maps.TravelMode.DRIVING,
                            }, (response, status)  => {
                                if (status === google.maps.DirectionsStatus.OK) {
                                    if(response.routes.length > 0){
                                        if(i == 0){
                                            punto = {
                                                'estudiante': this.estudiantes[i],
                                                'index': i,
                                                'distancia': response.routes[0].legs[0].duration.value
                                            };
                                        }else{
                                            if(punto.distancia > response.routes[0].legs[0].duration.value){
                                                punto = {
                                                    'estudiante': this.estudiantes[i],
                                                    'index': i,
                                                    'distancia': response.routes[0].legs[0].duration.value
                                                };
                                            }
                                        }
                                    }
                                } else {
                                    //alert('Could not display directions due to: ' + status);
                                    console.log('Could not display directions due to: ' + status)
                                }
                                if(this.estudiantes.length-1 == i){
                                    resolve(punto);
                                }
                            });
                        } catch (e) {
                            console.log('Could not display directions due to: ' + e)
                        }
                    //}else{
                        if(this.estudiantes.length-1 == i){
                            resolve(punto);
                        }
                    //}
                }
            });
        },
        async calcularColegioPunto(){
            return new Promise(async (resolve) => {
                let punto = [];
                if(this.estudiantes.length == 0){
                    resolve(punto);
                }
                
                    for (let i = 0; i < this.estudiantes.length; i++) {
                        //if(this.estudiantes[i].location){
                            //console.log(this.estudiantes[i].location);
                            try {
                                await this.directionsService.route({
                                    origin: {lat: 4.804691030951561, 'lng': -74.06343538028327},
                                    destination: this.estudiantes[i].location,
                                    //waypoints: wayPoints,
                                    optimizeWaypoints: true,
                                    travelMode: google.maps.TravelMode.DRIVING,
                                }, (response, status)  => {
                                    let valor = 0;
                                    if(this.estudiantes.length-1 == i){
                                        resolve(punto);
                                    }
                                    if (status === google.maps.DirectionsStatus.OK) {
                                        if(response.routes.length > 0){
                                            if(i == 0){
                                                punto = {
                                                    'estudiante': this.estudiantes[i],
                                                    'index': i,
                                                    'distancia': response.routes[0].legs[0].duration.value
                                                };
                                            }else{
                                                if(punto.distancia > response.routes[0].legs[0].duration.value){
                                                    punto = {
                                                        'estudiante': this.estudiantes[i],
                                                        'index': i,
                                                        'distancia': response.routes[0].legs[0].duration.value
                                                    };
                                                }
                                            }
                                        }else{
            
                                        }
                                    } else {
                                        //alert('Could not display directions due to: ' + status);
                                        console.log('Could not display directions due to: ' + status);
                                    }
                                    //4.804691030951561, -74.06343538028327
                                    
                                });
                            } catch (e) {
                                console.log('Could not display directions due to: ' + e);
                            }
                        //}else{
                        if(this.estudiantes.length-1 == i){
                            resolve(punto);
                        }
                        //}
                    }
            });
        },
        async resolveAfter2Seconds() {
            return new Promise((resolve) => {
                this.estudiantes.forEach(async (element, index) => {
                   this.estudiantes[index].location =  { lat: parseFloat(element.latitud), lng: parseFloat(element.longitud) };
                   if(this.estudiantes.length -1 == index){
                        resolve(true);
                    }
                });
            });
            /*return new Promise((resolve) => {
                this.estudiantes.forEach(async (element, index) => {
                    await this.geocoder
                    .geocode({ 'address': element.direccion})
                    .then((result) => {
                        const { results } = result;
                        let response = JSON.stringify(results[0]);
                        response = JSON.parse(response);
                        this.estudiantes[index].location =  { lat: response.geometry.location.lat, lng: response.geometry.location.lng };
                    })
                    .catch((e) => {
                        console.log("Geocode was not successful for the following reason: " + e);
                        //alert("Geocode was not successful for the following reason: " + e);
                    });
                    if(this.estudiantes.length -1 == index){
                        resolve(true);
                    }
                });
            });*/
        },
        calculateRoute1(wayPoints, id) {
            let map = new google.maps.Map(document.getElementById("map" + id), {
                zoom: 12,
                center: {lat: 4.804691030951561, 'lng': -74.06343538028327},
                mapTypeControl: false,
            });
            this.marker = new google.maps.Marker({
                map,
            });
            const image =
                "http://127.0.0.1:8000/colegio.png";
            const beachMarker = new google.maps.Marker({
                position: {lat: 4.804691030951561, 'lng': -74.06343538028327},
                map,
                icon: {
                    url: image
                }
            });
            this.directionsService.route({
                origin: {lat: 4.804691030951561, 'lng': -74.06343538028327},
                destination: {lat: 4.804691030951561, 'lng': -74.06343538028327},
                waypoints: wayPoints,
                optimizeWaypoints: true,
                travelMode: google.maps.TravelMode.DRIVING,
            }, (response, status)  => {
                let valor = 0;
                response.routes[0].legs.forEach(element => {
                    valor = element.duration.value + valor;
                });
                if (status === google.maps.DirectionsStatus.OK) {
                    let directionsDisplay = new google.maps.DirectionsRenderer(
                    {
                        map:map,
                        suppressMarkers: false,
                        preserveViewport: true,
                    }
                    );
                    directionsDisplay.setDirections(response);
                    directionsDisplay.setPanel(document.getElementById("indicators" + id));
                    let html = document.getElementById("valor" + id).innerHTML = 'Duración en ruta: ' + parseInt(valor/60) + " Minutos";

                } else {
                    alert('Could not display directions due to: ' + status);
                }
            });
        },
        leerRutas(){
            this.$swal.showLoading();
            axios.post('/rutas/leerRutas', {}).then((response)=> {
                if(response.data.status == 200){
                    this.rutas = response.data.rutas;
                    setTimeout(()=> {
                        Object.entries(response.data.rutas).forEach(async ([key, value]) => {
                            let wayPoints = [];
                            //valor comeinza
                            /*
                                let map = new google.maps.Map(document.getElementById("mapSeg" + value[0].id_bus), {
                                    zoom: 12,
                                    center: {lat: 4.804691030951561, 'lng': -74.06343538028327},
                                    mapTypeControl: false,
                                });
                                this.marker = new google.maps.Marker({
                                    map,
                                });
                                this.directionsService.route({
                                    origin: {lat: 4.804691030951561, 'lng': -74.06343538028327},
                                    destination: {'lat': parseFloat(value[0].lat), 'lng': parseFloat(value[0].lng)},
                                    //waypoints: wayPoints,
                                    optimizeWaypoints: true,
                                    travelMode: google.maps.TravelMode.DRIVING,
                                }, (response, status)  => {
                                    if (status === google.maps.DirectionsStatus.OK) {
                                        let directionsDisplay = new google.maps.DirectionsRenderer({
                                            suppressMarkers: false,
                                            preserveViewport: true,
                                        });
                                        directionsDisplay.setMap(map);
                                        directionsDisplay.setDirections(response);
                                    } else {
                                        alert('Could not display directions due to: ' + status);
                                    }
                                });
                                */
                                //valor finaliza
                                value.forEach(async (element, llave) => {
                                //valor comeinza
                                /*
                                new google.maps.Marker({
                                    position: {'lat': parseFloat(element.lat), 'lng': parseFloat(element.lng)},
                                    map: map,
                                    label: llave.toString()
                                });
                                if(value.length -2 >= llave){
                                    this.directionsService.route({
                                        origin: {'lat': parseFloat(element.lat), 'lng': parseFloat(element.lng)},
                                        destination: {'lat': parseFloat(value[llave+1].lat), 'lng': parseFloat(value[llave+1].lng)},
                                        //waypoints: wayPoints,
                                        optimizeWaypoints: true,
                                        travelMode: google.maps.TravelMode.DRIVING,
                                    }, (response, status)  => {
                                        if (status === google.maps.DirectionsStatus.OK) {
                                            let directionsDisplay = new google.maps.DirectionsRenderer({
                                                suppressMarkers: true,
                                                preserveViewport: false,
                                            });
                                            directionsDisplay.setMap(map);
                                            directionsDisplay.setDirections(response);
                                        } else {
                                            alert('Could not display directions due to: ' + status);
                                        }
                                    });
                                }
                                */
                                //valor finaliza
                                wayPoints.push({
                                    location: { lat: parseFloat(element.lat), lng: parseFloat(element.lng) },
                                    stopover: true,
                                });
                                if(value.length -1 == llave){
                                    console.log(wayPoints);
                                    document.getElementById("comentarios" + element.id_bus).innerHTML = 'Estudiantes: ' + value.length
                                    this.calculateRoute1(wayPoints, element.id_bus);
                                }
                            });
                        });
                    }, 2000);
                    
                    this.$swal.hideLoading();
                    this.$swal.fire({
                    position: "top-end",
                    icon: "success",
                    title: "Rutas Creadas Correctamente",
                    showConfirmButton: false,
                    timer: 1500
                    });
                }else if(response.data.status == 300){
                    this.$swal.fire({
                        position: "top-end",
                        icon: "warning",
                        title: response.data.msg,
                        showConfirmButton: false,
                        timer: 1500
                    });
                }else{
                    this.$swal.fire({
                        position: "top-end",
                        icon: "error",
                        title: 'Error de sistema!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            })
            .catch((error)=> {
                this.$swal.hideLoading();
                console.log('FAILURE!!' + error);
            });  
        },
    }
}
</script>

<style>

</style>