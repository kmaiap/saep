import { Component, OnInit } from '@angular/core';
import { Api } from '../../services/api';

@Component({
  selector: 'app-produto',
  imports: [],
  templateUrl: './produto.html',
  styleUrl: './produto.css',
})
export class Produto implements OnInit{
  //vetor para armazenar produtos
  produtos: any[] = [];
  termoBusca: string = "";
  produtoAtual = {nome: "", descricao: "", estoqueMinimo: null};

  constructor(private api:Api){}

  //Inicialização da Página
  ngOnInit(){this.carregar();}

  //carregar
  carregar(){
    //preenche o vetor com a resposta
    this.api.getProdutos().subscribe(res => this.produtos = res);
  }

  buscar(){ //SSR => renderização ao lado do servidor
    // para cada letra que eu digitar => vai no servidor e atualiza  lista de produtos
    this.api.buscarProdutos(this.termoBusca).subscribe(res => this.produtos = res);
  }

  //CSR
  buscar2(){
    let produtosFiltrados:any[] = []
    produtosFiltrados = this.produtos.filter((e)=>e.contains(this.termoBusca));
    this.produtos = produtosFiltrados;
  }





}
