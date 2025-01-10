<?php

  $pdo = new PDO('mysql:host=db;dbname=todo', 'root', 'root');

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $password = $_POST['password'];
    $password_confirmation = $_POST['password_confirmation'];

    if ($password !== $password_confirmation) {
      echo 'パスワードが一致しません';
      exit();
    }

    $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE id = :id');
    $stmt->execute(['id' => $id]);
    if ($stmt->fetchColumn() > 0) {
      echo 'ユーザーIDが既に使われています。別のユーザーIDを入力してください。';
    } else {

      $stmt = $pdo->prepare('INSERT INTO users (id, password_digest) VALUES (:id, :password_digest)');
      $success = $stmt->execute(['id' => $id, 'password_digest' => password_hash($password, PASSWORD_DEFAULT)]);

      if ($success) {
        session_start();
        $_SESSION['user_id'] = $id;
        echo 'アカウントを作成しました';
        header('Location: /');
        exit();
      } else {
        echo 'アカウントを作成できませんでした';
      }
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
  <label for="password">パスワード確認用:</label>
  <input type="password" name="password_confirmation" id="input-text" required>
  <br>
  <a href="signin.php">アカウントを既に持っていますか？</a>
  <br>
  <button type="submit">アカウント作成</button>
</form>
