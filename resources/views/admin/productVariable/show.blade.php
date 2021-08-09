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
                <h4 class="card-title">Product Variable Details</h4>
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
                               <th>Colour</th>
                               <td>{{$productVariable->color}}</td>
                           </tr>
                           <tr>
                               <th>Size</th>
                               <td>{{$productVariable->size}}</td>
                           </tr>
                           <tr>
                               <th>Price</th>
                               <td>{{$productVariable->price}}</td>
                           </tr>
                           <tr>
                               <th>MRP</th>
                               <td>{{$productVariable->mrp}}</td>
                           </tr>
                           <tr>
                               <th>Is On Sale?</th>
                               <td>{{$productVariable->is_on_sale}}</td>
                           </tr>
                           <tr>
                               <th>Sale Price</th>
                               <td>{{$productVariable->sale_price}}</td>
                           </tr>
                           <tr>
                               <th>Sale Percentage</th>
                               <td>{{$productVariable->sale_percentage}}</td>
                           </tr>
                           <tr>
                               <th>Quantity</th>
                               <td>{{$productVariable->quantity}}</td>
                           </tr>
                           <tr>
                               <th>Type</th>
                               <td>{{$productVariable->type}}</td>
                           </tr>
                           <tr>
                               <th>QR</th>
                               <td><img src="{{env('APP_URL').$productVariable->qr_image}}"> <a href="{{env('APP_URL').$productVariable->qr_image}}" class="btn btn-outlined-primary" download>Download</a></td>
                           </tr>
                           <!-- <tr>
                               <th>QR</th>
                               <td><a href="{{env('APP_URL').$productVariable->qr_image}}" target="_blank" data-lightbox="QR_Photo"><img loading="lazy" style="width: 100px;max-height: 100px;" src="{{env('APP_URL').$productVariable->qr_image}}">
                                </a></td>
                           </tr> -->
                           <tr>
                               <th>Image</th>
                                <td><a href="{{env('APP_URL').$productVariable->primary_image}}" target="_blank" data-lightbox="Primary_Photo"><img loading="lazy" style="width: 100px;max-height: 100px;" src="{{env('APP_URL').$productVariable->primary_image}}">
                                </a>
                            </td>
                           </tr>
                           <tr>
                               <th>Other Image</th>
                                <td>
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
                                </td>
                           </tr>
                       </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
  <script type="text/javascript" src="{{url('js/lightbox.js')}}" ></script>


  <script>
    lightbox.option({
      'resizeDuration': 200,
      'wrapAround': true
    })

    function svg2img(){
        console.log("few");
    var svg = document.querySelector('svg');
    var xml = new XMLSerializer().serializeToString(svg);
    var svg64 = btoa(xml); //for utf8: btoa(unescape(encodeURIComponent(xml)))
    var b64start = 'data:image/svg+xml;base64,';
    var image64 = b64start + svg64;
    // return image64;
    var a = document.createElement("a"); //Create <a>
    a.href = b64start + image64; //Image Base64 Goes here
    a.download = "Image.svg"; //File name Here
    a.click();
    console.log("dwadwd");
}
// };svg2img()
  </script>
@endsection


