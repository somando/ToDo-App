<?php
  session_start();
  if (!isset($_SESSION['user_id'])) {
    echo 'ToDo Listを利用するにはログインが必要です。<a href="signin.php">ログイン</a> <a href="signup.php">新規登録</a>';
  } else {
    echo 'ようこそ！'. $_SESSION['user_id'] . 'さん。 <a href="signout.php">ログアウト</a>';
    $pdo = new PDO('mysql:host=db;dbname=todo', 'root', 'root');
  }
?>

<h1>ToDo List</h1>

<?php

  if (isset($pdo)) {

    

  } else {

    echo 'ToDo Listを利用するにはログインが必要です。<a href="signin.php">ログイン</a> <a href="signup.php">新規登録</a>';

  }
