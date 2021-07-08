@extends('layouts.layout')

@section('css')
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
		                    <h4 class="card-title">Edit Role</h4>
		                    <div class="basic-form">
		                        <form enctype="multipart/form-data" action="{{ route('role.update',$role->id) }}" method="POST">
		                        	@csrf
									@method('PUT')
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
			                        	<div class="form-group col-md-12">
			                            	<label>Permissions</label>
			                            	<br/>
			                            	<div class="row">
			                            	@foreach($permissions as $value)
			                            	<div class="col-md-3">
								                <label>{{ Form::checkbox('permission[]', $value->id, false, array('class' => 'name')) }}
								                {{ $value->name }}</label>
								            </div>
								            <!-- <br/> -->
								            @endforeach
								        </div>
								            @if($errors->has('permissions'))
							                    <p class="help-block">
							                        {{ $errors->first('permissions') }}
							                    </p>
							                @endif
							            </div>
							        </div>
		                            <div class="col-xs-12 col-sm-12 col-md-12 ">
						            	<button type="submit" class="btn btn-primary">Update</button>
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
@endsection


