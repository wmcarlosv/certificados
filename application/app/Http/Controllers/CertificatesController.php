<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\UrlGenerator;
use App\Certificate;
use Excel;
use Mail;
use PDF;

class CertificatesController extends Controller
{
    private $view = 'admin.certificates.';
    private $title = "";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Certificate::all();
        $this->title = "Certificados";
        return view($this->view."index",['data' => $data, 'title' => $this->title ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->title = "Nuevo Certificado";
        return view($this->view."save",['action' => 'new', 'title' => $this->title]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required'
        ]);

        $object = new Certificate();
        $object->title = $request->input('title');
        if($request->hasFile("background")){
            $object->background = $request->background->store("certificates/backgrounds");
        }else{
            $object->background = NULL;
        }
        $object->content = $request->input("content");

        if($object->save()){
            flash("Registro insertado con Exito!!")->success();
        }else{
            flash("Error al intentan insertar el Registro!!")->error();
        }

        return redirect()->route('certificates.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $object = Certificate::findorfail($id);
        Storage::delete($object->background);
        $object->background = NULL;
        if($object->update()){
            print json_encode(['deleted' => 'yes']);
        }else{
            print json_encode(['deleted' => 'no']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Certificate::findorfail($id);
        $this->title = "Editar Certificado";
        return view($this->view."save",['action' => 'edit', 'data' => $data, 'title' => $this->title]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required'
        ]);

        $object = Certificate::findorfail($id);
        $object->title = $request->input('title');

        if($request->hasFile("background")){
            $object->background = $request->background->store("certificates/backgrounds");
        }

        $object->content = $request->input("content");

        if($object->save()){
            flash("Registro Actualizado con Exito!!")->success();
        }else{
            flash("Error al intentan Actualizar el Registro!!")->error();
        }

        return redirect()->route('certificates.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $object = Certificate::findorfail($id);
        Storage::delete($object->background);
        if($object->delete()){
            flash("Registro Eliminado con Exito!!")->success();
        }else{
            flash("Error al tratar de Eliminar el Registro!!")->error();
        }
        return redirect()->route('certificates.index');
    }

    public function send_certificate($id){
        $data = Certificate::findorfail($id);
        $this->title = "Enviar Certificado";
        return view($this->view."send",['title' => $this->title, 'data' => $data]);
    }

    public function load_cvs_fle(Request $request){

        $path = $request->file('csv')->getRealPath();
        $data = Excel::load($path)->get();
        $arr = [];
        $contador = 0;
        foreach($data as $key => $value){

            if(isset($value->correo_electra3nico_facturacia3n) and !empty($value->correo_electra3nico_facturacia3n)){
                $arr[$contador]['dni'] = $value->dni;
                $arr[$contador]['firts_name'] = $value->nombre_facturacia3n;
                $arr[$contador]['last_name'] = $value->apellido_facturacia3n;
                $arr[$contador]['email'] = $value->correo_electra3nico_facturacia3n;
                $contador++;
            }
        }

        print json_encode($arr);
    }

    public function store_send($id, Request $request){

        $data = Certificate::findorfail($id);
        $user = [];

        for($i = 0; $i < count($request->input('emails')); $i++){

            $user['email'] = $request->input('emails')[$i];
            $user['firts_name'] = $request->input('firts_name')[$i];
            $user['last_name'] = $request->input('last_name')[$i];
            $user['dni'] = $request->input('dni')[$i];
            $user['id'] = $id;

            $url = url('/') . "/certificate/".$id."/".$user['email'];

            Mail::send('admin.certificates.mail', ['data' => $data, 'url' => $url], function ($m) use ($user) {
                $m->from('certificadosreitigh@gmail.com', 'Certificado Otorgado');

                $m->to($user['email'], $user['firts_name']." ".$user['last_name'])->subject('Nuevo Certificado para Usted');
                $m->attachData($this->attachment_pdf($user['id'],$user['email']),'Certificado.pdf');
            });

            $validar = DB::select('select * from sends where certificate_id = '.$id.' and email = "'.$user['email'].'"');

            if(count($validar) == 0){

                DB::table('sends')->insert([
                    'certificate_id' => $id,
                    'first_name' => $user['firts_name'],
                    'last_name' => $user['last_name'],
                    'dni' => $user['dni'],
                    'email' => $user['email']
                ]);  

            }
            
            $user = [];
            $url = "";
        }

        flash("Correos Enviados Con Exito!!!")->success(); 

        return redirect()->route('certificates.index');
    }

    public function preview_pdf($id){
        $data = Certificate::findorfail($id);
        $pdf = PDF::loadView('admin.certificates.preview',['data' => $data])->setPaper('a4','landscape');
        return $pdf->stream();
    }

    public function view_certificate($id,$email){

        $data = Certificate::findorfail($id);
        $student = DB::select('select * from sends where email = "'.$email.'"')[0];
        $first_name = $student->first_name;
        $last_name = $student->last_name;
        $dni = $student->dni;

        $content = str_replace("{first_name}", $first_name, $data->content);
        $content = str_replace("{last_name}", $last_name, $content);
        $content = str_replace("{dni}", $dni, $content);

        $pdf = PDF::loadView('admin.certificates.pdf',['data' => $data, 'content' => $content])->setPaper('a4','landscape');
        return $pdf->stream();

    }

    public function attachment_pdf($id,$email){

        $data = Certificate::findorfail($id);
        $student = DB::select('select * from sends where email = "'.$email.'"')[0];
        $first_name = $student->first_name;
        $last_name = $student->last_name;
        $dni = $student->dni;

        $content = str_replace("{first_name}", $first_name, $data->content);
        $content = str_replace("{last_name}", $last_name, $content);
        $content = str_replace("{dni}", $dni, $content);

        $pdf = PDF::loadView('admin.certificates.pdf',['data' => $data, 'content' => $content])->setPaper('a4','landscape');
        return $pdf->output();

    }
}
