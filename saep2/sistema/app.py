from flask import Flask, render_template, request, redirect, url_for, session, flash
import mysql.connector
from datetime import timedelta
import base64

app = Flask(__name__)
app.secret_key = 'chave_super_secreta_saep'
# Sessão com tempo de expiração (5 minutos)
app.permanent_session_lifetime = timedelta(minutes=5)

# Configuração do Banco de Dados (Ajuste password se o MySQL da prova tiver senha)
db_config = {
    'host': 'localhost',
    'user': 'root',
    'password': '',
    'database': 'saep_db'
}

def get_db_connection():
    return mysql.connector.connect(**db_config)

# Criptografia do CPF e Senha do tutor
def criptografar(texto):
    return base64.b64encode(texto.encode('utf-8')).decode('utf-8')

def descriptografar(texto_cripto):
    try:
        return base64.b64decode(texto_cripto.encode('utf-8')).decode('utf-8')
    except Exception:
        return texto_cripto

# --- LOGIN ---
@app.route('/', methods=['GET', 'POST'])
@app.route('/login', methods=['GET', 'POST'])
def login():
    if request.method == 'POST':
        login_user = request.form['login']
        senha_user = request.form['senha']
        
        conn = get_db_connection()
        cursor = conn.cursor(dictionary=True)
        cursor.execute("SELECT * FROM usuarios WHERE login = %s AND senha = %s", (login_user, senha_user))
        usuario = cursor.fetchone()
        cursor.close()
        conn.close()
        
        if usuario:
            session.permanent = True
            session['usuario_id'] = usuario['id_usuario']
            session['usuario_nome'] = usuario['nome']
            return redirect(url_for('principal'))
        else:
            flash('Erro: Usuário ou senha inválidos! Tente novamente.', 'danger')
            
    return render_template('login.html')

@app.route('/logout')
def logout():
    session.clear()
    return redirect(url_for('login'))

# --- PRINCIPAL ---
@app.route('/principal')
def principal():
    if 'usuario_id' not in session:
        flash('Sessão expirada ou usuário não autenticado. Faça login novamente.', 'warning')
        return redirect(url_for('login'))
    return render_template('principal.html', nome_usuario=session['usuario_nome'])

# --- TUTORES ---
@app.route('/tutores', methods=['GET', 'POST'])
def tutores():
    if 'usuario_id' not in session:
        return redirect(url_for('login'))
        
    conn = get_db_connection()
    cursor = conn.cursor(dictionary=True)
    
    # Cadastrar tutor
    if request.method == 'POST' and 'cadastrar' in request.form:
        nome = request.form['nome']
        cpf_cripto = criptografar(request.form['cpf'])
        telefone = request.form['telefone']
        email = request.form['email']
        senha_cripto = criptografar(request.form['senha'])
        
        cursor.execute(
            "INSERT INTO tutores (nome, cpf, telefone, email, senha) VALUES (%s, %s, %s, %s, %s)",
            (nome, cpf_cripto, telefone, email, senha_cripto)
        )
        conn.commit()
        flash('Tutor cadastrado com sucesso!', 'success')
        return redirect(url_for('tutores'))

    # Buscar tutor
    termo_busca = request.args.get('busca', '')
    if termo_busca:
        cursor.execute("SELECT * FROM tutores WHERE nome LIKE %s OR email LIKE %s", (f"%{termo_busca}%", f"%{termo_busca}%"))
    else:
        cursor.execute("SELECT * FROM tutores")
        
    lista_tutores = cursor.fetchall()
    for t in lista_tutores:
        t['cpf_visivel'] = descriptografar(t['cpf'])
        
    cursor.close()
    conn.close()
    return render_template('tutores.html', tutores=lista_tutores, termo_busca=termo_busca)

@app.route('/tutores/excluir/<int:id_tutor>')
def excluir_tutor(id_tutor):
    if 'usuario_id' not in session:
        return redirect(url_for('login'))
    conn = get_db_connection()
    cursor = conn.cursor()
    cursor.execute("DELETE FROM tutores WHERE id_tutor = %s", (id_tutor,))
    conn.commit()
    cursor.close()
    conn.close()
    flash('Tutor excluído com sucesso!', 'success')
    return redirect(url_for('tutores'))

