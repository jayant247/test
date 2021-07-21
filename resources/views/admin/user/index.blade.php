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
                                <h4 class="card-title">Users</h4>
                            </div>
                            <div>
                                <a href="{{route('user.create')}}" class="btn btn-primary">Create</a>
                            </div>

                        </div>


                        <div class="table-responsive">

                            <table id="user-table" class="table table-striped table-bordered zero-configuration">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Mobile</th>
                                        <th>Role</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{$user->name}}</td>
                                        <td>{{$user->email}}</td>
                                        <td>{{$user->mobile_no}}</td>
                                        <td>
                                          @foreach($user->roles as $item)
                                            @if($item->name != 'Customer')
                                              <span class="badge badge-info">{{ $item->name }}</span>
                                            @endif
                                          @endforeach
                                        </td>
                                        <td>
                                            <form action="{{ route('user.destroy',$user->id) }}" method="POST">
                                                <a data-toggle="modal" onclick="openDetailsModal({{$user->id}})"  class="btn btn-sm btn-outline-dark" >
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a class="btn btn-sm btn-outline-dark" href="{{route('user.edit',$user->id)}}" >
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

                    </div>
                </div>
            </div>
        </div>
    </div>


{{--    Modal For Show Option--}}
   @if(count($users)>0)
       @foreach($users as $user)
           <div class="modal fade bd-example-modal-lg" id="{{$user->id}}" tabindex="-1" role="dialog" aria-hidden="true">
               <div class="modal-dialog modal-lg">
                   <div class="modal-content">
                       <div class="modal-header">
                           <h5 class="modal-title">User Details</h5>
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
                                           <td>Name</td>
                                           <td>{{$user->name}}</td>
                                       </tr>
                                       <tr>
                                           <td>Email</td>
                                           <td>{{$user->email}}</td>
                                       </tr>
                                       <tr>
                                           <td>Mobile</td>
                                           <td>{{$user->mobile_no}}</td>
                                       </tr>
                                       <tr>
                                           <td>Mobile</td>
                                           <td>
                                              @foreach($user->roles as $item)
                                                @if($item->name != 'Customer')
                                                  <span class="badge badge-info">{{ $item->name }}</span>
                                                @endif
                                              @endforeach
                                            </td>
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
        let users = {!! $users !!}
        if(users.length>0){
            $(document).ready( function () {
                $('#user-table').DataTable({
                    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
                });
            } );
        }
        function openDetailsModal(id) {
            $("#"+id).modal()
        }
    </script>
@endsection


