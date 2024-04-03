<script setup>
import { onMounted, ref} from "vue";
import mapboxgl from 'mapbox-gl';
import viewTitle from "../components/global/viewTitle.vue";
import 'mapbox-gl/dist/mapbox-gl.css';
import dynamicMenu from "../components/global/DynamicMenu.vue";
import citizensTable from "../components/citizens/Table.vue";

mapboxgl.accessToken = 'pk.eyJ1IjoiYWRjZWxwYXoiLCJhIjoiY2xyOGs3enp2Mnd3YzJrcGUzeHk1OG8xOSJ9.O_zRARvjs4jRF3qMzFdZ3A';
/*mapboxgl.accessToken = process.env.VUE_APP_MAPBOX_TOKEN;
    Don't understand why it isn't working like this.
*/
/*
console.log(process.env.VUE_APP_MAPBOX_TOKEN);
console.log(mapboxgl.accessToken);
*/
const mapContainer = ref(null);
const Markers = [];

onMounted(() => {
    const map = new mapboxgl.Map({
        container: mapContainer.value, //container ID
        style: 'mapbox://styles/mapbox/streets-v12', //style URL
        center: [-104.21805, 19.80603], //starting position
        zoom: 9 //inital zoom
    })

    fetchLocations();
    //addMarkers(location.latitude, location.latitude_direction, location.longitude, location.longitude_direction, location.altitude,location.date, location.time);

    function convertDMGToDD() {

    }

    function addMarkers(latitude, latitude_direction, longitude, longitude_direction, date, time, altitude){
        //const citizen = citizens.find(c => c.id === citizen_id);
        console.log('Latitude: ', latitude, 'Longitude: ', longitude);
        const Marker = new mapboxgl.Marker().setLngLat([latitude, longitude]).addTo(map.value);

        Markers.push(Marker);

        const popupInfo = `
        <div>
            <h4>Detalles</h4>
            <!--<p>Ciudadano: ${citizen.first_name}, ${citizen.last_name}</p>-->
            <p>Direccion de latitud: ${latitude_direction}</p>
            <p>Direccion de longitud: ${longitude_direction}</p>
            <p>Altitud: ${altitude}</p>
            <p>Fecha: ${date}</p>
            <p>Tiempo: ${time}</p>
       </div>
        `;

        //creates a popup for each marker
        const popup = new mapboxgl.Popup({ offset: 25 }).setHTML(popupInfo);
        Marker.setPopup(popup);
    }

    function clearMarkers(){
        Markers.forEach(markers => markers.remove());
        Markers.length = 0;
    }

    function fetchLocations(){
        //testing purposes
        fetch(`http://localhost:8000/api/locations`).then(response => {
            if(!response.ok) {
                throw new Error('There was an error');
            }
            return response.json();
        }).then(locations => {
            //clear any existing markers
            clearMarkers();
            locations.forEach(location => {
                addMarkers(location.latitude, location.latitude_direction, location.longitude, location.longitude_direction, location.date, location.time, location.altitude);
            });
        }).catch(error => {
            console.error('There was an error fetching the data', error);
        })
    }

});

</script>

<template>
    <div class="row">
        <div class="col-12">
            <view-title />
        </div>
        <div ref="mapContainer" class="mapContainer">
            <!--Map goes here-->
        </div>
    </div>
</template>

<style scoped>
.mapContainer {
    width: 100%;
    height: 800px;
}
</style>
