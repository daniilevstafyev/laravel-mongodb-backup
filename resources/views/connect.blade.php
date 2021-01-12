@extends('layouts.default')

@section('content')
<div class="container">
	<div id="error-msg" class="mt-5 alert alert-danger d-none" role="alert">
	</div>
	<div id="success-msg" class="mt-5 alert alert-success d-none" role="alert">
	</div>
	<div class="mt-5 row">
		<label for="targetConnectionUrl" class="col-sm-3 col-form-label">Target Mongodb Connection Url</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="targetConnectionUrl" name="targetConnectionUrl" placeholder="example: mongodb+srv://<username>:<password>@<cluster-address-1>/test?retryWrites=true&w=majority" required>
		</div>
	</div>
	<div class="mt-5 row">
		<label for="destinationConnectionUrl" class="col-sm-3 col-form-label">Distination Mongodb Connection Url</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="destinationConnectionUrl" name="destinationConnectionUrl" placeholder="example: mongodb+srv://<username>:<password>@<cluster-address-2>/test?retryWrites=true&w=majority"  required>
		</div>
	</div>
	
	<div class="mt-5">
		<h3> Collections List </h3>
		<ul class="list-group" id="list-collections">
		</ul>
	</div>
	<form id="form-add-collection" class="row mt-2">
		<div class="col-sm-4">
			<label for="input-collection" class="visually-hidden">Collection Name</label>
			<input type="text" class="form-control" id="input-collection" placeholder="Collection Name. e.g. event" required>
		</div>
		<div class="col-sm-4">
			<button type="submit" class="btn btn-success mb-3">Add Collection</button>
		</div>
	</form>

	<div class="mt-5">
		<h3> Developers List </h3>
		<ul class="list-group" id="list-developers">
		</ul>
	</div>
	<form id="form-add-developer" class="row mt-2">
		<div class="col-sm-4">
			<label for="input-developer" class="visually-hidden">Developer Name</label>
			<input type="text" class="form-control" id="input-developer" placeholder="Developer Name. e.g. John" required>
		</div>
		<div class="col-sm-4">
			<button type="submit" class="btn btn-success mb-3">Add Developer</button>
		</div>
	</form>

	<div class="mt-5 position-relative">
		<button type="button" id="btn-copy-data" class="btn btn-primary position-absolute start-50 translate-middle">Copy Data</button>
	</div>
</div>
@endsection