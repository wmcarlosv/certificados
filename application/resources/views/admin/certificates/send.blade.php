@extends('layouts.app')

@section('content')
<div class="container">
    @include('flash::message')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ $title }}</div>
                <div class="card-body">
                    {!! Form::open(['route' => ['store_send', $data->id]]) !!}
                        <ul class="nav nav-tabs">
                          <li class="active"><a data-toggle="tab" href="#certificate">Certificado</a></li>
                          <li><a data-toggle="tab" href="#students">Estudiantes</a></li>
                        </ul>

                        <div class="tab-content">
                          <div id="certificate" class="tab-pane fade in active">
                                <div class="form-group">
                                    <label>Titulo</label>
                                    <input type="text" readonly="readonly" class="form-control" value="{{ $data->title }}">
                                </div>
                                <div class="form-group">
                                    <label>Cabecera</label>
                                    <input type="text" readonly="readonly" class="form-control" value="{{ $data->header }}">
                                </div>
                                <div class="form-group">
                                    <label>Asunto</label>
                                    <input type="text" readonly="readonly" class="form-control" value="{{ $data->subject }}">
                                </div>
                                <div class="form-group">
                                    <label>Fondo:</label>
                                    <img src="{{ asset('application/storage/app/'.$data->background) }}" class="img-thumbnail" width="200" height="200">
                                    <a href="{{ route('preview_pdf',$data->id) }}" target="_blank" class="btn btn-success">Preview</a>
                                </div>
                          </div>
                          <div id="students" class="tab-pane fade">
                            <br />
                            <div class="form-group">
                                <label>Archivo CSV</label>
                                <input type="file" class="form-control" name="csv" id="csv">
                            </div>

                            <button type="button" id="load_csv_file" class="btn btn-success">Cargar Archivo</button>
                            <br />
                            <br />

                            <table class="table table-bordered table-striped">
                                <thead>
                                    <th>DNI</th>
                                    <th>Nombres</th>
                                    <th>Apellidos</th>
                                    <th>Correo</th>
                                </thead>
                                <tbody id="load_stuents">
                                    <tr>
                                        <td colspan="4"><center>Sin Datos</center></td>
                                    </tr>
                                </tbody>
                            </table>
                          </div>
                        </div>
                        <button class="btn btn-success">Enviar</button>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
<script type="text/javascript">
    $(document).ready(function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#load_csv_file').click(function(){    
            //on change event  
            formdata = new FormData();
            if($("#csv").prop('files').length > 0)
            {
                file = $("#csv").prop('files')[0];
                formdata.append("csv", file);
            }

            $.ajax({
                url: "{{ route('load_cvs_fle') }}",
                type: "POST",
                data: formdata,
                processData: false,
                contentType: false,
                success: function (result) {

                     var data = JSON.parse(result);
                     $("#load_stuents").empty();
                     $.each(data, function(index, value){
                        let html = "<tr>";
                        let name = "";
                        if(value.dni){  
                            html+="<td><input type='hidden' name='dni[]' value='"+value.dni+"' />"+value.dni+"</td>";
                        }else{
                            html+="<td><input type='hidden' name='dni[]' value='' /></td>";
                        }

                        if(value.firts_name){
                            html+="<td><input type='hidden' name='firts_name[]' value='"+value.firts_name+"' />"+value.firts_name+"</td>";
                        }else{
                            html+="<td><input type='hidden' name='firts_name[]' value='' /></td>";
                        }

                        if(value.last_name){
                            html+="<td><input type='hidden' name='last_name[]' value='"+value.last_name+"' />"+value.last_name+"</td>";
                        }else{
                            html+="<td><input type='hidden' name='last_name[]' value='' /></td>";
                        }

                        if(value.email){
                            html+="<td><input type='hidden' value='"+value.email+"' name='emails[]'>"+value.email+"</td>";
                        }else{
                            html+="<td><input type='hidden' value='' name='emails[]'></td>";
                        }
                        html+="<input type='hidden' name='full_name[]' value='"+name+"' />";
                        html+= "</tr>";

                        $("#load_stuents").append(html);

                        html = "";
                     });
                }
            });
        });
    });
</script>
@stop