@app.route('/tutores/editar/<int:id_tutor>', methods=['GET', 'POST'])
def editar_tutor(id_tutor):
    if 'usuario_id' not in session:
        return redirect(url_for('login'))
        
    conn = get_db_connection()
    cursor = conn.cursor(dictionary=True)
    
    if request.method == 'POST':
        nome = request.form['nome']
        cpf_cripto = criptografar(request.form['cpf'])
        telefone = request.form['telefone']
        email = request.form['email']
        
        if request.form['senha']:
            senha_cripto = criptografar(request.form['senha'])
            cursor.execute(
                "UPDATE tutores SET nome=%s, cpf=%s, telefone=%s, email=%s, senha=%s WHERE id_tutor=%s",
                (nome, cpf_cripto, telefone, email, senha_cripto, id_tutor)
            )
        else:
            cursor.execute(
                "UPDATE tutores SET nome=%s, cpf=%s, telefone=%s, email=%s WHERE id_tutor=%s",
                (nome, cpf_cripto, telefone, email, id_tutor)
            )
        conn.commit()
        cursor.close()
        conn.close()
        flash('Tutor atualizado com sucesso!', 'success')
        return redirect(url_for('tutores'))
        
    cursor.execute("SELECT * FROM tutores WHERE id_tutor = %s", (id_tutor,))
    tutor = cursor.fetchone()
    tutor['cpf_visivel'] = descriptografar(tutor['cpf'])
    cursor.close()
    conn.close()
    return render_template('editar_tutor.html', tutor=tutor)

# --- PETS ---
@app.route('/pets', methods=['GET', 'POST'])
def pets():
    if 'usuario_id' not in session:
        return redirect(url_for('login'))
        
    conn = get_db_connection()
    cursor = conn.cursor(dictionary=True)
    
    if request.method == 'POST':
        nome = request.form['nome']
        especie = request.form['especie']
        raca = request.form['raca']
        idade = request.form['idade']
        id_tutor = request.form['id_tutor']
        
        cursor.execute(
            "INSERT INTO pets (nome, especie, raca, idade, id_tutor) VALUES (%s, %s, %s, %s, %s)",
            (nome, especie, raca, idade, id_tutor)
        )
        conn.commit()
        flash('Pet cadastrado e associado ao tutor com sucesso!', 'success')
        return redirect(url_for('pets'))
        
    cursor.execute("SELECT id_tutor, nome FROM tutores")
    lista_tutores = cursor.fetchall()
    
    cursor.execute("""
        SELECT p.*, t.nome AS nome_tutor 
        FROM pets p 
        INNER JOIN tutores t ON p.id_tutor = t.id_tutor
    """)
    lista_pets = cursor.fetchall()
    
    cursor.close()
    conn.close()
    return render_template('pets.html', tutores=lista_tutores, pets=lista_pets)

# --- AGENDAMENTOS ---
@app.route('/agendamentos', methods=['GET', 'POST'])
def agendamentos():
    if 'usuario_id' not in session:
        return redirect(url_for('login'))
        
    conn = get_db_connection()
    cursor = conn.cursor(dictionary=True)
    
    # Atualizar data
    if request.method == 'POST' and 'atualizar_data' in request.form:
        id_agendamento = request.form['id_agendamento']
        nova_data = request.form['nova_data']
        cursor.execute(
            "UPDATE agendamentos SET data_agendamento = %s WHERE id_agendamento = %s",
            (nova_data, id_agendamento)
        )
        conn.commit()
        flash('Data do agendamento atualizada com sucesso!', 'success')
        return redirect(url_for('agendamentos'))

    # Novo agendamento
    if request.method == 'POST' and 'novo_agendamento' in request.form:
        data_ag = request.form['data_agendamento']
        horario = request.form['horario']
        procedimento = request.form['procedimento']
        id_pet = request.form['id_pet']
        
        cursor.execute("SELECT id_tutor FROM pets WHERE id_pet = %s", (id_pet,))
        pet_info = cursor.fetchone()
        
        cursor.execute(
            "INSERT INTO agendamentos (data_agendamento, horario, procedimento, id_tutor, id_pet) VALUES (%s, %s, %s, %s, %s)",
            (data_ag, horario, procedimento, pet_info['id_tutor'], id_pet)
        )
        conn.commit()
        flash('Agendamento criado com sucesso!', 'success')
        return redirect(url_for('agendamentos'))

    # Listar ordenado por data com todos os dados de tutor e pet
    cursor.execute("""
        SELECT 
            a.id_agendamento, a.data_agendamento, a.horario, a.procedimento,
            t.id_tutor, t.nome AS tutor_nome, t.cpf AS tutor_cpf, t.telefone AS tutor_telefone, t.email AS tutor_email,
            p.id_pet, p.nome AS pet_nome, p.especie AS pet_especie, p.raca AS pet_raca, p.idade AS pet_idade
        FROM agendamentos a
        INNER JOIN tutores t ON a.id_tutor = t.id_tutor
        INNER JOIN pets p ON a.id_pet = p.id_pet
        ORDER BY a.data_agendamento ASC, a.horario ASC
    """)
    lista_agendamentos = cursor.fetchall()
    for ag in lista_agendamentos:
        ag['tutor_cpf_visivel'] = descriptografar(ag['tutor_cpf'])
        
    cursor.execute("SELECT p.id_pet, p.nome AS pet_nome, t.nome AS tutor_nome FROM pets p INNER JOIN tutores t ON p.id_tutor = t.id_tutor")
    lista_pets = cursor.fetchall()
    
    cursor.close()
    conn.close()
    return render_template('agendamentos.html', agendamentos=lista_agendamentos, pets=lista_pets)

if __name__ == '__main__':
    app.run(debug=True)