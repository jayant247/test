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
		                    <h4 class="card-title">Add Notification</h4>
		                    <div class="basic-form">
		                        <form enctype="multipart/form-data" action="{{ route('notification.store') }}" method="POST">
		                        	@csrf
		                        	<div class="row">
		                        		<div class="col-md-4">
	                                        <label class="mr-sm-2">User Type*</label>
	                                        <select class="form-control mr-sm-2" id="specific_users" name="user_type" >
	                                            <option selected="selected">Choose...</option>
	                                            <option value="New">New</option>
	                                            <option value="All">All</option>
	                                            <option value="Specific">Specific</option>
	                                        </select>
	                                        @if($errors->has('user_type'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('user_type') }}
							                    </p>
							                @endif
	                                	</div>

	                                	<div class="form-group col-md-4" id="registered_from">
						                    <label>Registerd From*</label>
						                    <input type="date" id="registered_from" name="registered_from" class="form-control input-default">
						                    @if($errors->has('registered_from'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('registered_from') }}
							                    </p>
							                @endif
						                </div>

						                <div class="form-group col-md-4" id="registered_till">
						                    <label>Registered Till*</label>
						                    <input type="date" id="registered_till" name="registered_till" class="form-control input-default">
						                    @if($errors->has('registered_till'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('registered_till') }}
							                    </p>
							                @endif
						                </div>

						                <div class="form-group col-md-4">
						                	<label>Notification Type</label>
							                <div class="form-check mb-1">
                                                <label class="form-check-label">
                                                <input type="checkbox" name="is_mobile" id="for_mobile" class="form-check-input" value="1" onchange='mobile_fields(this);'>Mobile</label>
	                                        </div>
                                            <div class="form-check mb-1">
                                                <label class="form-check-label">
                                                <input type="checkbox" name="is_mail" id="for_mail" class="form-check-input" value="1" onchange='mail_fields(this);'>Mail</label>
                                            </div>
                                            <div class="form-check mb-1">
                                                <label class="form-check-label">
                                                <input type="checkbox" name="is_sms" id="for_sms" class="form-check-input" value="1" onchange='sms_fields(this);'>SMS</label>
                                            </div>
	                                    </div>	                                  

	                                	<div class="form-group col-md-4">
			                            	<label>Notification Heading*</label>
			                                <input type="text" name="heading" class="form-control input-default" placeholder="Enter Notification Heading">
			                                @if($errors->has('heading'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('heading') }}
							                    </p>
							                @endif
			                            </div>

			                            <div class="form-group col-md-4" id="mobile_body">
			                            	<label>Mobile Body</label>
			                                <input type="text" name="mobile_body" class="form-control input-default" placeholder="Enter Mobile Body">
			                                @if($errors->has('mobile_body'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('mobile_body') }}
							                    </p>
							                @endif
			                            </div>

			                            <div class="form-group col-md-4" id="mobile_image">
			                            	<label><br>Mobile Notification Image</label>
		                                	<div class="form-group">
		                                        <input type="file" accept=".png, .jpg, .jpeg" name="mobile_image" class="form-control-file">
		                                        @if($errors->has('mobile_image'))
								                    <p class="d-block invalid-feedback animated fadeInDown" style="">
								                        {{ $errors->first('mobile_image') }}
								                    </p>
								                @endif
		                                    </div>
		                                </div>

			                            <div class="form-group col-md-4" id="mail_body">
			                            	<label>Mail Body</label>
			                                <input type="text" name="mail_body" class="form-control input-default" placeholder="Enter Mail Body">
			                                @if($errors->has('mail_body'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('mail_body') }}
							                    </p>
							                @endif
			                            </div>

			                            <div class="form-group col-md-4" id="sms_body">
			                            	<label>SMS Body</label>
			                                <input type="text" name="sms_body" class="form-control input-default" placeholder="Enter SMS Body">
			                                @if($errors->has('sms_body'))
							                    <p class="d-block invalid-feedback animated fadeInDown" style="">
							                        {{ $errors->first('sms_body') }}
							                    </p>
							                @endif
			                            </div>
		                        	</div>

		                        	<br>
		                            <div class="col-xs-12 col-sm-12 col-md-12 ">
						            	<button type="submit" class="btn btn-primary">Create New Notification</button>
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
    $('#registered_from').hide();
    $('#registered_till').hide();  
	$('#specific_users').change(function(){	
		if( $(this).val()=== "Specific"){
        $("#registered_from").show();
        $("#registered_till").show();
        }
        else{
        $("#registered_from").hide();
        $("#registered_till").hide();
        }   	
	});

		$('#mobile_body').hide();
		$('#mail_body').hide();
	    $('#sms_body').hide(); 
	    $('#mobile_image').hide();
	function mobile_fields(checkbox) {
	    if(checkbox.checked == true){
	        $('#mobile_body').show(); 
	        $('#mobile_image').show();
	    }else{
	        $('#mobile_body').hide();
		    $('#mobile_image').hide();
	   }
	}
	function mail_fields(checkbox) {
	    if(checkbox.checked == true){
	        $('#mail_body').show();
	    }else{
			$('#mail_body').hide();
	   }
	}
	function sms_fields(checkbox) {
	    if(checkbox.checked == true){
	        $('#sms_body').show();
	    }else{
		    $('#sms_body').hide();
	   }
	}

</script>
@endsection


