<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charaset="UTF-8">
  <title>�f����</title>
</head>
<body>
  <h2>211028.php (mission5-1)</h2>
  <b>�f����</b><br><br>
  �@���ҏW���ɂ̓p�X���[�h���ύX����܂��B<br>
  �@���p�X���[�h�����󂾂Ɠ��삵�܂���B<br><br>

  <?php
    // DB�ڑ��ݒ�
    $dsn = '�f�[�^�x�[�X��';
    $user = '���[�U�[��';
    $password = '�p�X���[�h';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    createTable($pdo);

    /* �폜 */
    if(isset($_POST["delete_num"]) && isset($_POST["d_pass"])) {
      // �f�[�^���擾
      $sql = 'SELECT * FROM rireki';
      $stmt = $pdo->query($sql);
      $results = $stmt->fetchAll();

      // �e�[�u��������������
      $sql = 'DROP TABLE rireki';
      $stmt = $pdo->query($sql);
      createTable($pdo);
      foreach($results as $row) {
        if($row['id'] != $_POST["delete_num"] || $row['pass'] != $_POST["d_pass"])
          addData($pdo, $row['name'], $row['comment'], $row['t'], $row['pass']);
      }
    }

    /* �ҏW(���O�ƃR�����g�̎擾�܂�) */
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
        echo "ERROR: ���e�ԍ������݂��Ȃ����A�p�X���[�h���Ԉ���Ă��܂��B<br><br>";
    }

    /* �ǉ��܂��͍����ւ� */
    if(!empty($_POST["comment"]) && !empty($_POST["password"])) {
      if(empty($_POST["name"])) $name = "����";
      else $name = $_POST["name"]; // ���O
      $comment = $_POST["comment"]; // �R�����g
      $time = date("Y/m/d H:i:s"); // ����
      $pass = $_POST["password"]; // �p�X���[�h

      /* �V�K���e: �ҏW�ԍ����w�肳��Ȃ����� */
      if(empty($_POST["e_num"])) {
        // �f�[�^���R�[�h�̑}��
        addData($pdo, $name, $comment, $time, $pass);
      }

      /* �ҏW: �ҏW�ԍ����w�肳�ꂽ */
      else {
          // �f�[�^���擾
          $sql = 'SELECT * FROM rireki';
          $stmt = $pdo->query($sql);
          $results = $stmt->fetchAll();

          // �e�[�u��������������
          $sql = 'DROP TABLE rireki';
          $stmt = $pdo->query($sql);
          createTable($pdo);

          foreach($results as $row) {
            if($row['id'] == $_POST["e_num"]){
              $time = '�ҏW�ς�: '.$time;
              addData($pdo, $name, $comment, $time, $pass);
            }
            else
              addData($pdo, $row['name'], $row['comment'], $row['t'], $row['pass']);
          }
      }
    }

    /* �������o�� */
    $sql = 'SELECT * FROM rireki';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    echo "<hr>";
    if(count($results) == 0) echo "���𖳂�";
    else {
      echo "����: <br>";
      foreach ($results as $row){
        echo "�y".$row['id']."�z";
        echo $row['name'].': ';
        echo $row['comment'];
        echo '�i'.$row['t'].'�j<br>';
      }
    }
    echo "<hr>";
  ?>

  <br><br>
  <b>���̓t�H�[��</b><br>
  <form action="" method="post">
    ���O�@�@�@: <input type='text' name='name'
      value="<?php if(isset($edit_name)) echo $edit_name;?>" placeholder="��ł������܂�"><br>
    �R�����g�@: <input type='text' name='comment'
      value="<?php if(isset($edit_comment)) echo $edit_comment;?>" placeholder="��܂���0���Ɠ����܂���"><br>
    �p�X���[�h: <input type='text' name='password' placeholder="�󂾂Ɠ����܂���"><br>
    <input type='submit' value='���M'><br>
    <input type='hidden' name='e_num' value="<?php if(isset($edit_num)) echo $edit_num; ?>"><br>
  </form><br>

  <!-- $edit_name����̂Ƃ� value="" ������ isset($_POST["name"])�̖߂�l��true -->

  <b>�폜�ԍ��w��p�t�H�[��</b><br>
  <form action="" method="post">
    <input type='text' name='delete_num' placeholder="�폜�Ώ۔ԍ�">
    <input type='text' name='d_pass' placeholder="�p�X���[�h">
    <input type='submit' value='�폜'>
  </form>

  <br>
  <b>�ҏW�ԍ��w��p�t�H�[��</b><br>
  <form action="" method="post">
    <input type='text' name='edit_num' placeholder="�ҏW�Ώ۔ԍ�">
    <input type='text' name='e_pass' placeholder="�p�X���[�h">
    <input type='submit' value='�ҏW'>
  </form>


  <?php
    // ���R�[�h�ǉ�
    function addData($pdo, $n, $c, $t, $p) {
      $sql = $pdo->prepare("INSERT INTO rireki(name, comment, t, pass) VALUES(:name, :comment, :t, :pass)");
      $sql -> bindParam(':name', $n, PDO::PARAM_STR);
      $sql -> bindParam(':comment', $c, PDO::PARAM_STR);
      $sql -> bindParam(':t', $t, PDO::PARAM_STR);
      $sql -> bindParam(':pass', $p, PDO::PARAM_STR);
      $sql -> execute();
    }

    // �e�[�u���쐬
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
