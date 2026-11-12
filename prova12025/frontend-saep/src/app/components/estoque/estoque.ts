import { Component } from '@angular/core';
import { Api } from '../../services/api';

@Component({
  selector: 'app-estoque',
  imports: [],
  templateUrl: './estoque.html',
  styleUrl: './estoque.css',
})
export class Estoque {
  produtos: any[] = [];
  movimentacoes: any[] = [];
  novaMovimentacao = {produto: {id: null}, usuario: {id:null}, tipo: "", quantidade:null, dataMovimentacao: ""};

  constructor(private api: Api){}

  //Iniciar
  ngOnInit(){this.carregar();}

  //carregar
  carregar(){
    this.api.getMovimentacao().subscribe(res => this.movimentacoes = res);
    this.api.getProdutos().subscribe(res => this.produtos = res);
  }

  // ordenar as movimentações por produtos
  ordenarPorProduto(){
    this.movimentacoes.sort((a, b) => a.produto.nome.localeCompare(b.produto.nome));
    //localeCompare => compara String e ordena em ordem alfabética 
  }

  //Registrar movimentações
  registrarMovimentacao(){
    // validar se campos foram preenchido
    if(!this.novaMovimentacao.produto.id || !this.novaMovimentacao.usuario.id || !this.novaMovimentacao.tipo || !this.novaMovimentacao.quantidade){
      alert("Erro : Preencha todos os Campos");
      return;
    }
    // validar se a quantidade é maior que o solicitado
    const produtoSel = this.produtos.find(p => p.id === this.novaMovimentacao.produto.id);

    if(this.novaMovimentacao.tipo === "saida" && this.novaMovimentacao.quantidade > produtoSel.estoqueAtual){
      alert( "Erro: Quantidade de saída maior que a quantidade em estoque");
      return;
    }

    //Registro da Movimentação na API
    this.api.registrarMovimentacao(this.novaMovimentacao).subscribe(()=>{
      next: () => {
        alert(`Movimetação ${this.novaMovimentacao.tipo} registrada com sucesso`);
        this.carregar();
        this.novaMovimentacao = {produto: {id: null}, usuario: {id:null}, tipo: "", quantidade:null, dataMovimentacao: ""};
      }
      error: (e:any) =>{
        alert(`Erro: ${e.message}`);
        alert(`Erro: `+e.message);
      }
    })

  }
}
