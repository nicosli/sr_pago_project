<template>
    <div>
        <div v-show="loading">Loading table...</div>
        <div v-if="dataItems.length > 0 && !loading" class="alert alert-success" role="alert">
            {{ dataItems.length }} Gas Locations Results Found
        </div>
        

        <b-pagination
            v-if="dataItems.length > 10"
            v-model="currentPage"
            :total-rows="rows"
            :per-page="perPage"
            aria-controls="gasLocationsTable"
        ></b-pagination>

        <b-table 
            responsive
            id="gasLocationsTable"
            :per-page="perPage"
            :current-page="currentPage"
            striped 
            hover
            small
            head-variant="dark"
            :items="dataItems">
        </b-table>
    </div>
</template>

<script>
export default {
    data() {
        return {
            perPage: 10,
            currentPage: 1,
            dataItems:[]
        }
    },
    methods: {

    },
    watch:{
        dataTable: function(dataTable){
            this.dataItems = dataTable
        },
        loading: function(loading){
            if(loading == true)
                this.dataItems = []
        }
    },
    computed: {
      rows() {
        return this.dataItems.length
      }
    },
    props: {
        dataTable: {required: true},
        loading: {required: true}
    }
}
</script>