<?php

  session_start();
  
  $pdo = new PDO('mysql:host=db;dbname=todo', 'root', 'root');

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $user_id = $_POST['user_id'];

    if ($user_id === '') {
      echo '<h1 class="app-title"><a href="/">ToDo List</a></h1>';
      echo '<header class="alert-bar">必須事項が入力されていません。</header>';
    } else {
      $stmt = $pdo->prepare('SELECT * FROM folders_users WHERE folder_id = :folder_id AND user_id = :user_id');
      $stmt->execute(['folder_id' => $id, 'user_id' => $_SESSION['user_id']]);
      if (!$stmt->fetch()) {
        echo '<h1 class="app-title"><a href="/">ToDo List</a></h1>';
        echo '<header class="alert-bar">フォルダが見つかりません。</header>';
      } else {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute(['id' => $user_id]);
        if (!$stmt->fetch()) {
          echo '<h1 class="app-title"><a href="/">ToDo List</a></h1>';
          echo '<header class="alert-bar">ユーザーが見つかりません。</header>';
        } else {
          $stmt = $pdo->prepare('SELECT * FROM folders_users WHERE folder_id = :folder_id AND user_id = :user_id');
          $stmt->execute(['folder_id' => $id, 'user_id' => $user_id]);
          if ($stmt->fetch()) {
            echo '<h1 class="app-title"><a href="/">ToDo List</a></h1>';
            echo '<header class="alert-bar">既にメンバーに追加されています。</header>';
          } else {
            $stmt = $pdo->prepare('INSERT INTO folders_users (folder_id, user_id) VALUES (:folder_id, :user_id)');
            $stmt->execute(['folder_id' => $id, 'user_id' => $user_id]);
            $stmt->fetch();
            header('Location: /folders/members.php?id=' . $id);
          }
        }
      }
    }
  } else {
    echo '<h1 class="app-title"><a href="/">ToDo List</a></h1>';
    if (!isset($_SESSION['user_id'])) {
      echo '<header class="alert-bar">ToDo Listを利用するにはログインが必要です。<a href="/signin.php">ログイン</a> <a href="/signup.php">新規登録</a></header>';
    } else {
      echo '<header class="welcome">ようこそ、'. $_SESSION['user_id'] . 'さん。 <a href="/signout.php">ログアウト</a></header>';
    }
    $stmt = $pdo->prepare('SELECT * FROM folders_users WHERE user_id = :user_id AND folder_id = :folder_id');
    $stmt->execute(['user_id' => $_SESSION['user_id'], 'folder_id' => $_GET['id']]);
    if (!($stmt->fetch())) {
      echo '<header class="alert-bar">フォルダが見つかりません。</header>';
    }
    $stmt = $pdo->prepare('SELECT * FROM folders WHERE id = :id');
    $stmt->execute(['id' => $_GET['id']]);
    $folder = $stmt->fetch();
  }
?>

<meta>
  <title>フォルダメンバー｜ToDo List</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/style.css">
</meta>

<main>
  <h1>Members</h1>
  <div class="folder-container">
    <div class="content-block">
    <?php
      echo '      <h3 class="folder-title">' . $folder['name'] . '</h3>';
    ?>
    <div class="folder-task">
      <h4 class="folder-sub-title form-label-users">現在のメンバー</h4>
      <?php
        $count = 0;
        $stmt = $pdo->prepare('SELECT * FROM folders_users WHERE folder_id = :folder_id');
        $stmt->execute(['folder_id' => $_GET['id']]);
        $members = $stmt->fetchAll();
        if ($members) {
          echo '<div class="members-container">';
          foreach ($members as $member) {
            echo '  <p class="form-label-user">' . $member['user_id'] . '</p>';
            echo '  <form method="post" name="delete-member-form-' . $count . '" action="/folders/members/delete.php" class="delete-member-form">';
            echo '    <input type="hidden" name="id" value="' . $folder['id'] . '">';
            echo '    <input type="hidden" name="user_id" value="' . $member['user_id'] . '">';
            echo '    <a href="javascript:void(0)" class="delete-member-submit" onclick="submitForm(\'delete-member-form-' . $count . '\')"></a>';
            echo '  </form>';
            $count++;
          }
          echo '</div>';
        } else {
          echo '<p>メンバーはいません。</p>';
        }
      ?>
    </div>
    <div class="folder-task">
      <h4 class="folder-sub-title add-member">メンバーを追加</h4>
      <form method="post" class="add-member-form">
        <input type="hidden" name="id" value="<?php echo $folder['id']; ?>">
        <label for="user_id" class="form-label-user">ユーザーID:</label>
        <input type="text" name="user_id" id="input-text" required>
        <button type="submit" class="button center">追加</button>
      </form>
    </div>
</main>

<script>
  function submitForm(formName) {
    var form = document.forms[formName];
    if (form) {
      form.submit();
    } else {
      console.error('フォームが見つかりません: ' + formName);
    }
  }
</script>
