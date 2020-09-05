<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Clientes;

class ClientesController extends Controller
{
    /*****************************************
     * ACCESO A RAIZ
     ******************************************/
    public function index(){
        $json = array(
            "detalle" => "no encontrado"
        );

        return json_encode($json, true);
    }//end funcion

    /*****************************************
     * CREAR UN REGISTRO
     ******************************************/
    public function store(Request $request){
        // recoger datos
        $datos = array(
            "nombre" => $request->input("nombre"),
            "apellido" => $request->input("apellido"),
            "email" => $request->input("email")
        );

        if(empty($datos)){
            $json = array(
                "status" => 404,
                "detalle" => "Registros con errores"
            );
    
            return json_encode($json, true);
        }

        // VALIDAR DATOS
        $validator = Validator::make($datos, [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:clientes'
        ]);

        if ($validator->fails()) {

            $errores = $validator->errors();
            
            $json = array(
                "status" => 404,
                "detalle" => $errores
            );
    
            return json_encode($json, true);
        }

        $id_cliente = Hash::make($datos['nombre'].$datos['apellido'].$datos['email']);

        $llave_secreta = 
        Hash::make(
            $datos['email'].$datos['apellido'].$datos['nombre'],
            ['rounds' => 12]
            );

        $cliente = new Clientes();
        $cliente->nombre = $datos['nombre'];
        $cliente->apellido = $datos['apellido'];
        $cliente->email = $datos['email'];
        $cliente->id_cliente = str_replace('$', 'a', $id_cliente);
        $cliente->llave_secreta = str_replace('$', 'o',  $llave_secreta);
        $cliente->save();

        $json = array(
            "status" => "200",
            "detalle" => "Registro exitoso, guarde sus credenciales",
            "credenciales" => array(
                                "id_cliente" => str_replace('$', 'a',  $id_cliente),
                                "llave_secreta" => str_replace('$', 'o', $llave_secreta)
                            )
        );

        return json_encode($json, true);

    }//END FUNCTION

}//END CLASS
