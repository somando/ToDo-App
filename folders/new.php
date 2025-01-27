<?php

  session_start();
  
  $pdo = new PDO('mysql:host=db;dbname=todo', 'root', 'root');

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $owner_id = $_SESSION['user_id'];

    if ($name === '') {
      echo '<h1 class="app-title"><a href="/">ToDo List</a></h1>';
      echo '<header class="alert-bar">フォルダ名が入力されていません。</header>';
    } else {
      $stmt = $pdo->prepare('INSERT INTO folders (name, description, owner_id) VALUES (:name, :description, :owner_id)');
      $stmt->execute(['name' => $name, 'description' => $description, 'owner_id' => $owner_id]);
      $folder = $stmt->fetch();

      $folder_id = $pdo->lastInsertId();

      $stmt = $pdo->prepare('INSERT INTO folders_users (folder_id, user_id) VALUES (:folder_id, :user_id)');
      $stmt->execute(['folder_id' => $folder_id, 'user_id' => $owner_id]);
      $folder_user = $stmt->fetch();

      header('Location: /');
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
  <title>フォルダ作成｜ToDo List</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/style.css">
</meta>

<main>
  <h1>New Folder</h1>
  <form method="post" class="account-form">
    <label for="name" class="form-label-folder">フォルダ名:</label>
    <input type="text" name="name" id="input-text" required>
    <br>
    <label for="description" class="form-label-description">説明:</label>
    <textarea name="description" id="input-text"></textarea>
    <br>
    <div class="center">
      <button type="submit">作成</button>
    </div>
  </form>
</main>
