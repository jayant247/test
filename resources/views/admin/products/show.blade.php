@extends('layouts.layout')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.8.2/css/lightbox.min.css">

  <style type="text/css">
    .lightbox{
      z-index: 9999;
    }
    .small-img{
      width: 100px;height: 100px;
    }
  </style>
@endsection

@section('content')

    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Product Details</h4>
                <div class="row"> 
                <div class="col-md-6">
                    <div class=table-responsive>
                        <table class="table table-striped table-bordered zero-configuration">
                            <thead>
                                <tr>
                                    <th>Property Name</th>
                                    <th>Property Value</th>                                   
                                </tr>
                            </thead>
                            <tbody>                                   
                                <tr>
                                    <td>Product Name :</td>
                                    <td>{{$product->product_name}}</td>
                                </tr>
                            </tbody>
                            <tbody>                                   
                                <tr>
                                    <td>Product Price :</td>
                                    <td>{{$product->price}}</td>
                                </tr>
                            </tbody>
                            <tbody>                                   
                                <tr>
                                    <td>Available Sizes :</td>
                                    <td>{{$product->available_sizes}}</td>
                                </tr>
                            </tbody>
                            <tbody>                                   
                                <tr>
                                    <td>Is On Sale :</td>
                                    <td>{{$product->is_on_sale}}</td>
                                </tr>
                            </tbody>
                            <tbody>                                   
                                <tr>
                                    <td>Sale Percentage :</td>
                                    <td>{{$product->sale_percentage}}</td>
                                </tr>
                            </tbody>
                            <tbody>                                   
                                <tr>
                                    <td>Product Description :</td>
                                    <td>{{$product->description}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class=table-responsive>
                        <table class="table table-striped table-bordered zero-configuration">
                            <thead>
                                <tr>
                                    <th>Property Name</th>
                                    <th>Property Value</th>
                                    
                                </tr>
                            </thead>
                            <tbody>                                   
                                <tr>
                                    <td>Product MRP :</td>
                                    <td>{{$product->mrp}}</td>
                                </tr>
                            </tbody>
                            <tbody>                                   
                                <tr>
                                    <td>Product Ratings :</td>
                                    <td>{{$product->avg_rating}}/5.00</td>
                                </tr>
                            </tbody>
                            <tbody>                                   
                                <tr>
                                    <td>Available Colors :</td>
                                    <td>{{$product->available_colors}}</td>
                                </tr>
                            </tbody>
                            <tbody>                                   
                                <tr>
                                    <td>Sale Price :</td>
                                    <td>{{$product->sale_price}}</td>
                                </tr>
                            </tbody>
                            <tbody>                                   
                                <tr>
                                    <td>Units Sold :</td>
                                    <td>{{$product->sellCount}}</td>
                                </tr>
                            </tbody>
                            <tbody>                                   
                                <tr>
                                    <td>Is New? :</td>
                                    <td>{{$product->is_new}}</td>
                                </tr>
                            </tbody>
                            <tbody>                                   
                                <tr>
                                    <td>Product Image :</td>
                                    <td>
                                        <a href="{{env('APP_URL').$product->primary_image}}" target="_blank" data-lightbox="primary_photo">
                                            <img loading="lazy" style="width: 100px;max-height: 100px;" src="{{env('APP_URL').$product->primary_image}}">
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>                                       
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Product Images</h4>
                <br>
                <div class="row">
                    @foreach($productImages as $index=>$image)
                    <div class="col-md-2">
                        <div class="d-flex justify-content-center">
                            <a href="{{asset(env('APP_URL').$image->imagePath)}}"  data-lightbox="Other_Photos">
                                <img class="img-fluid" loading="lazy" style="width: 100px;max-height: 100px;min-height: 100px;" src="{{asset(env('APP_URL').$image->imagePath)}}">
                            </a>
                        </div>
                        <div class="d-flex justify-content-center my-2">
                            <a class="btn btn-sm btn-outline-dark" href="{{route('productVariable.destroyImage',$image->id)}}">
                                <i class="fa fa-trash"></i>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">Product Description</h4>
                    </div>
                    <div>
                        <a href="{{route('productDescription.create', $product->id)}}" class="btn btn-primary">Create</a>
                    </div>
                </div>

                @if(count($productDescriptions)>0)
                <div class="row">
                    <div class="col-md-6">
                        <div class=table-responsive>
                        <table class="table table-striped table-bordered zero-configuration">
                        <thead>
                            <tr>
                                <th>Property Name</th>
                                <th>Property Value</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                                <tbody>
                                    @foreach($productDescriptions as $index=>$productDescription)
                                    @if($index%2==0)
                                    <tr>
                                        <td>{{$productDescription->property_name}}</td>
                                        <td>{{$productDescription->property_value}}</td>
                                        <td>
                                            <form action="{{ route('productDescription.destroy',$productDescription->id) }}" method="POST">
                                                <button data-toggle="modal" onclick="openDetailsModal({{$productDescription->id}})"  class="btn btn-sm btn-outline-dark" >
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                <a class="btn btn-sm btn-outline-dark" href="{{route('productDescription.edit',$productDescription->id)}}" >
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm(' you want to delete?');" class="btn btn-sm btn-outline-dark">
                                                
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </form>
                                        </td>
                                    </tr>
                                    @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class=table-responsive>
                            <table class="table table-striped table-bordered zero-configuration">
                                 <thead>
                            <tr>
                                <th>Property Name</th>
                                <th>Property Value</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                                <tbody>
                                    @foreach($productDescriptions as $index=>$productDescription)
                                    @if($index%2==1)
                                    <tr>
                                        <td>{{$productDescription->property_name}}</td>
                                        <td>{{$productDescription->property_value}}</td>
                                        <td>
                                            <form action="{{ route('productDescription.destroy',$productDescription->id) }}" method="POST">
                                                <button data-toggle="modal" onclick="openDetailsModal({{$productDescription->id}})"  class="btn btn-sm btn-outline-dark" >
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                <a class="btn btn-sm btn-outline-dark" href="{{route('productDescription.edit',$productDescription->id)}}" >
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm(' you want to delete?');" class="btn btn-sm btn-outline-dark">
                                                
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </form>
                                        </td>
                                    </tr>
                                    @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>                    
                @else
                    <div class="text-center">
                        <h5>Desciption Not Available</h5>
                    </div>
                @endif
            </div>
            {{--    Modal For Product Description  --}}
            @if(count($productDescriptions)>0)
               @foreach($productDescriptions as $productDescription)
                   <div class="modal fade bd-example-modal-lg" id="{{$productDescription->id}}" tabindex="-1" permission="dialog" aria-hidden="true">
                       <div class="modal-dialog modal-lg">
                           <div class="modal-content">
                               <div class="modal-header">
                                   <h5 class="modal-title">Product Description</h5>
                                   <button type="button" class="close" data-dismiss="modal"><span>&times;</span>
                                   </button>
                               </div>
                               <div class="modal-body">

                                   <div class="container-fluid">
                                       <div class="row">
                                           <table  class="table table-striped table-bordered zero-configuration">
                                               <thead>
                                               <tr>
                                                   <th>Field</th>
                                                   <th>Value</th>
                                               </tr>
                                               </thead>
                                               <tbody>
                                                   <tr>
                                                       <td>{{$productDescription->property_name}}</td>
                                                       <td>{{$productDescription->property_value}}</td>
                                                   </tr>                                           
                                                </tbody>
                                           </table>
                                       </div>


                                   </div>
                               </div>
                               <div class="modal-footer">
                                   <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                               </div>
                           </div>
                       </div>
                   </div>
               @endforeach
            @endif
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">Product Variables</h4>
                    </div>
                    <div>
                        <a href="{{route('productVariable.create', $product->id)}}" class="btn btn-primary">Create</a>
                    </div>

                </div>
                @if(count($productVariables)>0)
                
                <div class="table-responsive">

                    <table id="productVariable-table" class="table table-striped table-bordered zero-configuration">
                        <thead>
                            <tr>
                                <th>Colour</th>
                                <th>Image</th>
                                <th>Size</th>
                                <th>Price</th>
                                <th>Is On sale</th>
                                <th>Sale Price</th>
                                <th>Sale Percentage</th>
                                <th>Quantity</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($productVariables as $productVariable)
                            <tr>
                                <td>{{$productVariable->color}}</td>
                                <td>
                                    <a href="{{env('APP_URL').$productVariable->primary_image}}" target="_blank">
                                        <img loading="lazy" style="width: 100px;max-height: 100px;" src="{{env('APP_URL').$productVariable->primary_image}}">
                                    </a>
                                </td>
                                <td>{{$productVariable->size}}</td>                           
                                <td>{{$productVariable->price}}</td>
                                <td>{{$productVariable->is_on_sale}}</td>
                                <td>{{$productVariable->sale_price}}</td>
                                <td>{{$productVariable->sale_percentage}}</td>
                                <td>{{$productVariable->quantity}}</td>
                                <td>
                                    <form action="{{ route('productVariable.destroy',$productVariable->id) }}" method="POST">
                                        <button data-toggle="modal" onclick="openDetailsModal({{$productVariable->id}})"  class="btn btn-sm btn-outline-dark" >
                                            <i class="fa fa-eye"></i>
                                        </button>
                                        <a class="btn btn-sm btn-outline-dark" href="{{route('productVariable.edit',$productVariable->id)}}" >
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm(' you want to delete?');" class="btn btn-sm btn-outline-dark">
                                        
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                    <div class="text-center">
                        <h5>Product Variables Not Available</h5>
                    </div>
                @endif
            </div>
        </div>

    </div>
@endsection

@section('js')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.16/js/jquery.dataTables.min.js" integrity="sha512-yCkOYsxpzPSpcbHspsH6A28Z0cgsfjJhlR78nPAfLLZSSV40tVN4VQ6ES/miqI/1z8a5FWVYwCF145+eyJx9Tw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.16/js/dataTables.bootstrap4.min.js" integrity="sha512-2wDq7VuYclJFDG5YbUbmOEWYtTEs/DwpKa9maNvC8gIhEHyR/rgh1BuyUrPZy00H8/DGlLAwbYwSpzCRV0dQJw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script type="text/javascript" src="{{url('js/lightbox.js')}}" ></script>
      <script>
        lightbox.option({
          'resizeDuration': 200,
          'wrapAround': true
        })
      </script>
    <script>
        let productVariables = {!! $productVariables !!}
        if(productVariables.length>0){
            $(document).ready( function () {
                $('#productVariable-table').DataTable({
                    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
                });
            } );
        }
        function openDetailsModal(id) {
            $("#"+id).modal()
        }
    </script>

    // Instantiate the Bootstrap carousel
<!--     <script>
        $('.multi-item-carousel').carousel({
          interval: false
        });

        $('.multi-item-carousel .item').each(function(){
          var next = $(this).next();
          if (!next.length) {
            next = $(this).siblings(':first');
          }
          next.children(':first-child').clone().appendTo($(this));
          
          if (next.next().length>0) {
            next.next().children(':first-child').clone().appendTo($(this));
          } else {
            $(this).siblings(':first').children(':first-child').clone().appendTo($(this));
          }
        });        
    </script> -->


@endsection


