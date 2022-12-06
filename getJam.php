<?php
include "config.php";

$departid = 0;

if(isset($_POST['depart'])){
   $departid = mysqli_real_escape_string($con,$_POST['depart']); // department id
}

$users_arr = array();

if($departid > 0){
    $sql = "SELECT dep_id,jam_masuk,jam_pulang FROM jam_jaga WHERE dep_id=".$departid;

    $result = mysqli_query($con,$sql);
    
    while( $row = mysqli_fetch_array($result) ){
        $depid = $row['dep_id'];
        $jammasuk = $row['jam_masuk'];
        $jampulang = $row['jam_pulang'];
    
        $users_arr[] = array("dep_id" => $depid, "jam_masuk" => $jammasuk, "jam_pulang" => $jampulang);
    }
}

// encoding array to json format
echo json_encode($users_arr);
?>
