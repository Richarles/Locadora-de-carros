<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Models\Modelo;
use App\Repositories\ModeloRepository;
use Illuminate\Http\Request;

class ModeloController extends Controller
{
    public function __construct(Modelo $modelo)
    {
        $this->modelo = $modelo;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $modeloRepository = new ModeloRepository($this->modelo);
        //$marcas = array();

        if($request->has('atributos_marca')){
            $atributos_marca = 'marca:id,'.$request->atributos_marca;
            $modeloRepository->selectAtributosRegistrosRelacionados($atributos_marca);
        }else{
            $modeloRepository->selectAtributosRegistrosRelacionados('modelos');
        }

        if($request->has('filtro')){
            $modeloRepository->filtro($request->filtro);
        }

        if($request->has('atributos')){
            $modeloRepository->selectAtributo($request->atributos);
        }
        //$marca = Marca::all();
        //$getMarcas = $this->marca->with('modelos')->get();

        return response()->json($modeloRepository->getResultado(),200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->modelo->rules());
        
        $imagem = $request->file('imagem');
        $imagemModelo = $imagem->store('imagens/modelos','public');

        $createModelo = $this->modelo->create([
            'marca_id'      => $request->marca_id,
            'nome'          => $request->nome,
            'imagem'        => $imagemModelo,
            'numero_portas' => $request->numero_portas,
            'lugares'       => $request->lugares,
            'air_bag'       => $request->air_bag,
            'abs'           => $request->abs
        ]);
        
        return response()->json($createModelo,201);
    }

    /**
     * Display the specified resource.
     *
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $showModelo = $this->modelo->with('marca')->find($id);

        if($showModelo === null){
            return  response()->json(['erro' => 'Registro não existe'],404) ;
        }
        return response()->json($showModelo,200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function edit(Modelo $modelo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $updateModelo = $this->modelo->find($id);

        if($updateModelo === null){
            return response()->json(['erro' => 'Registro não existe para a atualização'],404);
        }

        if($request->method() === 'PATCH'){
            $regrasDinamicas = array();
            
            //percorrendo todas as regras defindas na model
            foreach($updateModelo->rules() as $input => $regras){
                //coleta apenas as regras aplicaveis aos parametros parciais da requisição
                if( array_key_exists($input,$request->all()) ){
                    $regrasDinamicas[$input] = $regras;
                }
            }

            $request->validate($regrasDinamicas);

        }else{
            $request->validate($updateModelo->rules());
        }

        //remove o arquivo antigo caso um novo arquivo tenho sido enviado na request
        if($request->file('imagem')){
            Storage::disk('public')->delete($updateModelo->imagem);
        }
        
        $imagem = $request->file('imagem');
        $imagemModelo = $imagem->store('imagens/modelos','public');

        $updateModelo->fill($request->all());
        $updateModelo->imagem = $imagemModelo;
        $updateModelo->save();

        /* $updateModelo->update([
            'marca_id'      => $request->marca_id,
            'nome'          => $request->nome,
            'imagem'        => $imagemModelo,
            'numero_portas' => $request->numero_portas,
            'lugares'       => $request->lugares,
            'air_bag'       => $request->air_bag,
            'abs'           => $request->abs
        ]); */

        return response()->json($updateModelo,200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deleteModelo = $this->marca->find($id);

        if($deleteModelo === null){
            return response()->json(['erro' => 'Registro não existe para a exclusão'],404);
        }

        //remove o arquivo antigo 
        Storage::disk('public')->delete($deleteModelo->imagem);

        $deleteModelo->delete();

        return response()->json(['msg'=>'Modelo deletada co sucesso'],200);
    }
}
