@extends('layouts.layout')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.16/css/dataTables.bootstrap4.min.css" integrity="sha512-NDWv4n2v59EOoj+dDvXfD4uGGTCOXkLAnm+DhQtyYxpZL4lMSymTX5HD8i5NEcF+1YLBkgvByardYsJaA1W6MA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row d-flex justify-content-between">
                            <div>
                                <h4 class="card-title">Pincodes</h4>
                            </div>
                            <div>
                                <a href="{{route('pincode.create')}}" class="btn btn-primary">Create</a>
                            </div>

                        </div>
                        @if(count($pincodes)>0)

                        <div class="table-responsive">

                            <table id="pincode-table" class="table table-striped table-bordered zero-configuration">
                                <thead>
                                    <tr>
                                        <th>Pincode</th>
                                        <th>Is Active</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($pincodes as $pincode)
                                    <tr>
                                        <td>{{$pincode->pincode}}</td>
                                        <td>{{$pincode->is_active}}</td>
                                        <td>
                                            <a data-toggle="modal" onclick="openDetailsModal({{$pincode->id}})"  class="btn btn-sm btn-outline-dark" href="{{route('pincode.show',$pincode->id)}}">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a class="btn btn-sm btn-outline-dark" href="{{route('pincode.edit',$pincode->id)}}">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                            <a class="btn btn-sm btn-outline-dark">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                            <div class="text-center">
                                <h5>No Data Available</h5>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>


{{--    Modal For Show Option--}}
   @if(count($pincodes)>0)
       @foreach($pincodes as $pincode)
           <div class="modal fade bd-example-modal-lg" id="{{$pincode->id}}" tabindex="-1" permission="dialog" aria-hidden="true">
               <div class="modal-dialog modal-lg">
                   <div class="modal-content">
                       <div class="modal-header">
                           <h5 class="modal-title">Pincode Details</h5>
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
                                           <td>Pincode</td>
                                           <td>{{$pincode->pincode}}</td>
                                       </tr>
                                       <tr>
                                           <td>Is Active</td>
                                           <td>{{$pincode->is_active}}</td>
                                       </tr>
                                       </tbody>
                                   </table>
                               </div>


                           </div>
                       </div>
                       <div class="modal-footer">
                           <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
{{--                           <button type="button" class="btn btn-primary">Save changes</button>--}}
                       </div>
                   </div>
               </div>
           </div>
       @endforeach
   @endif



@endsection

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.16/js/jquery.dataTables.min.js" integrity="sha512-yCkOYsxpzPSpcbHspsH6A28Z0cgsfjJhlR78nPAfLLZSSV40tVN4VQ6ES/miqI/1z8a5FWVYwCF145+eyJx9Tw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.16/js/dataTables.bootstrap4.min.js" integrity="sha512-2wDq7VuYclJFDG5YbUbmOEWYtTEs/DwpKa9maNvC8gIhEHyR/rgh1BuyUrPZy00H8/DGlLAwbYwSpzCRV0dQJw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        let pincodes = {!! $pincodes !!}
        if(pincodes.length>0){
            $(document).ready( function () {
                $('#pincode-table').DataTable({
                    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
                });
            } );
        }
        function openDetailsModal(id) {
            $("#"+id).modal()
        }
    </script>
@endsection


