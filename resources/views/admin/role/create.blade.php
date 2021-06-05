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


		                        	<!-- <div class="col-xs-12 col-sm-12 col-md-12">

								        <div class="form-group">

								            <strong>Permission:</strong>

								            <br/>

								            @foreach($permissions as $value)

								                <label>{{ Form::checkbox('permission[]', $value->id, false, array('class' => 'name')) }}

								                {{ $value->name }}</label>

								            <br/>

								            @endforeach

								        </div>

								    </div> -->
		                            

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


		                            <!-- <div class="row">
			                        	<div class="form-group col-md-8">
			                            	<label>Permissions
							                    <span class="btn btn-info btn-xs select-all">Select all</span>
							                    <span class="btn btn-info btn-xs deselect-all">Deselect all</span></label>
							                <select name="permissions[]" id="permissions" class="form-control select2" multiple="multiple">
							                    @foreach($permissions as $id => $permissions)
							                        <option value="{{ $id }}" >
							                            {{ $permissions }}
							                        </option>
							                    @endforeach
							                </select>
							                @if($errors->has('permissions'))
							                    <p class="help-block">
							                        {{ $errors->first('permissions') }}
							                    </p>
							                @endif
							            </div>
							        </div> -->
		                        	
		                            
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
@endsection


