<?php

namespace App\Http\Controllers;

use App\Models\Carro;
use App\Http\Requests\StoreCarroRequest;
use App\Http\Requests\UpdateCarroRequest;
use App\Repositories\CarroRepository;
use Illuminate\Http\Request;

class CarroController extends Controller
{

    public function __construct(Carro $carro)
    {
        $this->carro = $carro;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $carroRepository = new CarroRepository($this->carro);
        //$marcas = array();

        if($request->has('atributos_modelo')){
            $atributos_modelo = 'modelo:id,'.$request->atributos_modelo;
            $carroRepository->selectAtributosRegistrosRelacionados($atributos_modelo);
        }else{
            $carroRepository->selectAtributosRegistrosRelacionados('modelo');
        }

        if($request->has('filtro')){
            $carroRepository->filtro($request->filtro);
        }

        if($request->has('atributos')){
            $carroRepository->selectAtributo($request->atributos);
        }
        //$marca = Marca::all();
        //$getMarcas = $this->marca->with('modelos')->get();

        return response()->json($carroRepository->getResultado(),200);
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
     * @param  \App\Http\Requests\StoreCarroRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->carro->rules());
        
        $createCarro = $this->marca->create([
            'modelo_id'  => $request->modelo_id,
            'placa'      => $request->placa,
            'disponivel' => $request->disponivel,
            'km'         => $request->km
        ]);
        
        return response()->json($createCarro,201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Carro  $carro
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $showCarro = $this->carro->with('modelo')->find($id);

        if($showCarro === null){
            return  response()->json(['erro' => 'Registro não existe'],404) ;
        }
        return response()->json($showCarro,200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Carro  $carro
     * @return \Illuminate\Http\Response
     */
    public function edit(Carro $carro)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCarroRequest  $request
     * @param  \App\Models\Carro  $carro
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCarroRequest $request, $id)
    {
        $updateCarro = $this->carro->find($id);

        if($updateCarro === null){
            return response()->json(['erro' => 'Registro não existe para a atualização'],404);
        }

        if($request->method() === 'PATCH'){
            $regrasDinamicas = array();
            
            //percorrendo todas as regras defindas na model
            foreach($updateCarro->rules() as $input => $regras){
                //coleta apenas as regras aplicaveis aos parametros parciais da requisição
                if(array_key_exists($input,$request->all())){
                    $regrasDinamicas[$input] = $regras;
                }
            }

            $request->validate($regrasDinamicas);

        }else{
            $request->validate($updateCarro->rules());
        }
        
        //preencher o objeto $updateMarca com dados do request
        $updateCarro->fill($request->all());
        $updateCarro->save();

        return response()->json($updateCarro,200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Carro  $carro
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deleteCarro = $this->carro->find($id);

        if($deleteCarro === null){
            return response()->json(['erro' => 'Registro não existe para a exclusão'],404);
        }

        $deleteCarro->delete();

        return response()->json(['msg'=>'Marca deletada co sucesso'],200);
       
    }
}
