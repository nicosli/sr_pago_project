@extends('template.base')

@section('content')
<div id="app">
    <div class="container-fluid">
        <div class="card mt-3">
            <h5 class="card-header">Form Gas Location</h5>
            <div class="card-body">
                <div class="alert alert-info" role="alert">
                    <h4 class="alert-heading">Welcome!</h4>
                    <p>This website shows the gas stations by filling out the following form.</p>
                </div>
                <!-- Inject Main Component -->
                <main-component></main-component>
            </div>
        </div>
    </div>
</div>
@endsection