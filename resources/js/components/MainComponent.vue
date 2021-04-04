<template>
    <div>
        <div class="alert alert-danger" role="alert" v-if="errorMessage!='' && !loading">
            {{errorMessage}}
        </div>
        <div class="alert alert-warning" role="alert" v-if="responseStatus == 204 && !loading">
            <i class="fas fa-exclamation-circle"></i> Gas Locations Not Found
        </div>
        <div class="row align-items-start">
            <div class="col-6">
                <div class="card border-primary mb-3">
                    <div class="card-body text-primary">
                        <p class="card-text"><i class="fas fa-info-circle"></i> Select the a County in the select</p>
                    </div>
                </div>
                <form-component 
                    :countys="countys" 
                    @dataTable="dataTableHandler" 
                    @loading="loadingHandler" 
                    @errorServer="errorServerFunction"
                    @responseStatus="responseStatusHandler">
                </form-component>
            </div>
            <div class="col-6">
                <map-component :locations="dataTable"></map-component>
            </div>
            <div class="col-12">
                <table-component :dataTable=dataTable :loading="loading" class="mt-4"></table-component>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            loading:false,
            countys: [],
            dataTable: [],
            errorMessage: '',
            responseStatus: 200
        }
    },
    methods: {
        // Function to call the API 
        // this service get the countys
        getCountys: function(){
            this.$http.get(process.env.MIX_API + '/api/county')
            .then( ({data}) => {
                this.countys = data
                this.loading = false
            })
            .catch((error) => {
                this.loading = false
                throw error
            })
        },
        // Method to handle the dataTable var
        // this object is the main object with gas locations
        dataTableHandler(dataTable){
            this.dataTable = dataTable
        },

        // Method to handle the loading event
        loadingHandler(loading){
            this.loading = loading
        },

        // Method to handle the error events
        errorServerFunction(errorMessage){
            this.errorMessage = errorMessage
        },

        // Method to handle the status code of the responses
        responseStatusHandler(responseStatus){
            this.responseStatus = responseStatus
        }
    },
    mounted() {
        // First steep, retrieve the county data
        this.getCountys();
    }
}
</script>