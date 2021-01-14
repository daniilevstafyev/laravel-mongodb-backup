@extends('layouts.default')

@section('content')
<div class="container">
	<div id="error-msg" class="mt-5 alert alert-danger d-none" role="alert">
	</div>
	<div id="success-msg" class="mt-5 alert alert-success d-none" role="alert">
	</div>
	<div class="mt-5 row">
		<label for="prodClusterUrl" class="col-sm-3 col-form-label">Production Mongodb Cluster Url</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="prodClusterUrl" placeholder="example: mongodb+srv://<username>:<password>@<cluster-address-1>" required>
		</div>
	</div>
	<div class="mt-2 row">
		<label for="databaseName" class="col-sm-3 col-form-label">Database Name</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="databaseName" name="databaseName" placeholder="example: sample_analytics"  required>
		</div>
	</div>

	<div class="mt-5 row">
		<label for="devClusterUrl" class="col-sm-3 col-form-label">Development Mongodb Cluster Url</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="devClusterUrl" placeholder="example: mongodb+srv://<username>:<password>@<cluster-address-2>"  required>
		</div>
	</div>

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
		<form id="form-final" method="POST" action="/connect">
			@csrf
			<input type="hidden" id="prodClusterUrlInput" name="prodClusterUrl" />
			<input type="hidden" id="devClusterUrlInput" name="devClusterUrl" />
			<input type="hidden" id="developersInput" name="developers" />
			<input type="hidden" id="dbNameInput" name="dbName" />
			<button type="submit" id="btn-copy-data" class="btn btn-primary position-absolute start-50 translate-middle">Copy Data</button>
		</form>
	</div>
</div>
@endsection