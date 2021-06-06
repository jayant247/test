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
		                    <h4 class="card-title">Edit Pincode</h4>
		                    <div class="basic-form">
		                        <form enctype="multipart/form-data" action="{{ route('pincode.update',$pincode->id) }}" method="POST">
		                        	@csrf
									@method('PUT')
		                        	<div class="row">
			                        	<div class="form-group col-md-4">
			                            	<label>Pincode</label>
			                                <input type="text" name="pincode" class="form-control input-default" placeholder="Enter Area Pincode" value="{{$pincode->pincode}}">
			                                @if($errors->has('pincode'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('pincode') }}
							                    </p>
							                @endif
			                            </div>			                            

			                            <div class="col-md-4">
	                                        <label class="mr-sm-2">Is Active</label>
	                                        <select class="form-control mr-sm-2" id="inlineFormCustomSelect" name="is_active" >
	                                            <option selected="selected">Choose...</option>
	                                            <option value="1">YES</option>
	                                            <option value="0">NO</option>
	                                        </select>
	                                        @if($errors->has('is_active'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('is_active') }}
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


