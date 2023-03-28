<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <card-component titulo='Busca de marcas'>
                    <template v-slot:conteudo>
                        <div class="card-body">
                        <div class="form-row">
                            <div class="col mb-3">
                                <inputcontainer-component titulo="ID" id="inputId" id-help="idHelp" texto-ajuda="Opcional. Informe o ID do registro">
                                    <input type="number" class="form-control" id="inputId" v-model="busca.id" aria-describedby="idHelp" placeholder="ID">
                                </inputcontainer-component>
                            </div>
                            <div class="col mb-3">
                                <inputcontainer-component titulo="Nome da Marca" id="inputNome" id-help="nomeHelp" texto-ajuda="Opcional. Informe o nome do registro">
                                    <input type="text" class="form-control" id="inputNome" aria-describedby="nomeHelp" v-model="busca.nome" placeholder="Nome da marca">
                                </inputcontainer-component>
                            </div>
                        </div>
                    </div>
                    </template>
                    <template v-slot:rodape>
                        <button type="submit" class="btn btn-primary btn-sm float-right" @click="pesquisar()">Pesquisar</button>
                    </template>
                </card-component>
                
                <card-component titulo="Relação de marcas">
                    <template v-slot:conteudo>
                        <table-component :dados="marcas.data" 
                                         :titulos="{
                                                    id: {titulo:'ID',tipo: 'texto'},
                                                    nome: {titulo:'Nome',tipo: 'texto'},
                                                    imagem: {titulo:'Imagem',tipo: 'imagem'},
                                                    created_at:{titulo:'Data de Criação',tipo: 'data'}
                                          }"
                            :visualizar="{visivel: true,dataToggle: 'modal',dataTarget: '#modalVizualizarMarca'}" :atualizar="true" :remover="true">
                        </table-component>
                    </template>
                    <template v-slot:rodape>
                        <div class="row">
                            <div class="col-10">
                            ,    <paginate-component>
                                    <li :class="l.active ?'page-item active': 'page-item'" v-for="l,key in marcas.links" :key="key" @click="paginacao(l)"><a class="page-link" v-html="l.label"></a></li>
                                </paginate-component>
                            </div>
                            <div class="col">
                                <button type="button" class="btn btn-primary btn-sm float-right" data-toggle="modal" data-target="#modalMarca">Adicionar</button>
                            </div>
                        </div>
                    </template>
                </card-component>
                
            </div>
        </div>
        <modal-component id="modalMarca" titulo="Adicionar Marca">
            <template v-slot:alertas>
                <alert-component tipo="success" :detalhes="transacaoDetalhes" titulo="Cadastro realizado com sucesso" v-if="transacaoStatus == 'Adicionado'"></alert-component>
                <alert-component tipo="danger" :detalhes="transacaoDetalhes" titulo="Erro ao adicionar" v-if="transacaoStatus == 'Erro'"></alert-component>
            </template>
            <template v-slot:conteudo>
                <div class="form-group">
                    <inputcontainer-component titulo="Novo nome da Marca" id="novoNome" id-help="novoNomeHelp" texto-ajuda="Informe o nome do registro">
                        <input type="text" class="form-control" id="novoNome" aria-describedby="novoNomeHelp" placeholder="Nome da marca" v-model="nomeMarca">
                    </inputcontainer-component>
                    {{ nomeMarca }}
                </div>
                <div class="form-group">
                    <inputcontainer-component titulo="Imagem" id="novoImagem" id-help="novoImagemHelp" texto-ajuda="Selecione uma imagem PNG">
                        <input type="file" class="form-control-file" id="novoImagem" aria-describedby="novoImagemHelp" placeholder="selecione uma imagem" @change="carregarImagem($event)">
                    </inputcontainer-component>
                    {{ arquivoImagem }}
                </div>
            </template>
            <template v-slot:rodape>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">fechar</button>
                <button type="button" class="btn btn-primary" @click="salvar()">Salvar</button>
            </template>
        </modal-component>
        <modal-component id="modalVizualizarMarca" titulo="Visualizar Marca">
            <template v-slot:alertas></template>
            <template v-slot:conteudo>
                <inputcontainer-component titulo="ID" >
                        <input type="text" class="form-control" :value="$store.state.item.id" disabled>
                </inputcontainer-component>
                <inputcontainer-component titulo="Nome" >
                        <input type="text" class="form-control" :value="$store.state.item.nome" disabled>
                </inputcontainer-component>
                <inputcontainer-component titulo="Imagem" >
                        <img :src="'storage/'+$store.state.item.imagem" v-if="$store.state.item.imagem">
                </inputcontainer-component>
                <inputcontainer-component titulo="Data de Criação" >
                        <input type="text" class="form-control" :value="$store.state.item.created_at" disabled>
                </inputcontainer-component>
            </template>
            <template v-slot:rodape>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">fechar</button>
            </template>
        </modal-component>
        <modal-component id="modalRemoverMarca" titulo="Remoer Marca">
            <template v-slot:alertas>
                <alert-component tipo="success" titulo="Maca removido com sucesso" detalhes="$store.state.transacao" v-if="$store.state.transacao.status == 'sucesso'"></alert-component>
                <alert-component tipo="danger" titulo="Erro ao remover" detalhes="$store.state.transacao" v-if="$store.state.transacao.status == 'erro'"></alert-component>
            </template>
            <template v-slot:conteudo v-if="$store.state.transacao.status != 'sucesso'">
                <inputcontainer-component titulo="ID" >
                        <input type="text" class="form-control" :value="$store.state.item.id" disabled>
                </inputcontainer-component>
                <inputcontainer-component titulo="Nome" >
                        <input type="text" class="form-control" :value="$store.state.item.nome" disabled>
                </inputcontainer-component>
            </template>
            <template v-slot:rodape v-if="$store.state.transacao.status != 'sucesso'">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">fechar</button>
                <button type="button" class="btn btn-danger" @click="remover()" >remover</button>
            </template>
        </modal-component>
        <modal-component id="modalAtualizarMarca" titulo="Atualizar Marca">
            <template v-slot:alertas>
                <alert-component tipo="success" titulo="Marca com sucesso" detalhes="$store.state.transacao" v-if="$store.state.transacao.status == 'sucesso'"></alert-component>
                <alert-component tipo="danger" titulo="Erro ao remover" detalhes="$store.state.transacao" v-if="$store.state.transacao.status == 'erro'"></alert-component>
            </template>
            <template v-slot:conteudo>
                <div class="form-group">
                    <inputcontainer-component titulo="Novo nome da Marca" id="atualizarNome" id-help="atualizarNomeHelp" texto-ajuda="Informe o nome do registro">
                        <input type="text" class="form-control" id="atualizarNome" aria-describedby="atualizarNomeHelp" placeholder="Nome da marca" v-model="$store.state.item.nome">
                    </inputcontainer-component>
                </div>
                <div class="form-group">
                    <inputcontainer-component titulo="Imagem" id="atualizarImagem" id-help="atualizarImagemHelp" texto-ajuda="Selecione uma imagem PNG">
                        <input type="file" class="form-control-file" id="atualizarImagem" aria-describedby="atualizarImagemHelp" placeholder="selecione uma imagem" @change="carregarImagem($event)">
                    </inputcontainer-component>
                </div>
            </template>
            <template v-slot:rodape>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">fechar</button>
                <button type="button" class="btn btn-primary" @click="atualizar()">Atualizar</button>
            </template>
        </modal-component>
    </div>
