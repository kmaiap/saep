# Sistema de Gestão para Clínica Veterinária (SAEP)

Sistema web desenvolvido para o gerenciamento de tutores, pets e agendamentos de uma clínica veterinária, contemplando autenticação de usuários com expiração de sessão, proteção de dados sensíveis conforme a LGPD e persistência em banco de dados relacional.

---

## Tecnologias Utilizadas

* **Linguagem de Programação:** Python 3.x
* **Framework Web:** Flask
* **Banco de Dados (SGBD):** MySQL 8.0+
* **Front-end:** HTML5 e CSS3 

---

## Estrutura do Projeto


├── documentacao.pdf          # Documentação técnica (Anexo 1)
├── der.png                   # Diagrama Entidade Relacionamento
├── saep_db.sql               # Script de criação e população do banco de dados
└── sistema/                  # Código-fonte da aplicação
    ├── README.md             # Instruções de instalação e execução
    ├── app.py                # Aplicação principal (rotas, regras de negócio e conexão)
    └── templates/            # Interfaces HTML
        ├── login.html
        ├── principal.html
        ├── tutores.html
        ├── editar_tutor.html
        ├── pets.html
        └── agendamentos.html