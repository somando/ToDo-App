<?php

  $pdo = new PDO('mysql:host=db;dbname=todo', 'root', 'root');

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $password = $_POST['password'];
    $password_confirmation = $_POST['password_confirmation'];

    if ($password !== $password_confirmation) {
      echo '<h1 class="app-title"><a href="/">ToDo List</a></h1>';
      echo '<header class="alert-bar">パスワードが一致しません。</header>';
    } else {

      $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE id = :id');
      $stmt->execute(['id' => $id]);
      if ($stmt->fetchColumn() > 0) {
        echo '<h1 class="app-title"><a href="/">ToDo List</a></h1>';
        echo '<header class="alert-bar">ユーザーIDが既に使われています。別のユーザーIDを入力してください。</header>';
      } else {

        $stmt = $pdo->prepare('INSERT INTO users (id, password_digest) VALUES (:id, :password_digest)');
        $success = $stmt->execute(['id' => $id, 'password_digest' => password_hash($password, PASSWORD_DEFAULT)]);

        if ($success) {
          session_start();
          $_SESSION['user_id'] = $id;
          header('Location: /');
        } else {
          echo '<h1 class="app-title"><a href="/">ToDo List</a></h1>';
          echo '<header class="alert-bar">アカウントを作成できませんでした</header>';
        }
      }
    }
  } else {
    echo '<h1 class="app-title"><a href="/">ToDo List</a></h1>';
    if (!isset($_SESSION['user_id'])) {
      echo '<header class="alert-bar">ToDo Listを利用するにはログインが必要です。<a href="/signin.php">ログイン</a> <a href="/signup.php">新規登録</a></header>';
    } else {
      echo '<header class="welcome">ようこそ、'. $_SESSION['user_id'] . 'さん。 <a href="/signout.php">ログアウト</a></header>';
      $pdo = new PDO('mysql:host=db;dbname=todo', 'root', 'root');
    }
  }
?>

<meta>
  <title>新規登録｜ToDo List</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/style.css">
</meta>

<main>
  <h1>Sign Up</h1>
  <form method="post" class="account-form">
    <label for="username" class="form-label-user">ユーザーID:</label>
    <input type="text" name="id" id="input-text" required>
    <br>
    <label for="password" class="form-label-password">パスワード:</label>
    <input type="password" name="password" id="input-text" required>
    <br>
    <label for="password" class="form-label-password">パスワード確認用:</label>
    <input type="password" name="password_confirmation" id="input-text" required>
    <br>
    <div class="center">
      <p class="have-account"><a href="signin.php">アカウントを既に持っていますか？</a></p>
      <br>
      <button type="submit">アカウント作成</button>
    </div>
  </form>
</main>