</template>
<script>
//import { config } from 'vue/types/umd'
export default{
        data(){
            return {
                nomeMarca:'',
                transacaoStatus: '',
                transacaoDetalhes : {},
                urlBase:'http://localhost:8000/api/v1/marca',
                urlPaginacao : '',
                urlFiltro: '',
                arquivoImagem: [],
                busca:{id :'',nome:''},
                marcas: { data: [] }
            }
        },
        methods:{
            atualizar(){
                let formData = new FormData()
                formData.append('_method','patch')
                formData.append('nome',this.$store.state.item.nome)

                if(this.arquivoImagem[0]){
                    formData.append('imagem',this.arquivoImagem[0])
                }

                let url = this.urlBase + '/' + this.$store.state.item.id

                let config = {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        // 'Authorization': this.token,
                        // 'Accept':'application/json'
                    }
                }

                axios.post(url,formData,config)
                    .then(response => {
                        console.log(response)
                        atualizarImagem.value = ''
                        this.$store.state.transacao.status = 'sucesso'
                        this.$store.state.transacao.mensagem = 'Regitro de marca atualizado com sucesso'
                        this.carregarLista()
                    })
                    .catch(errors => {
                        console.log(errors.response);
                        this.$store.state.transacao.status = 'erro'
                        this.$store.state.transacao.mensagem = errors.response.data.message
                        this.$store.state.transacao.dados = errors.response.data.errors
                    })
            },
            remover(){
                let confirmacao = confirm('deseja remover a marca ?')

                if (!confirmacao) {
                    return false;
                }

                let formData = new FormData()
                formData.append('_method','delete')
                console.log(formData);

                // let config = {
                //     headers:{
                //         'Accept':'application/json',
                //         'Authorization':this.token
                //     }
                // }

                let url = this.urlBase + '/' + this.$store.state.item.id
                //console.log(url);
                  axios.post(url,formData)
                  .then(response => {
                      console.log('removida',response)
                      this.carregarLista()
                      this.$store.state.transacao.status = 'sucesso'
                      this.$store.state.transacao.mensagem = response.data.msg
                  })
                  .catch(errors =>{
                      console.log(errors.response)
                      this.$store.state.transacao.status = 'erro'
                      this.$store.state.transacao.mensagem = errors.response.data.erro
                  })
            },
            pesquisar(){
                //console.log(this.busca)
                let filtro = ''
                for(let chave in this.busca) {
                    //console.log(chave,this.busca[chave])
                    if(this.busca[chave]){
                        if (filtro != '') {
                            filtro += ";"
                        }
                        filtro += chave + ':like:' + this.busca[chave]
                    }
                    
                }
                if(filtro != ''){
                    this.urlPaginacao = 'page=1'
                    this.urlFiltro = '&filtro='+filtro
                }else{
                    this.urlFiltro = ''
                }
                this.carregarLista()
                

            },
            paginacao(l){
                console.log(l)
                if (l.url) {
                    //this.urlBase = l.url
                    this.urlPaginacao = l.url.split('?')[1]
                    //console.log(this.urlPaginacao)
                    this.carregarLista()
                }
                
            },
            carregarLista(){
                let url = this.urlBase + '?' + this.urlPaginacao + this.urlFiltro
                console.log(url)
                // let config = {
                //         headers: {
                //             'Authorization': this.token,
                //             'Accept':'application/json'
                //         }
                //     }
                axios.get(url)
                    .then(response => {
                        this.marcas= response.data
                        //console.log('Sucesso : ',this.marcas.data[0].nome)
                    })
                    .catch(error => {
                        //console.log('Erro :',error.response)
                    })
            },
            carregarImagem(e){
                this.arquivoImagem = e.target.files
                console.log('imagem',this.arquivoImagem)
            },
            salvar(){
              console.log(this.nomeMarca,this.arquivoImagem[0])
              let formData = new FormData();
              formData.append('nome',this.nomeMarca)
              formData.append('imagem',this.arquivoImagem[0])

              let config = {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    // 'Authorization': this.token,
                    // 'Accept':'application/json'
                }
              }

              axios.post(this.urlBase,formData,config)
              .then(response =>{
                this.transacaoStatus = 'Adicionado'
                this.transacaoDetalhes = {
                    mensagem : 'ID do registro= '+ response.data.id
                }
                console.log(response)
              })
              .catch(errors => {
                this.transacaoStatus = 'Erro'
                this.transacaoDetalhes = {
                    mensagem: errors.response.data.message,
                    dados: errors.response.data.errors
                }
                console.log(errors.response.data.message)
              })
            }
        },
        mounted(){
            this.carregarLista()
        }
    }
</script>
