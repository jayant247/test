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
		                    <h4 class="card-title">Edit Product Variable</h4>
		                    <div class="basic-form">
		                        <form enctype="multipart/form-data" action="{{ route('productVariable.update',$productVariable->id) }}" method="POST">
		                        	@csrf
									@method('PUT')
		                        	<div class="row">
			                        	<div class="form-group col-md-4">
			                            	<label>Product Colour</label>
			                                <input type="text" name="color" class="form-control input-default" placeholder="Enter Product Colour" value="{{$productVariable->color}}">
			                                @if($errors->has('color'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('color') }}
							                    </p>
							                @endif
			                            </div>

			                            <div class="form-group col-md-4">
			                            	<label>Product Size</label>
			                                <input type="text" name="size" class="form-control input-default" placeholder="Enter Product Size" value="{{$productVariable->size}}">
			                                @if($errors->has('size'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('size') }}
							                    </p>
							                @endif
			                            </div>

			                            <div class="form-group col-md-4">
			                            	<label>Product Price</label>
			                                <input type="text" name="price" class="form-control input-default" placeholder="Enter Product Price" value="{{$productVariable->price}}">
			                                @if($errors->has('price'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('price') }}
							                    </p>
							                @endif
			                            </div>
			                        </div>

			                        <div class="row">
			                        	<div class="form-group col-md-4">
			                            	<label>Product MRP</label>
			                                <input type="number" name="mrp" class="form-control input-default" placeholder="Enter Product MRP" value="{{$productVariable->mrp}}">
			                                @if($errors->has('mrp'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('mrp') }}
							                    </p>
							                @endif
			                            </div>

			                            <div class="form-group col-md-4">
			                            	<label>Product Quantity</label>
			                                <input type="number" name="quantity" class="form-control input-default" placeholder="Enter Product Size" value="{{$productVariable->quantity}}">
			                                @if($errors->has('quantity'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('quantity') }}
							                    </p>
							                @endif
			                            </div>

			                            <div class="form-group col-md-4">
			                            	<label>Product Type</label>
			                                <input type="text" name="type" class="form-control input-default" placeholder="Enter Product Type" value="{{$productVariable->type}}">
			                                @if($errors->has('type'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('type') }}
							                    </p>
							                @endif
			                            </div>
			                        </div>

			                        <div class="row">
			                            <div class="col-md-4">
	                                        <label class="mr-sm-2">Is On Sale</label>
	                                        <select class="form-control mr-sm-2" id="inlineFormCustomSelect" name="is_on_sale" >
	                                        	@if($productVariable->is_on_sale == 1)
	                                            	<option value="{{ $productVariable->is_on_sale}}" selected="selected">Yes</option>
	                                            	<option value="0">NO</option>
	                                            @elseif($productVariable->is_on_sale == 0)
	                                            	<option value="{{ $productVariable->is_on_sale}}" selected="selected">NO</option>
	                                            	<option value="1">YES</option>
	                                            @endif
	                                        </select>
	                                        @if($errors->has('is_on_sale'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('is_on_sale') }}
							                    </p>
							                @endif
	                                	</div>

	                                	<div class="form-group col-md-4">
			                            	<label>Product Sale Price</label>
			                                <input type="number" name="sale_price" class="form-control input-default" placeholder="Enter Product Sale Price" value="{{$productVariable->sale_price}}">
			                                @if($errors->has('sale_price'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('sale_price') }}
							                    </p>
							                @endif
			                            </div>

			                            <div class="form-group col-md-4">
			                            	<label>Product Sale Percentage</label>
			                                <input type="number" name="sale_percentage" class="form-control input-default" placeholder="Enter Product MRP" value="{{$productVariable->sale_percentage}}">
			                                @if($errors->has('sale_percentage'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('sale_percentage') }}
							                    </p>
							                @endif
			                            </div>
							        </div>
							        <div class="row">
							        	<div class="form-group col-md-4">
			                            	<label><br>Primary Image</label>
		                                	<div class="form-group">
		                                        <input type="file" accept=".png, .jpg, .jpeg" name="primary_image" class="form-control-file">
		                                        @if($errors->has('primary_image'))
								                    <p class="d-block invalid-feedback animated fadeInDown" style="">
								                        {{ $errors->first('primary_image') }}
								                    </p>
								                @endif
		                                    </div>
		                                </div>

		                                <div class="form-group col-md-4">
		                                	<label><br>Other Images</label>
		                                	<div class="form-group">
		                                        <input type="file" accept=".png, .jpg, .jpeg" name="other_images[]" class="form-control-file" multiple>
		                                        @if($errors->has('other_images'))
								                    <p class="d-block invalid-feedback animated fadeInDown" style="">
								                        {{ $errors->first('other_images') }}
								                    </p>
								                @endif
		                                    </div>
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


