import { Component } from '@angular/core';
import { Api } from '../../service/api';
import { Router } from "@angular/router";

@Component({
  selector: 'app-login',
  imports: [],
  templateUrl: './login.html',
  styleUrl: './login.css',
})
export class Login {
  credenciais = {login: "", senha: ""};

  constructor(private api: Api, private router: Router){};

  //entrar
  entrar(){
    this.api.login(this.credenciais).subscribe({
      next: (usuario) => {
        //armazenamento no cache do navegador
        localStorage.setItem("usuarioLogado", JSON.stringify(usuario)); //converte Json em Texto e Armazena no LocalStorage
        this.router.navigate(["/home"]); // redirecionar o site para a página home do usuário
      },
      error: ()=>{
        //mensagem na Tela do usuário
        alert("Falha de Autenticação. Usuário ou Senha Incorretos.")
      }
    });
  }

}
