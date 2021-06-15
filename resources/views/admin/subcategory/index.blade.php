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
                                <h4 class="card-title">Sub - Categories</h4>
                            </div>
                            <div>
                                <a href="{{route('subcategory.create')}}" class="btn btn-primary">Create</a>
                            </div>

                        </div>
                        @if(count($categories)>0)

                        <div class="table-responsive">

                            <table id="category-table" class="table table-striped table-bordered zero-configuration">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Is Big Thumbnail Show</th>
                                        <th>Type</th>
                                        <th>Parent Category Name</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($categories as $category)
                                    <tr>
                                        <td>{{$category->category_name}}</td>
                                        <td>{{$category->is_bigthumbnail_show?'True':'False'}}</td>
                                        <td>{{$category->type}}</td>
                                        <td><a href="{{route('category.show',$category->parentCategory->id)}}"> {{$category->parentCategory->category_name}} </a></td>

                                        <td>
                                            <button data-toggle="modal" onclick="openDetailsModal({{$category->id}})"  class="btn btn-sm btn-outline-dark" href="{{route('category.show',$category->id)}}">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                            <a class="btn btn-sm btn-outline-dark" href="{{route('subcategory.edit',$category->id)}}">
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
   @if(count($categories)>0)
       @foreach($categories as $category)
           <div class="modal fade bd-example-modal-lg" id="{{$category->id}}" tabindex="-1" role="dialog" aria-hidden="true">
               <div class="modal-dialog modal-lg">
                   <div class="modal-content">
                       <div class="modal-header">
                           <h5 class="modal-title">Sub Category Details</h5>
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
                                           <td>{{$category->category_name}}</td>
                                       </tr>
                                       <tr>
                                           <td>Is Big Thumbnail Show</td>
                                           <td>{{$category->is_bigthumbnail_show?'True':'False'}}</td>
                                       </tr>
                                       <tr>
                                           <td>Type</td>
                                           <td>{{$category->type}}</td>
                                       </tr>
                                       <tr>
                                           <td>Parent Category</td>
                                           <td><a href="{{route('category.show',$category->parentCategory->id)}}"> {{$category->parentCategory->category_name}} </a></td>
                                       </tr>
                                       <tr>
                                           <td>Thumbnail</td>
                                           <td>
                                               <a href="{{env('APP_URL').$category->category_thumbnail}}" target="_blank">
                                                   <img loading="lazy" style="width: 100px;max-height: 100px;" src="{{env('APP_URL').$category->category_thumbnail}}">
                                               </a>

                                           </td></tr>
                                       <tr>
                                           <td>Big Thumbnail</td>
                                           <td>
                                               <a href="{{env('APP_URL').$category->big_thumbnail}}" target="_blank">
                                                   <img loading="lazy" style="width: 100px;max-height: 100px;" src="{{env('APP_URL').$category->big_thumbnail}}">
                                               </a>

                                           </td>
                                       </tr>
                                       <tr>
                                           <td>Square Thumbnail</td>
                                           <td>
                                               <a href="{{env('APP_URL').$category->square_thumbnail}}" target="_blank">
                                                   <img loading="lazy" style="width: 100px;max-height: 100px;" src="{{env('APP_URL').$category->square_thumbnail}}">
                                               </a>

                                           </td>
                                       </tr>
                                       <tr>
                                           <td>New Page Thumnail</td>
                                           <td>
                                               <a href="{{env('APP_URL').$category->new_page_thumbnail}}" target="_blank">
                                                   <img loading="lazy" style="width: 100px;max-height: 100px;" src="{{env('APP_URL').$category->new_page_thumbnail}}">
                                               </a>

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
        let categories = {!! $categories !!}
        if(categories.length>0){
            $(document).ready( function () {
                $('#category-table').DataTable({
                    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
                });
            } );
        }
        function openDetailsModal(id) {
            $("#"+id).modal()
        }
    </script>
@endsection


