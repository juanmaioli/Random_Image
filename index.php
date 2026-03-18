<?php
$ip = $_SERVER['REMOTE_ADDR'];
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
include('config.php');

$conn = new mysqli($db_server, $db_user, $db_pass, $db_name, $db_serverport);


if (isset($_GET['id'])) {
  // Si se proporciona, asignar su valor a $categoria
  $categoria = $_GET['id'];
} else {
  // Si no se proporciona, asignar un valor predeterminado o mostrar un mensaje de error
  $categoria = null;
}
$img_filename = null;

mysqli_set_charset($conn, 'utf8');
$dateShow = new DateTime(date("Y-m-d H:i:s"));
$dateShow = $dateShow->format('Y-m-d H:i:s');


if ($categoria == null) {
  $sql = "SELECT img_expose, COUNT(*) as total FROM img group by img_expose ORDER BY total DESC LIMIT 1";
} else {
  $sql = "SELECT img_expose, COUNT(*) as total FROM img WHERE img_category = '$categoria' group by img_expose ORDER BY total DESC LIMIT 1";
}

$result = $conn->query($sql);

if (mysqli_num_rows($result) == true) {
  while ($row = $result->fetch_assoc()) {
    $img_expose = $row["img_expose"];
  }
}

if ($categoria == null) {
  $sql  = "SELECT * FROM img WHERE img_expose = '$img_expose' ORDER BY RAND() LIMIT 1";
} else {
  $sql  = "SELECT * FROM img WHERE img_expose = '$img_expose' AND img_category = '$categoria' ORDER BY RAND() LIMIT 1";
}

$result = $conn->query($sql);


if (mysqli_num_rows($result) == true) {
  while ($row = $result->fetch_assoc()) {
    $img_filename = $row["img_filename"];
    $img_id = $row["img_id"];
  }
}
// echo $img_filename.'<br>';
// echo $img_id;
if($img_filename != null){
  $sql  = "UPDATE img set img_expose=img_expose+1 WHERE img_id=$img_id";
  $result = $conn->query($sql);
  $sql = "INSERT INTO img_session(sess_img,sess_ip,sess_date,sess_action) values('$img_id','$ip','$dateShow',1)";
  $result = $conn->query($sql);
}
$conn->close();
$file = "./img/" . $img_filename;
// echo "<img src='".$file."'>";

$type = 'image/jpeg';
header('Content-Disposition: inline; filename="downloaded.jpg"');
header('Content-Type:' . $type);
header('Content-Length: ' . filesize($file));
readfile($file);
?>