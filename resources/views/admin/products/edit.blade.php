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
		                    <h4 class="card-title">Edit Product</h4>
		                    <div class="basic-form">
		                        <form enctype="multipart/form-data" action="{{ route('product.update',$product->id) }}" method="POST">
		                        	@csrf
									@method('PUT')
		                        	<div class="row">
			                        	<div class="form-group col-md-4">
			                            	<label>Product Name</label>
			                                <input type="text" name="product_name" class="form-control input-default" placeholder="Enter Product Name" value="{{$product->product_name}}">
			                                @if($errors->has('product_name'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('product_name') }}
							                    </p>
							                @endif
			                            </div>	

			                            <div class="form-group col-md-4">
			                            	<label>Product Price</label>
			                                <input type="text" name="price" class="form-control input-default" placeholder="Enter Product Price" value="{{$product->price}}">
			                                @if($errors->has('price'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('price') }}
							                    </p>
							                @endif
			                            </div>

			                        	<div class="form-group col-md-4">
			                            	<label>Product MRP</label>
			                                <input type="number" name="mrp" class="form-control input-default" placeholder="Enter Product MRP" value="{{$product->mrp}}">
			                                @if($errors->has('mrp'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('mrp') }}
							                    </p>
							                @endif
			                            </div>
			                        </div>			                        

			                        <div class="row">
	                                	<div class="col-md-4">
	                                        <label class="mr-sm-2">Is On Sale</label>
	                                        <select class="form-control mr-sm-2" id="inlineFormCustomSelect" name="is_on_sale" value="{{ $product->is_on_sale }}">
	                                        	<option  value="{{$product->is_new}}">Choose... </option>
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
			                                <input type="number" name="sale_price" class="form-control input-default" placeholder="Enter Product Sale Price" value="{{$product->sale_price}}">
			                                @if($errors->has('sale_price'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('sale_price') }}
							                    </p>
							                @endif
			                            </div>

			                            <div class="form-group col-md-4">
			                            	<label>Product Sale Percentage</label>
			                                <input type="number" name="sale_percentage" class="form-control input-default" placeholder="Enter Sale Percentage" value="{{$product->sale_percentage}}">
			                                @if($errors->has('sale_percentage'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('sale_percentage') }}
							                    </p>
							                @endif
			                            </div>
							        </div>

							        <div class="row">                         
	                                	<div class="col-md-4">
	                                        <label class="mr-sm-2">Is New</label>
	                                        <select class="form-control mr-sm-2" id="inlineFormCustomSelect" name="is_new" value="{{ $product->is_new }}">
	                                        	<option  value="{{$product->is_new}}">Choose... </option>
	                                            <option value="1">YES</option>
	                                            <option value="0">NO</option>
	                                        </select>
	                                        @if($errors->has('is_new'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('is_new') }}
							                    </p>
							                @endif
	                                	</div>

	                                	<div class="form-group col-md-3">
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
							        </div>

							        <div class="row">

		                                <div class="form-group col-md-6">
							                <label>Category</label>
			                                <select class="form-control select2" id="categories" name="categories[]" multiple="true">
							                	@foreach($categories as $category)
							                    	<option  value="{{$category->id}}">{{$category->category_name}} </option>
							                    @endforeach
											</select>
										</div>

										<div class="form-group col-md-6">
							                <label>Sub Category</label>
			                                <select class="form-control select2" id="subCategories" name="subCategories[]" multiple="true">
											</select>
										</div>

							        </div>

							        <div class="row">
	                                	<div class="form-group col-md-12">
			                            	<label>Product Description</label>
			                                <textarea name="description" class="form-control input-default" placeholder="Enter Product Description" value="">{{$product->description}}  </textarea>
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

 	<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.16/js/jquery.dataTables.min.js" integrity="sha512-yCkOYsxpzPSpcbHspsH6A28Z0cgsfjJhlR78nPAfLLZSSV40tVN4VQ6ES/miqI/1z8a5FWVYwCF145+eyJx9Tw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.16/js/dataTables.bootstrap4.min.js" integrity="sha512-2wDq7VuYclJFDG5YbUbmOEWYtTEs/DwpKa9maNvC8gIhEHyR/rgh1BuyUrPZy00H8/DGlLAwbYwSpzCRV0dQJw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
	   

    </script>
    <script>
    	$(document).ready(function() {
	    $('#categories').select2();
	});
	
	$(document).ready(function() {
	    $('#subCategories').select2();
	});
    $( document ).ready(function() {
    	var orgCategory = {!! json_encode($product_categories)!!}
    	var orgSubCategory = {!! json_encode($product_sub_categories)!!}
    	var firstLoad = true;
    	var storeOptions = [];
    	console.log(orgCategory)
    	$('#categories').select2().val(orgCategory);
    	
    	$('#categories').select2().trigger({
		    type: 'select2:select',
		    params: {
		        data: []
		    }
		});
		getData(orgCategory.join())
    	$('#categories').on('select2:change', function (e) {
		  console.log("shubham")
		});
        function getData(parent_ids) {
    	
        let dataToSend = {}
        //$("#subCategories").empty();
        dataToSend['url']= "{!! route('getSubCategory') !!}"+"/?parent_id="+parent_ids
        dataToSend['requestType']='GET';
        dataToSend['data']={ };
        dataToSend['successCallbackFunction'] = onGetDataSuccess;
        ajaxCall(dataToSend)
        }

        function onGetDataSuccess(data){
        	 if(data['success']){
                for(let item of data['data']){
                	if(!storeOptions.includes(item["id"])){
                	$("#subCategories").append('<option value=' + item["id"] + '>' + item["category_name"] + '</option>');
         
                	storeOptions.push(item["id"]);
                	
                	}
                	
                }
                if(firstLoad){
                	$('#subCategories').val(orgSubCategory).trigger('change');
                	firstLoad = false;
                }
                
            }else{
                showToast('error','Error',/*data['message']*/'Select Category First');
            }
        }




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


	// $.each(product_categories, function(e){
	//     $("#subCategories option[value='" + e + "']").prop("selected", true);
	// });

	//subCategories.forEach(apndSubCat);

	// $('#subCategories').each(product_categories, function(e){
	// 	$("#subCategories option[value='" + e.category_id + "']").prop("selected", true);
	// });
    });
    </script>

@endsection


