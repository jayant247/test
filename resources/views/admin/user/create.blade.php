
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
		                    <h4 class="card-title">Add User</h4>
		                    <div class="basic-form">
		                        <form enctype="multipart/form-data" action="{{ route('user.store') }}" method="POST">
		                        	@csrf
		                        	<div class="row">
			                        	<div class="form-group col-md-4">
			                            	<label>Name*</label>
			                                <input type="text" name="name" class="form-control input-default" placeholder="Enter Your Name">
			                                @if($errors->has('name'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('name') }}
							                    </p>
							                @endif
			                            </div>

			                            <div class="form-group col-md-4">
			                            	<label>Email*</label>
			                                <input type="text" name="email" class="form-control input-default" placeholder="Enter Email id">
			                                @if($errors->has('email'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('email') }}
							                    </p>
							                @endif
			                            </div>

			                            <div class="form-group col-md-4">
			                            	<label>Mobile</label>
			                                <input type="text" name="mobile_no" class="form-control input-default" placeholder="Enter Mobile Number">
			                                @if($errors->has('mobile_no'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('email') }}
							                    </p>
							                @endif
			                            </div>
		                        	</div>
		                            
		                            <div class="row">

		                            	<div class="form-group col-md-4">
			                            	<label>Password*</label>
			                                <input type="password" name="password" class="form-control input-default" placeholder="Enter Password">
			                                @if($errors->has('password'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('password') }}
							                    </p>
							                @endif
			                            </div>

			                            <div class="form-group col-md-4">
			                            	<label>Confirm Password*</label>
			                                <input type="password" name="confirm-password" class="form-control input-default" placeholder="Enter Password Again">
			                                @if($errors->has('confirm-password'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('confirm-password') }}
							                    </p>
							                @endif
			                            </div>

							            <div class="form-group col-md-4">
							                <label>Role*</label>
							                <select class="form-control" name="role">
							                <option selected="selected">Choose...</option>
						                	@foreach($roles as $role)
						                    	<option  value="{{$role->id}}">{{$role->name}} </option>
						                    @endforeach
							                </select>
							                @if($errors->has('role'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('role') }}
							                    </p>
							                @endif
							            </div>							            
							        </div>
		                            <div class="col-xs-12 col-sm-12 col-md-12 ">
						            	<button type="submit" class="btn btn-primary">Create New User</button>
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


