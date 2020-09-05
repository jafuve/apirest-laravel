<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Cursos;
use App\Clientes;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CursosController extends Controller
{
    /*****************************************
     * MOSTRAR REGISTROS
     ******************************************/
    public function index(Request $request){

        $token = $request->header('Authorization');
        // echo $token . '<br>';
        $clientes = Clientes::all();

        foreach($clientes as $key => $value){
            if( "Basic ". base64_encode($value['id_cliente'].":".$value['llave_secreta']) == $token){

                // $cursos = Cursos::all();

                if(isset($_GET['page'])){
                    $cursos = DB::Table('cursos')
                    ->join('clientes', 'cursos.id_creador', '=' , 'clientes.id')
                    ->select('cursos.id', 'cursos.titulo', 'cursos.descripcion', 'cursos.instructor', 'cursos.imagen', 'cursos.id_creador', 'clientes.nombre', 'clientes.apellido')
                    ->paginate(10);
                }else{
                    $cursos = DB::Table('cursos')
                    ->join('clientes', 'cursos.id_creador', '=' , 'clientes.id')
                    ->select('cursos.id', 'cursos.titulo', 'cursos.descripcion', 'cursos.instructor', 'cursos.imagen', 'cursos.id_creador', 'clientes.nombre', 'clientes.apellido')
                    ->get();
                }

                

                if(empty($cursos)){
        
                    $json = array(
                        "status" => 200,
                        "total_registros" => 0,
                        "detalle" => "No hay ningún curso registrado"
                    );
            
                    return json_encode($json, true);
                }//END IF
        
                $json = array(
                    "status" => 200,
                    "total_registros" => count($cursos),
                    "detalle" => $cursos
                );
        
                return json_encode($json, true);
                
            }//END IF
        }

        // no existe coincidencia
        $json = array(
            "status" => 404,
            "detalle" => "No tiene autorización para recibir datos"
        );

        return json_encode($json, true);


       
    }//END CLASS

    /*****************************************
     *  CREAR UN REGISTRO
     ******************************************/
    public function store(Request $request){

        $token = $request->header('Authorization');
        $clientes = Clientes::all();

        foreach($clientes as $key => $value){

            if( "Basic ". base64_encode($value['id_cliente'].":".$value['llave_secreta']) == $token){

                // recoger datos
                $datos = array(
                    "titulo" => $request->input("titulo"),
                    "descripcion" => $request->input("descripcion"),
                    "instructor" => $request->input("instructor"),
                    "imagen" => $request->input("imagen"),
                    "precio" => $request->input("precio"),
                );

                // VALIDATE empty
                if(empty($datos)){
                    $json = array(
                        "status" => 404,
                        "detalle" => "Los registros no pueden estar vacios"
                    );
            
                    return json_encode($json, true);
                }

                 // VALIDAR DATOS
                $validator = Validator::make($datos, [
                    "titulo" => 'required|string|max:255|unique:cursos',
                    "descripcion" => 'required|string|max:255|unique:cursos',
                    "instructor" => 'required|string|max:255',
                    "imagen" => 'required|string|max:255|unique:cursos',
                    "precio" => 'required|numeric',
                ]);

                if ($validator->fails()) {
            
                    $errores = $validator->errors();

                    $json = array(
                        "status" => 404,
                        "detalle" => $errores
                    );
            
                    return json_encode($json, true);
                }//END IF

                $cursos = new Cursos();
                $cursos->titulo = $datos['titulo'];
                $cursos->descripcion = $datos['descripcion'];
                $cursos->instructor = $datos['instructor'];
                $cursos->imagen = $datos['imagen'];
                $cursos->precio = $datos['precio'];
                $cursos->id_creador = $value['id'];

                $cursos->save();

                $json = array(
                    "status" => "200",
                    "detalle" => "Registro exitoso, su curso ha sido guardado",
                );
        
                return json_encode($json, true);

            }

        }

        // no existe coincidencia
        $json = array(
            "status" => 404,
            "detalle" => "No tiene autorización para recibir datos"
        );

        return json_encode($json, true);

    }

    /*****************************************
     *  TOMAR UN REGISTRO
     ******************************************/
    public function show($id, Request $request){

        $token = $request->header('Authorization');
        $clientes = Clientes::all();

        foreach($clientes as $key => $value){

            if( "Basic ". base64_encode($value['id_cliente'].":".$value['llave_secreta']) == $token){

                $curso = Cursos::where('id', $id)->get();

                if(empty($curso)){
        
                    $json = array(
                        "status" => 200,
                        "detalle" => "No hay ningún curso registrado"
                    );
            
                    return json_encode($json, true);
                }//END IF
        
                $json = array(
                    "status" => 200,
                    "detalle" => $curso
                );
        
                return json_encode($json, true);

            }
        }

          // no existe coincidencia
        $json = array(
            "status" => 404,
            "detalle" => "No tiene autorización para recibir datos"
        );

        return json_encode($json, true);

    }//END FUNCTION

    /*****************************************
     *  EDITAR UN REGISTRO
     ******************************************/
    public function update($id, Request $request){

        $token = $request->header('Authorization');
        $clientes = Clientes::all();

        foreach($clientes as $key => $value){

            if( "Basic ". base64_encode($value['id_cliente'].":".$value['llave_secreta']) == $token){

                // recoger datos
                $datos = array(
                    "titulo" => $request->input("titulo"),
                    "descripcion" => $request->input("descripcion"),
                    "instructor" => $request->input("instructor"),
                    "imagen" => $request->input("imagen"),
                    "precio" => $request->input("precio"),
                );

                // VALIDATE empty
                if(empty($datos)){
                    $json = array(
                        "status" => 404,
                        "detalle" => "Los registros no pueden estar vacios"
                    );
            
                    return json_encode($json, true);
                }

                 // VALIDAR DATOS
                $validator = Validator::make($datos, [
                    "titulo" => 'required|string|max:255',
                    "descripcion" => 'required|string|max:255',
                    "instructor" => 'required|string|max:255',
                    "imagen" => 'required|string|max:255',
                    "precio" => 'required|numeric',
                ]);

                if ($validator->fails()) {
            
                    $json = array(
                        "status" => 404,
                        "detalle" => $validation->errors()
                        // "detalle" => "Registros con errores: posible titulo repetido, descripción repetida, imágen repetida, no se permiten caracteres especiales"
                    );
            
                    return json_encode($json, true);
                }//END IF

                $traer_curso = Cursos::where("id", $id)->get();

                if($value['id'] == $traer_curso[0]['id_creador']){

                    $datos = array(
                        "titulo" => $datos['titulo'],
                        "descripcion" => $datos['descripcion'],
                        "instructor" => $datos['instructor'],
                        "imagen" => $datos['imagen'],
                        "precio" => $datos['precio'],
                    );

                    $cursos = Cursos::where('id', $id)->update($datos);

                    $json = array(
                        "status" => "200",
                        "detalle" => "Registro actualizado",
                    );
            
                    return json_encode($json, true);
                }else{
                    $json = array(
                        "status" => 404,
                        "detalle" => "No está autorizado para modificar este curso.",
                    );
            
                    return json_encode($json, true);
                }

               

            }

        }

        // no existe coincidencia
        $json = array(
            "status" => 404,
            "detalle" => "No tiene autorización para recibir datos"
        );

        return json_encode($json, true);

    }

    /*****************************************
     *  EDITAR UN REGISTRO
     ******************************************/
    public function destroy($id, Request $request){
        $token = $request->header('Authorization');
        $clientes = Clientes::all();

        foreach($clientes as $key => $value){

            if( "Basic ". base64_encode($value['id_cliente'].":".$value['llave_secreta']) == $token){

                $validar = Cursos::where('id', $id)->get();
                
                if(empty($validar)){
                    $json = array(
                        "status" => 404,
                        "detalle" => "El curso no existe"
                    );
                    return json_encode($json, true);
                }//END IF

                if($value['id'] != $validar[0]['id_creador']){
                    $json = array(
                        "status" => 404,
                        "detalle" => "No tiene autorización para eliminar este curso"
                    );
                    return json_encode($json, true);
                }//end 

                

                $curso = Cursos::where('id', $id)->delete();

                $json = array(
                    "status" => 200,
                    "detalle" => "Se ha borrado su curso con éxito"
                );
                return json_encode($json, true);

            }

        }
    }//END FUNCTION


}
