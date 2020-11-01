<?php
    $dsn = 'mysql:dbname=tb******db;host=localhost';
    $user = 'tb-******';
    $password = '*';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    //テーブルの作成
	$sql = "CREATE TABLE IF NOT EXISTS mission"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
	. "comment TEXT,"
	. "date TEXT,"
	. "pass TEXT"
	.");";
	$stmt = $pdo->query($sql);
    //テーブル構成詳細を表示
    /*
	$sql ='SHOW CREATE TABLE mission';
	$result = $pdo -> query($sql);
	foreach ($result as $row){
		echo $row[1];
	}
	echo "<hr>";
	*/
    
	//新規投稿
	if (!empty($_POST["name"]) && !empty($_POST["comment"]) 
    && !empty($_POST["submit"])  && !empty($_POST["pass"]) && empty($_POST["edit_post"])) {
	//POST処理
	    $name = $_POST['name'];
	    $comment = $_POST['comment'];
	    $date = date("Y年m月d日 H:i:s");
	    $pass = $_POST['pass'];
	//$hash = password_hash ($pass, PASSWORD_DEFAULT);
	//データベースへ書き込み
	    $sql = $pdo -> prepare("INSERT INTO mission (name, comment, date, pass) 
	    VALUES (:name, :comment, :date, :pass)");
	    $sql -> bindParam(':name', $name, PDO::PARAM_STR);
	    $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
	    $sql -> bindParam(':date', date("Y年m月d日 H:i:s"), PDO::PARAM_STR);
	    //$sql -> bindValue(':pass', $hash, PDO::PARAM_STR);
	    $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
	    $sql -> execute();
    } elseif ((!empty($_POST["edit_num"])) && (!empty($_POST["edit_btn"])) && (!empty($_POST["edit_pass"])) 
        && (empty($_POST["delete"])) && (empty($_POST["delete_ad"]))) {
    //編集番号取得
        $edit_num = $_POST["edit_num"];
        $ed_pass = $_POST["edit_pass"];
        $id = $edit_num;
        $sql = 'SELECT * FROM mission WHERE id=:id';
        $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);// ←その差し替えるパラメータの値を指定してから、
        $stmt->execute();                             // ←SQLを実行する。
        $results = $stmt->fetchAll(); 
	    foreach ($results as $row){
		//$rowの中にはテーブルのカラム名が入る
		    if($ed_pass == $row['pass']) {
		        $edit_post = $row['id'];
		        $edit_name = $row['name'];
		        $edit_comment = $row['comment'];
		        $edit_pass = $row['pass'];
		    } else {
		        echo "パスワードが合致しませんでした";
		    }
	    }
    }  elseif (!empty($_POST["name"]) && !empty($_POST["comment"]) 
    && !empty($_POST["submit"]) && !empty($_POST["edit_post"]) && !empty($_POST["pass"])) {
    //編集機能
	    $id = $_POST["edit_post"]; //変更する投稿番号
	    $name = $_POST["name"];
	    $comment = $_POST["comment"];
	    $date = date("Y年m月d日 H:i:s");
	    $pass = $_POST['pass'];
        $sql = 'UPDATE mission SET name=:name,comment=:comment,date=:date WHERE id=:id && pass=:pass';
	    $stmt = $pdo->prepare($sql);
	    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
	    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
	    $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
	    $stmt->bindParam(':date', date("Y年m月d日 H:i:s"), PDO::PARAM_STR);
	    $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
	    $stmt->execute();
    } elseif ((!empty ($_POST["delete"])) && (!empty($_POST["delete_ad"])) && (!empty($_POST["delete_pass"])) &&
            (empty($_POST["name"])) && (empty($_POST["comment"]))) {
    //削除機能
        $delete = $_POST["delete"];
		$id = $delete;
		$pass = $_POST["delete_pass"];
	    $sql = 'delete from mission where id=:id && pass=:pass';
	    $stmt = $pdo->prepare($sql);
	    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
	    $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
	    $stmt->execute();
	} else {
		  //echo "条件が合致しませんでした";
	}
	
	
?>
<!DOCTYPE html>
<html lang = "ja">
<head>
    <meta charaset = "UTF-8">
    <title>mission_5-2</title>
</head>
<body>
    <form action = "#" method = "post">
        <p>
            【投稿フォーム】<br>
            <input type = "text" name = "name" placeholder = "名前" value = <?php echo $edit_name; ?> > <br>
            <input type = "text" name = "comment" placeholder = "コメント" value = <?php echo $edit_comment; ?>> <br>
            <input hidden = "text" name = "edit_post" value = <?php echo $edit_post; ?>>
            <input type="password" name="pass" placeholder = "パスワード" value = <?php echo $edit_pass?> > <br>
            <input type = "submit" name = "submit">
        </p>
        <p>    
            【削除】<br> 
            <input type = "number" name = "delete"><br>
            <input type="password" name="delete_pass" placeholder = "パスワード"><br>
            <input type = "submit" name = "delete_ad" value = "削除">
            <br>
        </p>
        <p>
            【編集】<br> <input type = "number" name = "edit_num" placeholder = "編集したい番号"><br>
            <input type="password" name="edit_pass" placeholder = "パスワード"><br>
            <input type = "submit" name = "edit_btn" value = "編集">
            
        </p>
    </form>
    
    <?php
    //入力したデータレコードを抽出し、表示する
	$sql = 'SELECT * FROM mission';
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
	foreach ($results as $row){
		//$rowの中にはテーブルのカラム名が入る
		echo $row['id'].',';
		echo $row['name'].',';
		echo $row['comment'].',';
		echo $row['date'].',';
	echo "<hr>";
	} 
	
    ?>

</body>
</html>