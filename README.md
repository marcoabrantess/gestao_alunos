# Gestao Alunos

Um sistema simples de gestao de alunos, feito em PHP e JavaScript, onde se adiciona, deleta e pesquisa no banco de dados MySQL  
Para rodar o sistema, abra o mysql workbench ou abra o mysql no terminal, execute os comandos:  
1. CREATE DATABASE alunos;  
2. CREATE TABLE alunos(  
	id int auto_increment primary key,  
	nome varchar(255),  
	email varchar(255),  
	telefone varchar(100),  
	endereco varchar(255)  
);  
3. Create a local MySQL account and grant it access to `alunos`. Choose the account name and password yourself. Store them only in the environment or in an uncommitted `.env` file.  
   CREATE USER '<DB_USER>'@'localhost' IDENTIFIED BY '<DB_PASSWORD>';  
   GRANT ALL ON alunos.* TO '<DB_USER>'@'localhost';  
   FLUSH PRIVILEGES;  
4. Copy `.env.example` to `.env` and set `DB_HOST`, `DB_USER`, `DB_PASSWORD`, and `DB_NAME`. `processa.php` reads those variables from the environment. A local `.env` file fills in a name only when the environment has not already set it. Do not commit `.env`. Configure the web server so it does not serve that file.  
