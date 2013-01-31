
<?
	$id = $_GET['id'];


	$db = new PDO('mysql:host=localhost;dbname=bikemap', 'zero', 'bikemap1234');

	$stmt = $db->prepare("SELECT * FROM collision WHERE incident_ID = ? LIMIT 0,5" );
	$stmt->bindValue(1, $id, PDO::PARAM_INT);
	$stmt->execute();
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	//print_r($rows);
	echo json_encode($rows);

?>
