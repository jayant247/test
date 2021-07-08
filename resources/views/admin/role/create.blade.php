@extends('layouts.layout')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
    @if ($errors->any())

        <div class="alert alert-danger">

            <strong>Whoops!</strong> There were some problems with your input.<br><br>

        </div>

    @endif

        <div class="container-fluid">
		    <div class="row">
		        <div class="col-lg-12">
		            <div class="card">
		                <div class="card-body">
		                    <h4 class="card-title">Add Role</h4>
		                    <div class="basic-form">
		                        <form enctype="multipart/form-data" action="{{ route('role.store') }}" method="POST">
		                        	@csrf
		                        	<div class="row">
			                        	<div class="form-group col-md-4">
			                            	<label>Name*</label>
			                                <input type="text" name="name" class="form-control input-default" placeholder="Enter Role Name">
			                                @if($errors->has('name'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('name') }}
							                    </p>
							                @endif
			                            </div>

			                            <div class="form-group col-md-4">
			                            	<label>Guard Name</label>
			                                <input type="text" name="guard_name" class="form-control input-default" placeholder="Enter Guard Name">
			                                @if($errors->has('guard_name'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('guard_name') }}
							                    </p>
							                @endif
			                            </div>			                            
		                        	</div>
		                            

								    <div class="row">
			                        	<!-- <div class="form-group col-md-12">
			                            	<label>Permissions</label>
			                            	<br/>
			                            	<div class="row">
			                            	@foreach($permissions as $value)
			                            	<div class="col-md-3">
								                <label>{{ Form::checkbox('permission[]', $value->id, false, array('class' => 'name')) }}
								                {{ $value->name }}</label>
								            </div>
								            <br/>
								            @endforeach
								        </div>
								            @if($errors->has('permissions'))
							                    <p class="help-block">
							                        {{ $errors->first('permissions') }}
							                    </p>
							                @endif
							            </div> -->

							            <div class="form-group col-md-6" id="permission_class">
							                <label>Seelct Permissions*</label>
			                                <select class="form-control select2" id="permissions" name="permissions[]" multiple="true">
							                	@foreach($permissions as $permission)
							                    	<option  value="{{$permission->id}}">{{$permission->name}} </option>
							                    @endforeach
											</select>
										</div>
		                        	</div>	
							        </div>	                        	
		                            
		                            <div class="col-xs-12 col-sm-12 col-md-12 ">
						            	<button type="submit" class="btn btn-primary">Create New Role</button>
						            </div>
		                        </form>
		                    </div>
		                </div>
		            </div>
		        </div>
	        </div>
        </div>


@endsection

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.16/js/jquery.dataTables.min.js" integrity="sha512-yCkOYsxpzPSpcbHspsH6A28Z0cgsfjJhlR78nPAfLLZSSV40tVN4VQ6ES/miqI/1z8a5FWVYwCF145+eyJx9Tw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.16/js/dataTables.bootstrap4.min.js" integrity="sha512-2wDq7VuYclJFDG5YbUbmOEWYtTEs/DwpKa9maNvC8gIhEHyR/rgh1BuyUrPZy00H8/DGlLAwbYwSpzCRV0dQJw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
	$(document).ready(function() {
	    $('#permissions').select2();
	});
</script>
@endsection


