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
		                    <h4 class="card-title">Edit Promocode</h4>
		                    <div class="basic-form">
		                        <form enctype="multipart/form-data" action="{{ route('promocode.update',$promocode->id) }}" method="POST">
		                        	@csrf
									@method('PUT')

									<div class="row">
			                        	<div class="form-group col-md-4">
			                            	<label>Promocode</label>
			                                <input type="text" name="promocode" class="form-control input-default" placeholder="Enter Promocode" value="{{ $promocode->promocode }}">
			                                @if($errors->has('promocode'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('promocode') }}
							                    </p>
							                @endif
			                            </div>

			                            <!-- <div class="form-group col-md-4">
			                            	<label>Type*</label>
			                                <input type="text" name="type" class="form-control input-default" placeholder="Enter Prmocode Type">
			                                @if($errors->has('type'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('type') }}
							                    </p>
							                @endif
			                            </div> -->

			                            <div class="col-md-4">
	                                        <label class="mr-sm-2">Type</label>
	                                        <select class="form-control mr-sm-2" id="inlineFormCustomSelect" name="type" value="{{ $promocode->type }}">
	                                            <option selected="selected">Choose...</option>
	                                            <option value="1">Percentage</option>
	                                            <option value="0">Flat</option>
	                                        </select>
	                                        @if($errors->has('is_for_new_user'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('is_for_new_user') }}
							                    </p>
							                @endif
	                                	</div>

			                            <div class="col-md-4">
	                                        <label class="mr-sm-2">Only for New Customers</label>
	                                        <select class="form-control mr-sm-2" id="inlineFormCustomSelect" name="is_for_new_user" value="{{ $promocode->is_for_new_user }}">
	                                            <option selected="selected">Choose...</option>
	                                            <option value="1">YES</option>
	                                            <option value="0">NO</option>
	                                        </select>
	                                        @if($errors->has('is_for_new_user'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('is_for_new_user') }}
							                    </p>
							                @endif
	                                	</div>	
		                        	</div>
		                            
		                        	<div class="row">
			                        	<div class="form-group col-md-4">
			                            	<label>Discount</label>
			                                <input type="number" name="discount" class="form-control input-default" placeholder="Enter Discount Value" value="{{ $promocode->discount }}">
			                                @if($errors->has('discount'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('discount') }}
							                    </p>
							                @endif
			                            </div>

			                            <div class="form-group col-md-4">
			                            	<label>Minimum Cart Value</label>
			                                <input type="number" name="minimal_cart_total" class="form-control input-default" placeholder="Enter Minimum Cart Value" value="{{ $promocode->minimal_cart_total }}">
			                                @if($errors->has('minimal_cart_total'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('minimal_cart_total') }}
							                    </p>
							                @endif
			                            </div>

			                            <div class="form-group col-md-4">
			                            	<label>Maximum Discount</label>
			                                <input type="number" name="max_discount" class="form-control input-default" placeholder="Enter Max Discount Value" value="{{ $promocode->max_discount }}">
			                                @if($errors->has('max_discount'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('max_discount') }}
							                    </p>
							                @endif
			                            </div>	
		                        	</div>

		                        	<div class="row">
		                        		<div class="form-group col-md-4">
						                    <label>Starts From</label>
						                    <input type="date" id="start_from" name="start_from" class="form-control input-default" value="{{ $promocode->start_from }}">
						                    @if($errors->has('start_from'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('start_from') }}
							                    </p>
							                @endif
						                </div>

						                <div class="form-group col-md-4">
						                    <label>Ends On</label>
						                    <input type="date" id="end_on" name="end_on" class="form-control input-default" value="{{ $promocode->end_on }}">
						                    @if($errors->has('end_on'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('end_on') }}
							                    </p>
							                @endif
						                </div>

						                <div class="col-md-4">
	                                        <label class="mr-sm-2">Is Active</label>
	                                        <select class="form-control mr-sm-2" id="inlineFormCustomSelect" name="is_active" value="{{ $promocode->is_active }}">
	                                        	<!-- @if($promocode->is_active == 1){
	                                        		<option selected="selected">Yes</option>
	                                        	}
	                                            @else{
	                                            	<option selected="selected">NO</option>
	                                        	}
	                                        	@endif -->
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

		                            <div class="row">
			                            <div class="form-group col-md-12">
			                            	<label>Description</label>
			                                <input type="text" name="description" class="form-control input-default" placeholder="Enter Prmocode Description" value="{{ $promocode->description }}">
			                                @if($errors->has('description'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('description') }}
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


