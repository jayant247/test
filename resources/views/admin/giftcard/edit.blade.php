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
		                    <h4 class="card-title">Edit Gift Card</h4>
		                    <div class="basic-form">
		                        <form enctype="multipart/form-data" action="{{ route('giftcard.update',$giftcard->id) }}" method="POST">
		                        	@csrf
									@method('PUT')
		                        	<div class="row">
			                        	<div class="form-group col-md-4">
			                            	<label>Title*</label>
			                                <input type="text" name="title" class="form-control input-default" placeholder="Enter Giftcard Title" value ="{{$giftcard->title}}">
			                                @if($errors->has('title'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('title') }}
							                    </p>
							                @endif
			                            </div>

			                            <div class="form-group col-md-4">
			                            	<label>Purchase Amount*</label>
			                                <input type="number" name="purchase_amount" class="form-control input-default" placeholder="Enter Purchase Amount" value ="{{$giftcard->purchase_amount}}">
			                                @if($errors->has('purchase_amount'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('purchase_amount') }}
							                    </p>
							                @endif
			                            </div>

			                            <div class="form-group col-md-4">
			                            	<label>Gift Card Amount*</label>
			                                <input type="number" name="gift_amount" class="form-control input-default" placeholder="Enter Gift Card Amount" value ="{{$giftcard->gift_amount}}">
			                                @if($errors->has('gift_amount'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('gift_amount') }}
							                    </p>
							                @endif
			                            </div>			                            
							        </div>

							        <div class="row">
			                        	<div class="form-group col-md-4">
			                            	<label>Validity*</label>
			                                <input type="number" name="validity_days_from_purchase_date" class="form-control input-default" placeholder="Enter Validity in Days" value ="{{$giftcard->validity_days_from_purchase_date}}">
			                                @if($errors->has('validity_days_from_purchase_date'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('validity_days_from_purchase_date') }}
							                    </p>
							                @endif
			                            </div>

		                        		<div class="form-group col-md-4">
						                    <label>Starts From*</label>
						                    <input type="date" id="start_from" name="start_from" class="form-control input-default" value ="{{$giftcard->start_from}}">
						                    @if($errors->has('start_from'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('start_from') }}
							                    </p>
							                @endif
						                </div>

						                <div class="form-group col-md-4">
						                    <label>Ends On*</label>
						                    <input type="date" id="end_on" name="end_on" class="form-control input-default" value="{{$giftcard->end_on}}">
						                    @if($errors->has('end_on'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('end_on') }}
							                    </p>
							                @endif
						                </div>
			                        </div>

			                        <div class="row">
			                            <div class="form-group col-md-8">
			                            	<label>Description</label>
			                                <input type="text" name="description" class="form-control input-default" placeholder="Enter Giftcard Description" value="{{$giftcard->description}}">
			                                @if($errors->has('description'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('description') }}
							                    </p>
							                @endif
			                            </div>

			                            <div class="col-md-4">
	                                        <label class="mr-sm-2">Is Active*</label>
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


