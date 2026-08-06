import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root',
})
export class Api {
  private baseUrl = "http://localhost:8080/api";

  //toda vezx que chamar o construtor ele habilita do HTTPClient
  constructor(private http: HttpClient){}

  //métodos de Conexão com Api

  //login
  login(dados: any): Observable<any> { //Observable => conexão Assincrona com a API
    return this.http.post(`${this.baseUrl}/auth/login`, dados);
  }

  //getProdutos
  getProdutos():Observable<any> {return this.http.get(`${this.baseUrl}/produtos`);}

  //buscarProdutos(por partes do nome)
  buscarProdutos(termo:String): Observable<any>{return this.http.get(`${this.baseUrl}/produtos/busca?termo=${termo}`);}
  
  //postProduto
  postProduto(produto: any): Observable<any>{return this.http.post(`${this.baseUrl}/produtos`, produto);}
  
  //updateProduto
  updateProduto(id:number, produto: any): Observable<any>{return this.http.put(`${this.baseUrl}/produtos/${id}`, produto);}
  
  //deleteProduto
  deleteProduto(id:number): Observable<any>{return this.http.delete(`${this.baseUrl}/produtos/${id}`);}
  
  //postMovimentacao
  postMovimentacao(movimentacao: any): Observable<any>{return this.http.post(`${this.baseUrl}/movimentacao`, movimentacao);}
  
  //getMovimetacao
  getMovimentacao(): Observable<any>{return this.http.get(`${this.baseUrl}/movimentacao`);}

}
