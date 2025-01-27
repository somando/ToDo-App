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
    } else {
      echo '<h1 class="app-title"><a href="/">ToDo List</a></h1>';
      echo '<header class="alert-bar">ログインできませんでした</header>';
    }
  } else {
    echo '<h1 class="app-title"><a href="/">ToDo List</a></h1>';
    if (!isset($_SESSION['user_id'])) {
      echo '<header class="alert-bar">ToDo Listを利用するにはログインが必要です。<a href="/signin.php">ログイン</a> <a href="/signup.php">新規登録</a></header>';
    } else {
      echo '<header class="welcome">ようこそ、'. $_SESSION['user_id'] . 'さん。 <a href="/signout.php">ログアウト</a></header>';
    }
  }

?>

<meta>
  <title>ログイン｜ToDo List</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/style.css">
</meta>

<main>
  <h1>Sign In</h1>
  <form method="post" class="account-form">
    <label for="username" class="form-label-user">ユーザーID:</label>
    <input type="text" name="id" id="input-text" required>
    <br>
    <label for="password" class="form-label-password">パスワード:</label>
    <input type="password" name="password" id="input-text" required>
    <br>
    <div class="center">
      <p class="do-not-have-account"><a href="/signup.php">アカウントがありませんか？</a></p>
      <br>
      <button type="submit">ログイン</button>
    </div>
  </form>
</main>
