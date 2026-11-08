<?php

/**
 * Resolve one database setting.
 * Process environment wins. A local, uncommitted .env file supplies only missing keys.
 */
function read_database_setting(string $name): string
{
    $value = database_setting_from_environment($name);
    if ($value !== null) {
        return $value;
    }

    $fromFile = database_settings_from_local_file();
    if (isset($fromFile[$name]) && $fromFile[$name] !== '') {
        return $fromFile[$name];
    }

    http_response_code(500);
    die('Database configuration is incomplete. Set DB_HOST, DB_USER, DB_PASSWORD, and DB_NAME.');
}

function database_setting_from_environment(string $name): ?string
{
    $candidates = [];
    $fromGetenv = getenv($name);
    if (is_string($fromGetenv)) {
        $candidates[] = $fromGetenv;
    }
    if (isset($_SERVER[$name]) && is_string($_SERVER[$name])) {
        $candidates[] = $_SERVER[$name];
    }
    if (isset($_ENV[$name]) && is_string($_ENV[$name])) {
        $candidates[] = $_ENV[$name];
    }

    foreach ($candidates as $candidate) {
        if ($candidate !== '') {
            return $candidate;
        }
    }

    return null;
}

/**
 * @return array<string, string>
 */
function database_settings_from_local_file(): array
{
    static $settings = null;
    if (is_array($settings)) {
        return $settings;
    }

    $settings = [];
    $path = __DIR__ . '/.env';
    if (!is_readable($path)) {
        return $settings;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES);
    if ($lines === false) {
        return $settings;
    }

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strncmp($line, '#', 1) === 0) {
            continue;
        }
        if (strncmp($line, 'export ', 7) === 0) {
            $line = trim(substr($line, 7));
        }

        $separator = strpos($line, '=');
        if ($separator === false) {
            continue;
        }

        $key = trim(substr($line, 0, $separator));
        $rawValue = trim(substr($line, $separator + 1));
        if ($key === '' || preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $key) !== 1) {
            continue;
        }

        $length = strlen($rawValue);
        if ($length >= 2) {
            $quote = $rawValue[0];
            if (($quote === '"' || $quote === "'") && $rawValue[$length - 1] === $quote) {
                $rawValue = substr($rawValue, 1, -1);
            }
        }

        $settings[$key] = $rawValue;
    }

    return $settings;
}

//banco de dados
$server = read_database_setting('DB_HOST');
$user = read_database_setting('DB_USER');
$password = read_database_setting('DB_PASSWORD');
$base = read_database_setting('DB_NAME');
try {
    $connection = new mysqli($server, $user, $password, $base);
} catch (mysqli_sql_exception) {
    die('Falha na conexão');
}
if ($connection->connect_error) {
    die('Falha na conexão');
}
//busca
//verifica se o metodo GET foi realizado
if(isset($_GET['buscanome'])){
   
    $nome = $_GET['buscanome'];
    echo $nome;
    if(empty($nome)){
        $query = "SELECT * FROM alunos";
    }
    else{
        $query = "SELECT * FROM alunos WHERE nome LIKE '%$nome%'";
    }
    //executa a sql
    $sql = $connection->query($query);
    //verifica o numero de linhas do resultado
    $contagem = $sql->num_rows;
    sleep(1);
    //so exibe resultado se aparecer alguma linha no sql
    if($contagem > 0){
       //exibe os resultados
       echo "<table class='table table-hover table-striped'>
                  <thead>
                      <tr>
                          <th>Id</th>
                          <th>Nome</th>
                          <th>Telefone</th>
                          <th>Email</Th>
                          <th>Endereco</th>
                          <th>Deletar</th>
                      <tr>
                  </thead>
                  <tbody>";
       while($linha = $sql->fetch_array()){
          echo '<tr>';
          echo '<td>'.$linha['id'].'</td>';
          echo '<td>'.$linha['nome'].'</td>';
          echo '<td>'.$linha['email'].'</td>';
          echo '<td>'.$linha['telefone'].'</td>';
          echo '<td>'.$linha['endereco'].'</td>';
          echo '<td><a onclick="deletaUsuario('.$linha['id'].');" class="btn btn-danger">Deletar</a></td>';
          echo '</tr>';
       }
    }
    else{
        echo "<span class='alert alert-danger' role='alert'>Nenhum registro encontrado!</span>";
    }
}
elseif(!empty($_GET['nome']) and
    !empty($_GET['endereco']) and
    !empty($_GET['telefone']) and
    !empty($_GET['email'])
){
    
    //define o valor das variaveis
    $nome = $_GET['nome'];
	$telefone = $_GET['telefone'];
	$endereco = $_GET['endereco'];
	$email = $_GET['email'];

    //insere no banco de dados
	$query = "INSERT INTO alunos(nome, telefone, endereco, email) VALUES('$nome', '$telefone', '$endereco', '$email')";
    
    sleep(1);
    
    //executa a sql
	$sql = $connection->query($query);
    
    echo "<span class='alert alert-success' role='alert'>Inserido com sucesso</span>";
}
elseif(isset($_GET['deleta'])){
    $id = $_GET['deleta'];

    $query = "DELETE FROM alunos WHERE id = '$id'";
    
    sleep(1);
    
    //executa a sql
	$sql = $connection->query($query);
    
    echo "<span class='alert alert-success' role='alert'>Deletado com sucesso</span>";
}
else{
    echo "<span class='alert alert-danger' role='alert'>UM erro ocorreu. Por favor,
     preencha todos os campos </span>";
}
?>
