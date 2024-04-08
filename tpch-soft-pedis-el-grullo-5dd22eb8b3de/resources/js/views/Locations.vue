<script setup>
import { onMounted, ref} from "vue";
import mapboxgl from 'mapbox-gl';
import viewTitle from "../components/global/viewTitle.vue";
import 'mapbox-gl/dist/mapbox-gl.css';

mapboxgl.accessToken = 'pk.eyJ1IjoiYWRjZWxwYXoiLCJhIjoiY2xyOGs3enp2Mnd3YzJrcGUzeHk1OG8xOSJ9.O_zRARvjs4jRF3qMzFdZ3A';

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

    function convertDMSToDD(coord, direction) {
        let degrees, minutes;
        if(coord.length !== 12){
            degrees = coord.substring(0,2);
            minutes = coord.substring(2,10)
        } else {
            degrees = coord.substring(0, 3);
            minutes = coord.substring(3, 11);
        }
        const decimal = parseFloat(degrees) + ((parseFloat(minutes)/60));
        return direction === 'N' || direction === 'E' ? decimal : -decimal;
    }

    function addMarkers(first_name, last_name, latitude, latitude_direction, longitude, longitude_direction, date, time, altitude){
        const latitudeD = convertDMSToDD(latitude, latitude_direction);
        const longitudeD = convertDMSToDD(longitude, longitude_direction);

        console.log('Date', date);
        console.log('Latitude: ', latitudeD, 'Longitude: ', longitudeD);
        const Marker = new mapboxgl.Marker().setLngLat([longitudeD, latitudeD]).addTo(map);

        Markers.push(Marker);

        const popupInfo = `
        <div>
            <h4>Detalles</h4>
            <p>Nombre: ${first_name}</p>
            <p>Apellido: ${last_name}</p>
            <p>Direccion de latitud: ${latitude_direction}</p>
            <p>Direccion de longitud: ${longitude_direction}</p>
            <p>Altitud: ${altitude}</p>
            <p>Fecha: ${date}</p>
            <p>Tiempo: ${time}</p>
       </div>
        `;

        //creates a popup for each marker
        const popup = new mapboxgl.Popup({ offset: 25 }).setHTML(popupInfo).addTo(map);
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
                addMarkers(location.first_name, location.last_name, location.latitude, location.latitude_direction, location.longitude, location.longitude_direction, location.date, location.time, location.altitude);
                console.log('Code reaches here.');
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
