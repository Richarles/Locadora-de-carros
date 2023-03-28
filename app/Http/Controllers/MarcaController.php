<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Models\Marca;
use App\Models\Modelo;
use App\Repositories\MarcaRepository;
use CreateMarcasTable;
use Illuminate\Http\Request;

class MarcaController extends Controller
{
    public function __construct(Marca $marca)
    {
        $this->marca = $marca;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $marcaRepository = new MarcaRepository($this->marca);
        //$marcas = array();

        if($request->has('atributos_modelo')){
            $atributos_modelo = 'modelos:id,'.$request->atributos_modelo;
            $marcaRepository->selectAtributosRegistrosRelacionados($atributos_modelo);
        }else{
            $marcaRepository->selectAtributosRegistrosRelacionados('modelos');
        }

        if($request->has('filtro')){
            $marcaRepository->filtro($request->filtro);
        }

        if($request->has('atributos')){
            $marcaRepository->selectAtributo($request->atributos);
        }
        //$marca = Marca::all();
        //$getMarcas = $this->marca->with('modelos')->get();

        return response()->json($marcaRepository->getResultadoPaginado(3),200);
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
        $request->validate($this->marca->rules(),$this->marca->feedback());
        //$marca = Marca::create($request->all());
        //
        $imagem = $request->file('imagem');
        $imagemMarca = $imagem->store('imagens','public');

        $createMarca = $this->marca->create([
            'nome' => $request->nome,
            'imagem' => $imagemMarca
        ]);
        
        return response()->json($createMarca,201);
    }

    /**
     * Display the specified resource.
     *
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $showMarca = $this->marca->with('modelos')->find($id);

        if($showMarca === null){
            return  response()->json(['erro' => 'Registro não existe'],404) ;
        }
        return response()->json($showMarca,200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Marca  $marca
     * @return \Illuminate\Http\Response
     */
    public function edit(Marca $marca)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        /*print_r($request->all()); //os dados atualizados
        echo '<hr>';
        print_r($marca->getAttributes()); //os dados antigos */

        $updateMarca = $this->marca->find($id);

        if($updateMarca === null){
            return response()->json(['erro' => 'Registro não existe para a atualização'],404);
        }

        if($request->method() === 'PATCH'){
            $regrasDinamicas = array();
            
            //percorrendo todas as regras defindas na model
            foreach($updateMarca->rules() as $input => $regras){
                //coleta apenas as regras aplicaveis aos parametros parciais da requisição
                if(array_key_exists($input,$request->all())){
                    $regrasDinamicas[$input] = $regras;
                }
            }

            $request->validate($regrasDinamicas, $updateMarca->feedback());

        }else{
            $request->validate($updateMarca->rules(),$updateMarca->feedback());
        }

        //remove o arquivo antigo caso um novo arquivo tenho sido enviado na request
        // if($request->file('imagem')){
        //     Storage::disk('public')->delete($updateMarca->imagem);
        // }

        $updateMarca->fill($request->all());

        if($request->file('imagem')){
            Storage::disk('public')->delete($updateMarca->imagem);

            $imagem = $request->file('imagem');
            $imagemMarca = $imagem->store('imagens','public');
            $updateMarca->imagem = $imagemMarca;
        }
        
        $updateMarca->save();

        return response()->json($updateMarca,200);
        
        /*$imagem = $request->file('imagem');
        $imagemMarca = $imagem->store('imagens','public');

        //preencher o objeto $updateMarca com dados do request
        $updateMarca->fill($request->all());
        $updateMarca->imagem = $imagemMarca;
        $updateMarca->save();*/

        /* $updateMarca->update([
           'nome'   => $request->nome,
           'imagem' => $imagemMarca
        ]); */

        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deleteMarca = $this->marca->find($id);

        if($deleteMarca === null){
            return response()->json(['erro' => 'Registro não existe para a exclusão'],404);
        }

        //remove o arquivo antigo 
        Storage::disk('public')->delete($deleteMarca->imagem);

        $deleteMarca->delete();

        return response()->json(['msg'=>'Marca deletada co sucesso'],200);
    }
}
