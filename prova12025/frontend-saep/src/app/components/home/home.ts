import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';

@Component({
  selector: 'app-home',
  imports: [],
  templateUrl: './home.html',
  styleUrl: './home.css',
})
export class Home implements OnInit{
  nomeUsuario: string = "";

  constructor(private router: Router){}

  ngOnInit(){
    //pegar as informações do localStorage
    // || Operador OU, ?? verifica nulidade, a diferença é que o 
    // || se caso for algum valor null, ) ou "" ele preenche com o valor apos o operador
    // ?? 
    const user = JSON.parse(localStorage.getItem("usuarioLogado") ?? "{}");
    this.nomeUsuario = user.nome ?? "Visitante";
  }

  logout(){
    localStorage.removeItem("usuarioLogado");
    this.router.navigate(["/login"]);
  }
}
