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
		                    <h4 class="card-title">Add Category</h4>
		                    <div class="basic-form">
		                        <form enctype="multipart/form-data" action="{{ route('category.store') }}" method="POST">
		                        	@csrf
		                        	<div class="row">
			                        	<div class="form-group col-md-4">
			                            	<label>Name*</label>
			                                <input value="{{old('category_name')}}" type="text" name="category_name" class="form-control input-default" placeholder="Enter Category Name">
			                                @if($errors->has('category_name'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('category_name') }}
							                    </p>
							                @endif
			                            </div>

			                            <div class="form-group col-md-4">
			                            	<label>Type</label>
			                                <input value="{{old('type')}}" type="text" name="type" class="form-control input-default" placeholder="Enter Category Type">
			                                @if($errors->has('type'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('type') }}
							                    </p>
							                @endif
			                            </div>

			                            <div class="col-md-4">
	                                        <label class="mr-sm-2">Is Big Thumbnail Show*</label>
	                                        <select class="form-control mr-sm-2" id="inlineFormCustomSelect" name="is_bigthumbnail_show" >
	                                            <option selected="selected">Choose...</option>
	                                            <option value="1">YES</option>
	                                            <option value="2">NO</option>
	                                        </select>
	                                        @if($errors->has('is_bigthumbnail_show'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('is_bigthumbnail_show') }}
							                    </p>
							                @endif
	                                	</div>
		                        	</div>

		                        	<div class="row">
		                                <div class="form-group col-md-3">
			                            	<label><br>Thumbnail*</label>
		                                	<div class="form-group">
		                                        <input type="file" accept=".png, .jpg, .jpeg" name="category_thumbnail" class="form-control-file">
		                                        @if($errors->has('category_thumbnail'))
								                    <p class="d-block invalid-feedback animated fadeInDown" style="">
								                        {{ $errors->first('category_thumbnail') }}
								                    </p>
								                @endif
		                                    </div>
		                                </div>

		                                <div class="form-group col-md-3">
			                            	<label><br>Big Thumbnail</label>
		                                	<div class="form-group">
		                                        <input type="file" accept=".png, .jpg, .jpeg" name="big_thumbnail" class="form-control-file">
		                                        @if($errors->has('big_thumbnail'))
								                    <p class="d-block invalid-feedback animated fadeInDown" style="">
								                        {{ $errors->first('big_thumbnail') }}
								                    </p>
								                @endif
		                                    </div>
		                                </div>

		                                <div class="form-group col-md-3">
			                            	<label><br>Square Thumbnail*</label>
		                                	<div class="form-group">
		                                        <input type="file" accept=".png, .jpg, .jpeg" name="square_thumbnail" class="form-control-file">
		                                        @if($errors->has('square_thumbnail'))
								                    <p class="d-block invalid-feedback animated fadeInDown" style="">
								                        {{ $errors->first('square_thumbnail') }}
								                    </p>
								                @endif
		                                    </div>
		                                </div>

		                                <div class="form-group col-md-3">
			                            	<label><br>New Page Thumnail</label>
		                                	<div class="form-group">
		                                        <input type="file" accept=".png, .jpg, .jpeg" name="new_page_thumbnail" class="form-control-file">
		                                        @if($errors->has('new_page_thumbnail'))
								                    <p class="d-block invalid-feedback animated fadeInDown" style="">
								                        {{ $errors->first('new_page_thumbnail') }}
								                    </p>
								                @endif
		                                    </div>
		                                </div>
		                            </div>
		                            <div class="col-xs-12 col-sm-12 col-md-12 ">
						            	<button type="submit" class="btn btn-primary">Create New Category</button>
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


