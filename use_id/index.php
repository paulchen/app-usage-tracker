<?php
require_once('../db.php');

$data = $_SERVER['REQUEST_URI'];
$pos = strrpos($data, '/');
if($pos === false) {
	die();
}
$uid = substr($data, $pos+1);
$data = substr($data, 0, $pos);
$pos = strrpos($data, '/');
if($pos === false) {
	die();
}
$app = substr($data, $pos+1);

$db = new mysqli($host, $username, $password, $db);
$db->autocommit(false);
$db->query("SET NAMES 'utf8'");
$db->query("SET CHARACTER SET 'utf8'");

$stmt = $db->prepare('SELECT appId FROM apps WHERE name = ?');
$stmt->bind_param('s', $app);
$stmt->execute();
$stmt->bind_result($app_id);
if(!$stmt->fetch()) {
	$stmt->close();
	$db->close();
	die();
}
$stmt->close();

$stmt = $db->prepare('SELECT appId FROM app_ids WHERE appId = ? AND uid = ?');
$stmt->bind_param('is', $app_id, $uid);
$stmt->execute();
$stmt->bind_result($id);
if(!$stmt->fetch()) {
	$stmt->close();
	$db->close();
	die();
}
$stmt->close();

$stmt = $db->prepare('INSERT INTO app_uses (appId, uid) VALUES (?, ?)');
$stmt->bind_param('is', $app_id, $uid);
$stmt->execute();
$stmt->close();

$db->commit();
$db->close();

echo 'ok';

