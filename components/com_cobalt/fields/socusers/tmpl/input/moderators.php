<?php
/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */




class Craftmoders
{
	public $_pdo = null;

	// connect DB to record craft moderators
	public function __construct()
	{
		try
		{
			$this->_pdo = new PDO(
				'mysql:dbname=romanosz_craft;host=localhost',
				"romanosz_craft",
				"Gfhjkm234303",
				array(PDO::ATTR_PERSISTENT => true)
			);
		}
		catch (PDOException $e)
		{
			die($e->getMessage());

		}

        $cuser_id = $_POST['cuser_id'];
		$moder_record = $_POST['moder_record'];

		if (isset($_POST['save'])) {
		$this->moderatorsave($cuser_id, $moder_record);
        }

		if (isset($_POST['delete'])) {
			$this->deletemoderator($cuser_id, $moder_record);
		}

	}
    // Record to DB new craft cobalt wineries moderators
	public function moderatorsave($cuser_id, $moder_record )
	{
		echo $moder_record.'<br>';
		echo $cuser_id;

		$result = $this->_pdo->exec('INSERT INTO craft_cobalt_moderators (cuser_id, moder_record) VALUES (' . $cuser_id . ', ' . $moder_record . ')');
		//$result2 = $this->_pdo->exec('INSERT INTO craft_user_usergroup_map (user_id, group_id) VALUES (' . $cuser_id . ',  3)');
		if ($result === false) throw new Exception($this->_pdo->errorInfo()[2]);
		//if ($result2 === false) throw new Exception($this->_pdo->errorInfo()[2]);

	}
	// Delete from DB craft cobalt wineries moderators
	public function deletemoderator ($cuser_id, $moder_record) {
		$result = $this->_pdo->exec('DELETE FROM  `craft_cobalt_moderators` WHERE `cuser_id` = '.$cuser_id.' AND `moder_record` = ' . $moder_record);
		//$result2 = $this->_pdo->exec('DELETE FROM  `craft_user_usergroup_map` WHERE `user_id` = '.$cuser_id.' AND `group_id` = 3');
		if($result === false) throw new Exception($this->_pdo->errorInfo()[2]);
		//if($result2 === false) throw new Exception($this->_pdo->errorInfo()[2]);
	}

}
try{
	$moders = new Craftmoders();
} catch(Exception $e) {
	die('Line '.$e->getLine().': '.$e->getMessage());
}

$body = '';
$body_html = '';


?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title> result</title>
</head>
<body>

<?


if (isset($_POST['save'])) {
    echo 'Пост на сохранение прошел';
    var_dump($_POST);
}
if (isset($_POST['delete'])) {
	echo 'Пост на удаление прошел';
    var_dump($_POST);
}
?>
<?=$body_html?>
<?=$body?>