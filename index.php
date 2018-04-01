<?php
ini_set('display_errors', 1); // エラーログをHTMLに出力

// 定数宣言
define('DB_DATABASE', 'bbs_db');	// DB名
define('DB_USERNAME', 'dbuser');	// ユーザ名
define('DB_PASSWORD', 'dbuser');	// パスワード
define('PDO_DSN', 'mysql:host=localhost;dbname=' . DB_DATABASE . ';charset=utf8');	//データソース名

// データベースを操作する変数
$db;

// 特殊文字をエスケープするラッパー関数
function h($s){
	return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

try{
	// PDOオブジェクトの作成
	$db = new PDO(PDO_DSN, DB_USERNAME, DB_PASSWORD);
	// 静的プレースホルダを用いるようにエミュレーションを無効化
	$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	// 例外を投げるようにエラーモードを設定
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(PDOException $e){
	// 例外のメッセージを出力
	echo $e->getMessage();
	// 終了
	exit;
}

// ログインボタンが押されたときの処理
if(isset($_POST["login"])){
	// 入力されたloginIDと一致するデータを取得
	$stmt = $db->prepare("select * from userData where loginID = :loginID");
	$loginID = h($_POST["loginID"]);
	$stmt->bindParam(':loginID', $loginID, PDO::PARAM_STR);
	$stmt->execute();

	// 取得したデータ群からパスワードが一致するものがあるかチェック
	$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
	foreach($users as $user){
		// 入力されたパスワードをハッシュ化
		$loginPass = password_hash($_POST["loginPass"], PASSWORD_DEFAULT);
		// データベースに登録されたパスと一致するか
		if( $loginPass === $user['loginPass'] ){
			echo "correct!";
		}
	}
	echo "incorrect!";
}
?>

<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>ログイン</title>
	</head>

	<body>
		<h1>ログイン画面</h1>

		<form id="loginForm" name="loginForm" action="" method="POST">
			<fieldset>
			<legend>ログインフォーム</legend>
			loginID:<input type="text" id="loginID" name="loginID">
			loginPass:<input type="text" id="loginPass" name="loginPass">
			<input type="submit" id="login" name="login" value="ログイン">
			</fieldset>
		</form>

		<form action="SignUp.php">
			<fieldset>
				<legend>新規登録フォーム</legend>
				<input type="submit" value="新規登録">
			</fieldset>
		</form>
	</body>
</html>

