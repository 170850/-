



<?php
//⓪（データベースと接続）
$dsn = 'データベース';
$user = 'ユーザー名';
$password = 'パスワード';
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
//array(~)とは、データベース操作で発生したエラーを警告として表示してくれる設定をするための要素

//（データベース内にテーブルを作成）
$sql = "CREATE TABLE IF NOT EXISTS newtable"
."("
. "id INT AUTO_INCREMENT PRIMARY KEY,"
. "name char(32),"
. "comment TEXT,"
. "date DATETIME,"
. "pass TEXT"
. ");";

$stmt = $pdo->query($sql);

//編集中かどうかを示すフラッグ（印のようなもの）です。20191012
$editingFlag = false;


if(!empty($_POST["name"]) && !empty($_POST["comment"])){ //投稿（編集＆新規）

    if(!empty($_POST["editnumber"])){//編集投稿
        $ednum = $_POST["editnumber"];//変更する投稿番号
	    $edname = $_POST["name"];
        $edcom = $_POST["comment"]; //変更する名前、変更したいコメント
        $edate = date("Y/m/d H:i:s");
 
        $sql = "update newtable set name=:name,comment=:comment,date=:date where id = $ednum";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $edname, PDO::PARAM_STR);
        $stmt->bindParam(':comment', $edcom, PDO::PARAM_STR);
        $stmt->bindParam(':date',$edate, PDO::PARAM_STR);
        $stmt->execute();

         //mission4-6 表示させる
         $sql = 'SELECT * FROM newtable';
         $stmt = $pdo->query($sql);
         $results = $stmt->fetchAll();
         foreach ($results as $row){
             //$rowの中にはテーブルのカラム名が入る
                 echo $row['id'].',';
                 echo $row['name'].',';
                 echo $row['comment'].',';
                 echo $row['date'].'<br>';
                 echo "<hr>";
         }//foreach
    
        
    }//編集終了
    else{//新規投稿（編集番号なし

        if(!empty($_POST["password"])){

            $sql = $pdo -> prepare("INSERT INTO newtable (name,comment,date,pass) VALUES (:name,:comment,:date,:pass)");
            $sql -> bindParam(':name', $name, PDO::PARAM_STR);
            $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
            $sql -> bindParam(':date', $date, PDO::PARAM_STR);
            $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);

            $name = $_POST["name"];
            $comment = $_POST["comment"]; 
            $pass = $_POST["password"];
            $date = date("Y/m/d H:i:s");
            
            $sql -> execute();

            //mission4-6 表示させる
            $sql = 'SELECT * FROM newtable';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            foreach ($results as $row){
                //$rowの中にはテーブルのカラム名が入る
                    echo $row['id'].',';
                    echo $row['name'].',';
                    echo $row['comment'].',';
                    echo $row['pass'].',';
                    echo $row['date'].'<br>';
                    echo "<hr>";
            }//foreach

    }else{//パスワードなし
        echo "パスワードを入力してください";
    }
    
    }###新規投稿
}//投稿おわり

elseif(!empty($_POST["delete"])&& !empty($_POST["delPass"])){#削除
    $delete = $_POST["delete"];
    
    //特定の番号を取り出す
    $sql = 'SELECT * FROM newtable where id = :id';
    $stmt = $pdo ->prepare($sql);
    $stmt ->bindParam(':id',$delete,PDO::PARAM_INT);
    $stmt -> execute();
    $results =$stmt -> fetch();

    if($results['pass'] == $_POST["delPass"]){//パス一致

        $sql = "delete from newtable where id = $delete" ;
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        //mission4-6 表示させる
        $sql = 'SELECT * FROM newtable';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){
                echo $row['id'].',';
                echo $row['name'].',';
                echo $row['comment'].',';
                echo $row['date'].'<br>';
                echo "<hr>";
        }
    }//パス一致おわり
    else{//パス不一致
        echo "パスワードがちがいます";
    }
    

}#削除
 
elseif(!empty($_POST["editnumber"])){//編集選択
    $edid = $_POST["editnumber"];

    //編集状態に入ったのでtrueにします。20191012
    $editingFlag = true;

    //特定の番号を取り出す
    $sql = 'SELECT * FROM newtable where id = :id';
    $stmt = $pdo ->prepare($sql);
    $stmt ->bindParam(':id',$edid,PDO::PARAM_INT);
    $stmt -> execute();
    $results =$stmt -> fetch();

    if($results['pass'] == $_POST["ediPass"]){//パス一致

    $sql = "SELECT * FROM newtable where id = $edid";
    $stmt = $pdo ->prepare($sql);
    $stmt -> execute();
    $results = $stmt ->fetch();
        $edid = $results['id'];
        $edname = $results['name'];
        $edcom = $results['comment'];
        $edate = $results['date'];
    

        $sql = 'SELECT * FROM newtable';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){
                echo $row['id'].',';
                echo $row['name'].',';
                echo $row['comment'].',';
                echo $row['date'].'<br>';
                echo "<hr>";
        }
    }//パス一致おわり
    else{
        echo "パスワードがちがいます";
    }


}//へんしゅうせんたく
?>

<html>
 <head>
<!---入力フォーム--->
   <form method = "POST">
    <!-- 以下のinputでは、もし編集中フラッグ(editingFlag)がtrueのときのみ、
    テキストボックスに文字を再表示するようにする20191012 -->
    <input type = "text" name = "name" placeholder = "名前" value = "<?php if($editingFlag){echo $edname;} ?>"><br>
    <input type = "text" name = "comment" placeholder = "コメント" value = "<?php if($editingFlag){echo $edcom;} ?>"><br>
    <input type = "hidden" name = "editnumber" value = "<?php if($editingFlag){echo $edid;} ?>"><br>
    <input type = "text" name = "password" placeholder = "パスワード">
    <input type = "submit" value = "送信">
   </form> 
            <br>
            <br>
<!---削除フォーム--->
   <form method = "POST">
    <input type = "text" name = "delete" placeholder = "削除番号">
    <input type = "text" name = "delPass" placeholder = "削除パスワード">
    <input type = "submit" value = "削除">
   </form>
<!---編集フォーム--->
   <form method = "POST">
   <input type = "text" name = "editnumber" placeholder = "編集番号">
   <input type = "text" name = "ediPass" placeholder = "編集パスワード">
   <input type = "submit" value = "編集"><br>

   
  </form>
 </head>
</html>

<!---流れ
⓪データベースに接続
②ファイルをプラウザに表示
③削除機能追加
④編集機能追加
⑤パスワード追加!>



