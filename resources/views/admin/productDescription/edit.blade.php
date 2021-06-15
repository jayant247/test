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
		                    <h4 class="card-title">Edit Product Description</h4>
		                    <div class="basic-form">
		                        <form enctype="multipart/form-data" action="{{ route('productDescription.update',$productDescription->id) }}" method="POST">
		                        	@csrf
									@method('PUT')
		                        	<div class="row">
			                        	<div class="form-group col-md-4">
			                            	<label>Property Name</label>
			                                <input type="text" name="property_name" class="form-control input-default" placeholder="Enter Property Name" value="{{$productDescription->property_name}}">
			                                @if($errors->has('property_name'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('property_name') }}
							                    </p>
							                @endif
			                            </div>			                            

			                        	<div class="form-group col-md-4">
			                            	<label>Property Value</label>
			                                <input type="text" name="property_value" class="form-control input-default" placeholder="Enter Property Name" value="{{$productDescription->property_value}}">
			                                @if($errors->has('property_value'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('property_value') }}
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


