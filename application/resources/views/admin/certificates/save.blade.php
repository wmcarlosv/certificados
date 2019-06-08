@extends('layouts.app')

@section('content')
<div class="container">

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ $title }}</div>

                <div class="card-body">
                   
                   @if($action == 'new')
                    {!! Form::open(['route' => 'certificates.store', 'files' => true, 'method' => 'POST']) !!}
                   @else
                    {!! Form::open(['route' => ['certificates.update',@$data->id], 'files' => true, 'method' => 'PUT']) !!}
                   @endif
                        <div class="form-group">
                            <label for="title">Titulo: </label>
                            <input class="form-control" type="text" name="title" id="title" value="{{ @$data->title }}">
                        </div>
                        @if($action == "edit")
                            @if(!empty($data->background))
                                <div class="content-image">
                                    <img src="{{ asset('application/storage/app/'.$data->background) }}" class="img-thumbnail" width="300" height="300">
                                    <button type="button" data-url="{{ route('certificates.show',['id' => $data->id]) }}" class="btn btn-danger delete-image">Delete</button>
                                </div>
                                <br />
                                <div class="form-group hide-file" style="display:none;">
                                    <label for="background">Imagen de Fondo: </label>
                                    <input class="form-control" type="file" name="background" id="background" />
                                </div>
                            @else
                                <div class="form-group">
                                    <label for="background">Imagen de Fondo: </label>
                                    <input class="form-control" type="file" name="background" id="background" />
                                </div>
                            @endif
                        @else
                            <div class="form-group">
                                <label for="background">Imagen de Fondo: </label>
                                <input class="form-control" type="file" name="background" id="background" />
                            </div>
                        @endif
                        <div class="form-group">
                            <label for="content">Contenido: </label>
                            <textarea class="form-control super-editor" type="text" name="content" id="content">{{ @$data->content }}</textarea>
                        </div>
                        <button class="btn btn-success">Save</button>
                        <a href="{{ route('certificates.index') }}" class="btn btn-danger">Cancel</a>
                   {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
