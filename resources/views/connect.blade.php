@extends('layouts.default')

@section('content')
<div class="container">
	<form method="POST" action="/connect">
		@csrf
		<div class="row">
			@if(isset($error))
				<div class="mt-5 alert alert-danger" role="alert">
					{{ $error }}
				</div>
			@endif
			<div class="mt-5">
				<label for="targetConnectionUrl" class="form-label">Target Mongodb Connection Url</label>
				<input type="text" class="form-control" id="targetConnectionUrl" name="targetConnectionUrl" placeholder="example: mongodb+srv://<username>:<password>@<cluster-address-1>/test?retryWrites=true&w=majority" required>
			</div>
			<div class="mt-5">
				<label for="destinationConnectionUrl" class="form-label">Distination Mongodb Connection Url</label>
				<input type="text" class="form-control" id="destinationConnectionUrl" name="destinationConnectionUrl" placeholder="example: mongodb+srv://<username>:<password>@<cluster-address-2>/test?retryWrites=true&w=majority"  required>
			</div>
			<div class="mt-5 position-relative">
				<button type="submit" class="btn btn-primary position-absolute start-50 translate-middle">Copy Data</button>
			</div>
		</div>
	</form>
</div>
@endsection