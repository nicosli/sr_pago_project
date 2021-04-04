<template>
    <div>
        <div class="info-windows">
            <GmapMap 
                :center="{lat:25.5708219, lng:-100.665759}"
                :zoom="4"
                id="map" 
                ref="gmap">
                <GmapMarker
                    :key="index"
                    v-for="(m, index) in markers"
                    :position="m.position"
                    :clickable="true"
                    :draggable="false"
                />
            </GmapMap>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            showInfo: false,
            infoWindowContext: {
                position: {
                    lat: 25.0114527,
                    lng: -102.2358514
                }
            },
            markers: []
        }
    },
    methods: {
        toggleInfoWindow(context) {
            this.infoWIndowContext = context
            this.showInfo = true
        },
        infoClicked(context) {
            console.log(context)
        },
        getMarkers(){
            let m = [];
            var _this = this;
            if(this.locations)
            this.locations.forEach(function(item){
                m.push({
                    position:{
                        lat:parseFloat(item.latitude), 
                        lng:parseFloat(item.longitude)
                    },
                });
            });
            this.markers = m;
        },
        centerMap(){
            const bounds = new google.maps.LatLngBounds();
            for (let m of this.markers) {
                bounds.extend(m.position)
            }
            this.$refs.gmap.$mapObject.fitBounds(bounds)
        },
    },
    watch: {
        locations: function(location){
            this.getMarkers()
        },
        markers: function(markers){
            this.centerMap();
        }
    },
    props: {
        locations: {required:true}
    }
}
</script>

<style scoped>
#map{
    width: 100%;
    height: 400px;
}
</style>