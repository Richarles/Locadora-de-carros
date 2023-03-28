<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    use HasFactory;

    protected $fillable = ['nome','imagem'];

    public function rules()
    {
        return [
            'nome' => 'required|unique:marcas,nome,'.$this->id.'|min:3',
            'imagem' => 'required|file'
        ];
        /*
        unique
        1)tabela
        2)nome da colunaque será pesquisada na tabela
        3)id do reistro que será desconsiderado na pesquisa.
        */ 
    }

    public function feedback()
    {
        return [
            'required' => 'o campo :attribute é obrigatório',
            'imagem.mimes' => 'A imagem tem que ser do tipo png',
            'nome.unique' => 'o nome da marca já existe'
        ];
    }

    public function modelos(){
        //Uma marca possui muitos modelos
        return $this->hasMany('App\Models\Modelo');
    }
}
