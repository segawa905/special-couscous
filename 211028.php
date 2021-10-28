<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charaset="UTF-8">
  <title>掲示板</title>
</head>
<body>
  <h2>211028.php (mission5-1)</h2>
  <b>掲示板</b><br><br>
  　※編集時にはパスワードも変更されます。<br>
  　※パスワード欄が空だと動作しません。<br><br>

  <?php
    // DB接続設定
    $dsn = 'データベース名';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    createTable($pdo);

    /* 削除 */
    if(isset($_POST["delete_num"]) && isset($_POST["d_pass"])) {
      // データを取得
      $sql = 'SELECT * FROM rireki';
      $stmt = $pdo->query($sql);
      $results = $stmt->fetchAll();

      // テーブルを書き換える
      $sql = 'DROP TABLE rireki';
      $stmt = $pdo->query($sql);
      createTable($pdo);
      foreach($results as $row) {
        if($row['id'] != $_POST["delete_num"] || $row['pass'] != $_POST["d_pass"])
          addData($pdo, $row['name'], $row['comment'], $row['t'], $row['pass']);
      }
    }

    /* 編集(名前とコメントの取得まで) */
    if(isset($_POST["edit_num"]) && isset($_POST["e_pass"])) {
      $sql = 'SELECT * FROM rireki';
      $stmt = $pdo->query($sql);
      $results = $stmt->fetchAll();
      foreach($results as $row) {
        if($row['id'] == $_POST["edit_num"] && $row['pass'] == $_POST["e_pass"]) {
          $edit_num = $_POST["edit_num"];
          $edit_name = $row['name'];
          $edit_comment = $row['comment'];
          $success = 1;
        }
      }
      if(empty($success))
        echo "ERROR: 投稿番号が存在しないか、パスワードが間違っています。<br><br>";
    }

    /* 追加または差し替え */
    if(!empty($_POST["comment"]) && !empty($_POST["password"])) {
      if(empty($_POST["name"])) $name = "匿名";
      else $name = $_POST["name"]; // 名前
      $comment = $_POST["comment"]; // コメント
      $time = date("Y/m/d H:i:s"); // 日時
      $pass = $_POST["password"]; // パスワード

      /* 新規投稿: 編集番号が指定されなかった */
      if(empty($_POST["e_num"])) {
        // データレコードの挿入
        addData($pdo, $name, $comment, $time, $pass);
      }

      /* 編集: 編集番号が指定された */
      else {
          // データを取得
          $sql = 'SELECT * FROM rireki';
          $stmt = $pdo->query($sql);
          $results = $stmt->fetchAll();

          // テーブルを書き換える
          $sql = 'DROP TABLE rireki';
          $stmt = $pdo->query($sql);
          createTable($pdo);

          foreach($results as $row) {
            if($row['id'] == $_POST["e_num"]){
              $time = '編集済み: '.$time;
              addData($pdo, $name, $comment, $time, $pass);
            }
            else
              addData($pdo, $row['name'], $row['comment'], $row['t'], $row['pass']);
          }
      }
    }

    /* 履歴を出力 */
    $sql = 'SELECT * FROM rireki';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    echo "<hr>";
    if(count($results) == 0) echo "履歴無し";
    else {
      echo "履歴: <br>";
      foreach ($results as $row){
        echo "【".$row['id']."】";
        echo $row['name'].': ';
        echo $row['comment'];
        echo '（'.$row['t'].'）<br>';
      }
    }
    echo "<hr>";
  ?>

  <br><br>
  <b>入力フォーム</b><br>
  <form action="" method="post">
    名前　　　: <input type='text' name='name'
      value="<?php if(isset($edit_name)) echo $edit_name;?>" placeholder="空でも動きます"><br>
    コメント　: <input type='text' name='comment'
      value="<?php if(isset($edit_comment)) echo $edit_comment;?>" placeholder="空または0だと動きません"><br>
    パスワード: <input type='text' name='password' placeholder="空だと動きません"><br>
    <input type='submit' value='送信'><br>
    <input type='hidden' name='e_num' value="<?php if(isset($edit_num)) echo $edit_num; ?>"><br>
  </form><br>

  <!-- $edit_nameが空のとき value="" だから isset($_POST["name"])の戻り値はtrue -->

  <b>削除番号指定用フォーム</b><br>
  <form action="" method="post">
    <input type='text' name='delete_num' placeholder="削除対象番号">
    <input type='text' name='d_pass' placeholder="パスワード">
    <input type='submit' value='削除'>
  </form>

  <br>
  <b>編集番号指定用フォーム</b><br>
  <form action="" method="post">
    <input type='text' name='edit_num' placeholder="編集対象番号">
    <input type='text' name='e_pass' placeholder="パスワード">
    <input type='submit' value='編集'>
  </form>


  <?php
    // レコード追加
    function addData($pdo, $n, $c, $t, $p) {
      $sql = $pdo->prepare("INSERT INTO rireki(name, comment, t, pass) VALUES(:name, :comment, :t, :pass)");
      $sql -> bindParam(':name', $n, PDO::PARAM_STR);
      $sql -> bindParam(':comment', $c, PDO::PARAM_STR);
      $sql -> bindParam(':t', $t, PDO::PARAM_STR);
      $sql -> bindParam(':pass', $p, PDO::PARAM_STR);
      $sql -> execute();
    }

    // テーブル作成
    function createTable($pdo) {
      $sql = "CREATE TABLE IF NOT EXISTS rireki"
      ." ("
      . "id INT AUTO_INCREMENT PRIMARY KEY,"
      . "name char(32),"
      . "comment TEXT,"
      . "t TEXT,"
      . "pass char(32)"
      .");";
      $stmt = $pdo->query($sql);
    }
  ?>

</body>
