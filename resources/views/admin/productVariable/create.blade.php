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
		                    <h4 class="card-title">Add Product Variable</h4>
		                    <div class="basic-form">
		                        <form enctype="multipart/form-data" action="{{ route('productVariable.store') }}" method="POST">
		                        	@csrf
		                        	<div class="row">
			                        	<div class="form-group col-md-4">
			                            	<label>Product Colour*</label>
			                                <input type="text" name="color" class="form-control input-default" placeholder="Enter Product Colour">
			                                @if($errors->has('color'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('color') }}
							                    </p>
							                @endif
			                            </div>	

			                            <div class="form-group col-md-4">
			                            	<label>Product Size*</label>
			                                <input type="text" name="size" class="form-control input-default" placeholder="Enter Product Size">
			                                @if($errors->has('size'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('size') }}
							                    </p>
							                @endif
			                            </div>	

			                            <div class="form-group col-md-4">
			                            	<label>Product Price*</label>
			                                <input type="number" name="price" class="form-control input-default" placeholder="Enter Product Price">
			                                @if($errors->has('price'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('price') }}
							                    </p>
							                @endif
			                            </div>	 
			                        </div>

			                        <div class="row">
			                        	<div class="form-group col-md-4">
			                            	<label>Product MRP*</label>
			                                <input type="number" name="mrp" class="form-control input-default" placeholder="Enter Product MRP">
			                                @if($errors->has('mrp'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('mrp') }}
							                    </p>
							                @endif
			                            </div>	

			                            <div class="form-group col-md-4">
			                            	<label>Product Quantity*</label>
			                                <input type="number" name="quantity" class="form-control input-default" placeholder="Enter Product Quantity">
			                                @if($errors->has('quantity'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('quantity') }}
							                    </p>
							                @endif
			                            </div>	

			                            <div class="form-group col-md-4">
			                            	<label>Product Type</label>
			                                <input type="text" name="type" class="form-control input-default" placeholder="Enter Product Type">
			                                @if($errors->has('type'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('type') }}
							                    </p>
							                @endif
			                            </div>	 
			                        </div>

			                        <div class="row">
			                            <div class="col-md-4">
	                                        <label class="mr-sm-2">Is On Sale*</label>
	                                        <select class="form-control mr-sm-2" id="is_on_sale" name="is_on_sale" >
	                                            <option selected="selected">Choose...</option>
	                                            <option value="1">YES</option>
	                                            <option value="0">NO</option>
	                                        </select>
	                                        @if($errors->has('is_on_sale'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('is_on_sale') }}
							                    </p>
							                @endif
	                                	</div>

	                                	<div class="form-group col-md-4" id="sale_price">
			                            	<label>Product Sale Price</label>
			                                <input type="number" name="sale_price" class="form-control input-default" placeholder="Enter Product Sale Price">
			                                @if($errors->has('sale_price'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('sale_price') }}
							                    </p>
							                @endif
			                            </div>

			                            <div class="form-group col-md-4" id="sale_percentage">
			                            	<label>Product Sale Percentage</label>
			                                <input type="number" name="sale_percentage" class="form-control input-default" placeholder="Enter Product Sale Percentage">
			                                @if($errors->has('sale_percentage'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('sale_percentage') }}
							                    </p>
							                @endif
			                            </div>

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

		                                <div class="form-group col-md-4">
			                            	<label>Shelf number</label>
			                                <input type="text" name="shelf_no" class="form-control input-default" placeholder="Enter Shelf Number">
			                                @if($errors->has('shelf_no'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('shelf_no') }}
							                    </p>
							                @endif
			                            </div>	

		                                <div class="form-group col-md-4">
							                <label>Product</label>
							                <select class="form-control" name="product_id" id="product_id">
							                    <option  value="{{$product->id}}">{{$product->product_name}} </option>
							                </select>
							            </div>
							        </div>
		                            <div class="col-xs-12 col-sm-12 col-md-12 ">
						            	<button type="submit" class="btn btn-primary">Add New Product Variable</button>
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
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js">
</script>
<script type="text/javascript">
    $('#sale_price').hide();
    $('#sale_percentage').hide();  
	$('#is_on_sale').change(function(){	
		if( $(this).val()=== "1"){
        $("#sale_price").show();
        $("#sale_percentage").show();
        }
        else{
        $("#sale_price").hide();
        $("#sale_percentage").hide();
        }   	
	});
</script>
@endsection


