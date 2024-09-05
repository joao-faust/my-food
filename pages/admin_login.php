<?php

use App\Lib\DbConnection;

$error = filter_input(INPUT_GET, 'error', FILTER_SANITIZE_SPECIAL_CHARS);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $password = filter_input(INPUT_POST, 'password');

  $conn = DbConnection::getConn();
  $smtp = $conn->prepare('SELECT senha FROM config LIMIT 1 ');
  $smtp->execute();
  $passwordHash = $smtp->fetch(PDO::FETCH_ASSOC)["senha"];

  $validPassword = password_verify($password, $passwordHash);

  if ($validPassword) {
    $_SESSION["loggedIn"] = true;
    header("Location: /?page=admin_home");
  } else {
    header("Location: /?page=admin_login&error=true");
  }
}

?>

<div class="fullscreen d-flex justify-content-center align-items-center">
  <h1 class="main-heading">My Food</h1>
  <div class="card w-50">
    <h2>LOGIN</h2>
    <form method="post" action="/?page=admin_login">
      <div class="my-3">
        <label for="password">Senha de Administrador</label>
        <input type="password" name="password" id="password" placeholder="Digite a senha de administrador">
        <?php if ($error): ?>
          <p class="text-danger">Senha Incorreta</p>
        <?php endif; ?>
      </div>
      <button type="submit" class="w-100">Entrar</button>
    </form>
  </div>
</div>