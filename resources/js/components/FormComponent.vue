<template>
    <div>
        <div class="mb-3">
            <label class="form-label">County</label>
            <select class="form-select" v-model="countySelected" :disabled="countys.length == 0 ? true : false">
                <option value="">Select County</option>
                <option v-for="county in countys" v-bind:value="county.d_estado" :key="county.d_estado">
                    {{ county.d_estado }}
                </option>
            </select>
        </div>
        <div class="mb-3">
            <label  class="form-label">Municipality</label>
            <select class="form-select" v-model="municipalitySelected" :disabled="municipalities.length == 0 ? true : false">
                <option value="">Select Municipality</option>
                <option v-for="municipality in municipalities" v-bind:value="municipality.D_mnpio" :key="municipality.D_mnpio">
                    {{ municipality.D_mnpio }}
                </option>
            </select>
        </div>
        <div class="row">
            <div class="col">
                <label  class="form-label">Order By</label>
                <select class="form-select" v-model="orderBy">
                    <option value="premium">Premium</option>
                    <option value="regular">Regular</option>
                    <option value="disiel">Disiel</option>
                </select>
            </div>
            <div class="col">
                <label  class="form-label">Order Direction</label>
                <select class="form-select" v-model="orderDirection">
                    <option value="ASC">ASC</option>
                    <option value="DESC">DESC</option>
                </select>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            countySelected: '',
            municipalitySelected: '',
            municipalities: [],
            loading: false,
            orderDirection: 'ASC',
            orderBy: 'premium'
        }
    },
    methods: {
        // Function to call the API 
        // this service get the municipalities
        getMunicipalities(){
            this.loading = true;
            this.$http.get(process.env.MIX_API + '/api/municipality?county=' + this.countySelected)
            .then( ({data}) => {
                this.municipalities = data.results
                this.loading = false
            })
            .catch((error) => {
                this.loading = false
                throw error
            })
        },
        // Function to call the API 
        // this service get the gas locations
        getGasLocations(){
            this.loading = true;
            this.$emit('loading', true)
            this.$http.get(
                process.env.MIX_API 
                + '/api/pricing?' 
                + 'county=' + this.countySelected 
                + '&municipality=' + this.municipalitySelected
                + '&orderDirection=' + this.orderDirection
                + '&orderBy=' + this.orderBy
            ).then(response => {
                // Check the status code from response
                // to emit the events to the parent
                if(response.status == 200){
                    this.$emit('dataTable', response.body.results)
                    this.$emit('loading', false)
                    this.loading = false
                } else if (response.status == 204) {
                    this.$emit('dataTable', [])
                    this.$emit('loading', false)
                    this.loading = false
                }

                // Emit the response status to parent
                this.$emit('responseStatus', response.status)
                
            }).catch((error) => {
                this.loading = false
                this.$emit('loading', false)
                this.$emit('errorServer', error.body.message)
            })
        }
    },
    watch:{
        countySelected: function(countySelected){
            this.municipalities = []
            this.municipalitySelected = '';
            this.getMunicipalities()
            this.getGasLocations()
        },
        // When the municipality input select change 
        // then, call the gas location function
        municipalitySelected: function(municipalitySelected){
            this.getGasLocations()
        },
        // When the orderBy input select change 
        // then, call the gas location function
        orderBy: function(orderBy){
            this.getGasLocations()
        },
        // When the orderDirection input select change 
        // then, call the gas location function
        orderDirection: function(orderDirection){
            this.getGasLocations()
        }
    },
    props: {
        countys: {required: true}
    }
}
</script>