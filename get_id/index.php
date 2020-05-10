<?php
require_once('../db.php');

$app = basename($_SERVER['REQUEST_URI']);
$db = new mysqli($host, $username, $password, $db);
$db->autocommit(false);
$db->query("SET NAMES 'utf8'");
$db->query("SET CHARACTER SET 'utf8'");

$stmt = $db->prepare('SELECT appId FROM apps WHERE name = ?');
$stmt->bind_param('s', $app);
$stmt->execute();
$stmt->bind_result($id);
if(!$stmt->fetch()) {
	$stmt->close();
	$db->close();
	die();
}
$stmt->close();

$db->query('LOCK TABLES app_ids WRITE');

$stmt = $db->prepare('SELECT uid FROM app_ids WHERE uid = ? AND appId = ?');
while(true) {
	$app_id = sha1(rand());

	$stmt->bind_param('ss', $app_id, $id);
	$stmt->execute();
	$stmt->bind_result($uid);
	if(!$stmt->fetch()) {
		$stmt->close();
		break;
	}
}

$stmt = $db->prepare('INSERT INTO app_ids (appId, uid) VALUES (?, ?)');
$stmt->bind_param('is', $id, $app_id);
$stmt->execute();
$stmt->close();

$db->query('UNLOCK TABLES');

$db->commit();
$db->close();
echo $app_id;

