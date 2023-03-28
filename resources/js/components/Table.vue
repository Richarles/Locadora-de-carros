<template>
<div>
    <table class="table table-hover">
        <thead>
            <tr>
                <th scope="col" v-for="t,key in titulos" :key="key">{{ t.titulo }}</th>
                <th v-if="visualizar.visivel || atualizar || remover"></th>
            </tr>
        </thead>
        <tbody>
        <tr v-for="obj,chave in dadosFiltrados" :key="chave">
            <td v-for="valor,chaveValor in obj" :key="chaveValor">
                <span v-if="titulos[chaveValor].tipo == 'texto'">{{ valor }}</span>
                <span v-if="titulos[chaveValor].tipo == 'imagem'"><img :src="'/storage/'+valor" width="30" height="30"></span>
                <span v-if="titulos[chaveValor].tipo == 'data'">{{ valor | formataDataTempoGlobal }}</span>
            </td>
            <td v-if="visualizar.visivel || atualizar || remover" >
                <button v-if="visualizar.visivel" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#modalVizualizarMarca" @click="setStore(obj)">Visualizar</button>
                <button v-if="atualizar" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#modalAtualizarMarca" @click="setStore(obj)">Atualizar</button>
                <button v-if="remover" class="btn btn-outline-danger btn-sm" data-toggle="modal" data-target="#modalRemoverMarca" @click="setStore(obj)">Remover</button>
            </td>
        </tr>
        </tbody>
    </table>
</div>
</template>

<script>
    export default {
        props:['dados','titulos','visualizar','atualizar','remover'],
        methods: {
            setStore(obj){
                this.$store.state.transacao.status = ''
                this.$store.state.transacao.mensagem = ''
                this.$store.state.transacao.dados = ''
                this.$store.state.item = obj
                //console.log(obj)
            }
        },
        computed:{
            dadosFiltrados(){
                let campos = Object.keys(this.titulos)
                let dadosFiltrados = []
                //console.log('ttttt= '+campos)
                // console.log(this.dados)
                // console.log(this.titulos)
                 this.dados.map((item,chave) =>{
                    // console.log(chave,item)
                     let itemFiltrado= {}
                     campos.forEach(campos => {
                        //console.log(campos)
                         itemFiltrado[campos] = item[campos]
                         //console.log(chave,item,campos)
                     })
                     dadosFiltrados.push(itemFiltrado)
                 }) 
                 //console.log('dados ',dadosFiltrados[0].imagem)
                return dadosFiltrados
            }
        }
    }
</script>
