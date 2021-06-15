@extends('layouts.layout')

@section('css')
@endsection

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Category Details</h4>
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
                            <td>Parent Category</td>
                            <td><a href="{{route('category.show',$category->parentCategory->id)}}"> {{$category->parentCategory->category_name}} </a></td>
                        </tr>
                        <tr>
                            <td>Type</td>
                            <td>{{$category->type}}</td>
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


    </div>
@endsection

@section('js')
@endsection


