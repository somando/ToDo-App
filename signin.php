<?php

  session_start();
  
  $pdo = new PDO('mysql:host=db;dbname=todo', 'root', 'root');

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_digest'])) {
      $_SESSION['user_id'] = $user['id'];
      header('Location: /');
      exit();
    } else {
      echo 'ログインできませんでした';
    }
  }
?>

<form method="post">
  <label for="username">ユーザーID:</label>
  <input type="text" name="id" id="input-text" required>
  <br>
  <label for="password">パスワード:</label>
  <input type="password" name="password" id="input-text" required>
  <br>
  <a href="signup.php">アカウントがありませんか？</a>
  <br>
  <button type="submit">ログイン</button>
</form>
