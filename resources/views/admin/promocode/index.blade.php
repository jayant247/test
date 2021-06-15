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
                                <h4 class="card-title">Promocodes</h4>
                            </div>
                            <div>
                                <a href="{{route('promocode.create')}}" class="btn btn-primary">Create</a>
                            </div>

                        </div>
                        @if(count($promocodes)>0)

                        <div class="table-responsive">

                            <table id="promocode-table" class="table table-striped table-bordered zero-configuration">
                                <thead>
                                    <tr>
                                        <th>Promocode</th>
                                        <th>Type</th>
                                        <th>Discount</th>
                                        <th>Max Discount</th>
                                        <th>Is Active</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($promocodes as $promocode)
                                    <tr>
                                        <td>{{$promocode->promocode}}</td>
                                        <td>{{$promocode->type}}</td>
                                        <td>{{$promocode->discount}}</td>
                                        <td>{{$promocode->max_discount}}</td>
                                        <td>{{$promocode->is_active}}</td>
                                        <td>
                                            <button data-toggle="modal" onclick="openDetailsModal({{$promocode->id}})"  class="btn btn-sm btn-outline-dark" href="{{route('promocode.show',$promocode->id)}}">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                            <a class="btn btn-sm btn-outline-dark" href="{{route('promocode.edit',$promocode->id)}}">
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
   @if(count($promocodes)>0)
       @foreach($promocodes as $promocode)
           <div class="modal fade bd-example-modal-lg" id="{{$promocode->id}}" tabindex="-1" role="dialog" aria-hidden="true">
               <div class="modal-dialog modal-lg">
                   <div class="modal-content">
                       <div class="modal-header">
                           <h5 class="modal-title">Promocode Details</h5>
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
                                           <td>Promocode</td>
                                           <td>{{$promocode->promocode}}</td>
                                       </tr>
                                       <tr>
                                           <td>Type</td>
                                           <td>{{$promocode->type}}</td>
                                       </tr>
                                       <tr>
                                           <td>Discount</td>
                                           <td>{{$promocode->discount}}</td>
                                       </tr>
                                       <tr>
                                           <td>Minimum Cart Value</td>
                                           <td>{{$promocode->minimal_cart_total}}</td>
                                       </tr>
                                       <tr>
                                           <td>Maximum Discount</td>
                                           <td>{{$promocode->max_discount}}</td>
                                       </tr>
                                       <tr>
                                           <td>Only For new Users</td>
                                           <td>{{$promocode->is_for_new_user?'True':'False'}}</td>
                                       </tr>                                       
                                       <tr>
                                           <td>Start Date</td>
                                           <td>{{$promocode->start_from}}</td>
                                       </tr>
                                       <tr>
                                           <td>Last Date</td>
                                           <td>{{$promocode->end_on}}</td>
                                       </tr>
                                       <tr>
                                           <td>Description</td>
                                           <td>{{$promocode->description}}</td>
                                       </tr>
                                       <tr>
                                           <td>Is Active</td>
                                           <td>{{$promocode->is_active}}</td>
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
        let promocodes = {!! $promocodes !!}
        if(promocodes.length>0){
            $(document).ready( function () {
                $('#promocode-table').DataTable({
                    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
                });
            } );
        }
        function openDetailsModal(id) {
            $("#"+id).modal()
        }
    </script>
@endsection


