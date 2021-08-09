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
		                    <h4 class="card-title">Add Product</h4>
		                    <div class="basic-form">
		                        <form enctype="multipart/form-data" action="{{ route('product.store') }}" method="POST">
		                        	@csrf
		                        	<div class="row">
			                        	<div class="form-group col-md-4">
			                            	<label>Product Name*</label>
			                                <input value="{{old('product_name')}}" type="text" name="product_name" class="form-control input-default" placeholder="Enter Product Name">
			                                @if($errors->has('product_name'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('product_name') }}
							                    </p>
							                @endif
			                            </div>

			                            <div class="form-group col-md-4">
			                            	<label>Product Price*</label>
			                                <input value="{{old('price')}}" type="number" name="price" class="form-control input-default" placeholder="Enter Product Price">
			                                @if($errors->has('price'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('price') }}
							                    </p>
							                @endif
			                            </div>

			                        	<div class="form-group col-md-4">
			                            	<label>Product MRP*</label>
			                                <input value="{{old('mrp')}}" type="number" name="mrp" class="form-control input-default" placeholder="Enter Product MRP">
			                                @if($errors->has('mrp'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('mrp') }}
							                    </p>
							                @endif
			                            </div>
			                        </div>

			                        <div class="row">
			                            <div class="col-md-4">
	                                        <label class="mr-sm-2">Is On Sale*</label>
	                                        <select class="form-control mr-sm-2" id="inlineFormCustomSelect" name="is_on_sale" >
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

	                                	<div class="form-group col-md-4">
			                            	<label>Product Sale Price</label>
			                                <input value="{{old('sale_price')}}" type="number" name="sale_price" class="form-control input-default" placeholder="Enter Product Sale Price">
			                                @if($errors->has('sale_price'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('sale_price') }}
							                    </p>
							                @endif
			                            </div>

			                            <div class="form-group col-md-4">
			                            	<label>Product Sale Percentage</label>
			                                <input value="{{old('sale_percentage')}}" type="number" name="sale_percentage" class="form-control input-default" placeholder="Enter Product Sale Percentage">
			                                @if($errors->has('sale_percentage'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('sale_percentage') }}
							                    </p>
							                @endif
			                            </div>
							        </div>

							        <div class="row">
			                            <div class="col-md-4">
	                                        <label class="mr-sm-2">Is New*</label>
	                                        <select class="form-control mr-sm-2" id="inlineFormCustomSelect" name="is_new" >
	                                            <option selected="selected">Choose...</option>
	                                            <option value="1">YES</option>
	                                            <option value="0">NO</option>
	                                        </select>
	                                        @if($errors->has('is_new'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('is_new') }}
							                    </p>
							                @endif
	                                	</div>

	                                	<div class="col-md-4">
	                                        <label class="mr-sm-2">Is Live*</label>
	                                        <select class="form-control mr-sm-2" id="inlineFormCustomSelect" name="is_live" >
	                                            <option selected="selected">Choose...</option>
	                                            <option value="1">YES</option>
	                                            <option value="0">NO</option>
	                                        </select>
	                                        @if($errors->has('is_live'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('is_live') }}
							                    </p>
							                @endif
	                                	</div>

	                                	<div class="form-group col-md-4">
			                            	<label><br>Primary Image</label>
		                                	<div class="form-group">
		                                        <input value="{{old('primary_image')}}" type="file" accept=".png, .jpg, .jpeg" name="primary_image" class="form-control-file">
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
		                                        <input value="{{old('other_images')}}" type="file" accept=".png, .jpg, .jpeg" name="other_images[]" class="form-control-file" multiple>
		                                        @if($errors->has('other_images'))
								                    <p class="d-block invalid-feedback animated fadeInDown" style="">
								                        {{ $errors->first('other_images') }}
								                    </p>
								                @endif
		                                    </div>
		                                </div>
							        </div>


							        <div class="row">

		                                <div class="form-group col-md-6">
							                <label>Category*</label>
			                                <select class="form-control
			                                " id="categories" name="categories[]" multiple="true">
							                	@foreach($categories as $category)
							                    	<option  value="{{$category->id}}">{{$category->category_name}} </option>
							                    @endforeach
											</select>
										</div>

										<div class="form-group col-md-6">
							                <label>Sub Category*</label>
			                                <select class="form-control select2" id="subCategories" name="subCategories[]" multiple="true">
											</select>
										</div>

							        </div>

							        <div class="row">
	                                	<div class="form-group col-md-12">
			                            	<label>Product Description</label>
			                                <textarea name="description" class="form-control input-default" placeholder="Enter Product Description">{{old('description')}}</textarea>
			                                @if($errors->has('description'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('description') }}
							                    </p>
							                @endif
			                            </div>
							        </div>
		                            <div class="col-xs-12 col-sm-12 col-md-12 ">
						            	<button type="submit" class="btn btn-primary">Add New Product</button>
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
    	var storeOptions = [];
	    function getData(parent_ids) {
        let dataToSend = {}
        dataToSend['url']= "{!! route('getSubCategory') !!}"+"/?parent_id="+parent_ids
        dataToSend['requestType']='GET';
        dataToSend['data']={ };
        dataToSend['successCallbackFunction'] = onGetDataSuccess;
        ajaxCall(dataToSend)
        }

        function onGetDataSuccess(data){
        	 if(data['success']){
        	 	console.log(storeOptions)
                for(let item of data['data']){
                	if(!storeOptions.includes(item["id"])){
                	$("#subCategories").append('<option value=' + item["id"] + '>' + item["category_name"] + '</option>');

                	storeOptions.push(item["id"]);

                	}
                	}
            }else{
                showToast('error','Error',/*data['message']*/'Select Category First');
            }
        }


	$(document).ready(function() {
	    $('#categories').select2();
	});
	$(document).ready(function() {
	    $('#subCategories').select2();
	});

$('#categories').on('change', function (e) {
    // var data = e.params.data;
    // console.log(data);
    var theID = $('#categories').select2('data');
    let parent_ids_string = '';
    for (let eachSelectedOpt of  theID) {
    	if(parent_ids_string == ''){
    		parent_ids_string = eachSelectedOpt.id
    	}else{
    		parent_ids_string= parent_ids_string+','+eachSelectedOpt.id
    	}

    }
    getData(parent_ids_string);
 });


    </script>

@endsection


