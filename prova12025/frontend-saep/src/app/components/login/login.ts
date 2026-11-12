import { Component } from '@angular/core';
import { Api } from '../../services/api';
import { Router } from '@angular/router';


@Component({
  selector: 'app-login',
  imports: [],
  templateUrl: './login.html',
  styleUrl: './login.css',
})
export class Login {
  credenciais = {login: "", senha :""};

  constructor(private api: Api, private router: Router){}

  entrar(){
    this.api.login(this.credenciais).subscribe({
      next: ()
    });
  }

}
