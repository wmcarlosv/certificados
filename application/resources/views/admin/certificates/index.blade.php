@extends('layouts.app')

@section('content')
<div class="container">
    @include('flash::message')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ $title }}</div>

                <div class="card-body">
                    <a href="{{ route('certificates.create') }}" class="btn btn-success">New</a>
                    <br />
                    <br />
                    <table class="table table-borderd table-striped data-table">
                        <thead>
                            <th>ID</th>
                            <th>Titulo</th>
                            <th>Cabecera</th>
                            <th>Asunto</th>
                            <th>Fondo</th>
                            <th>/</th>
                        </thead>
                        <tbody>
                            @foreach($data as $d)
                            <tr>
                                <td>{{ $d->id }}</td>
                                <td>{{ $d->title }}</td>
                                <td>{{ $d->header }}</td>
                                <td>{{ $d->subject }}</td>
                                <td>
                                    @if(isset($d->background) and !empty($d->background))
                                        <img src="{{ asset('application/storage/app/'.$d->background) }}" class="img-thumbnail" width="150" height="150">
                                    @else
                                        <center>Sin Imagen</center>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('certificates.edit',['id' => $d->id]) }}" class="btn btn-info">Edit</a>
                                    {!! Form::open(['route' => ['certificates.destroy', $d->id], 'method' => 'DELETE', 'style' => 'display:inline;']) !!}
                                        <button class="btn btn-danger delete-row" type="sumit">Delete</button>
                                    {!! Form::close() !!}

                                    <a href="{{ route('preview_pdf',$d->id) }}" target="_blank" class="btn btn-success">Preview</a>

                                    <a href="{{ route('send_certificate',['id' => $d->id]) }}" class="btn btn-success">Send</a>
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
@endsection
