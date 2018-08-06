<?php
ini_set('max_execution_time', 999999);
ini_set('memory_limit','999999M');
ini_set('upload_max_filesize', '500M');
ini_set('max_input_time', '-1');
ini_set('max_execution_time', '-1');
//print_r($_FILES);
$response = array();
foreach($_FILES as $key => $file){
	if(is_array($_FILES[$key]["name"])){
		foreach($_FILES[$key]["name"] as $_key => $value) {
			$fileName = time().rand(0,99999).".".pathinfo($_FILES[$key]["name"][$_key], PATHINFO_EXTENSION);
			if(move_uploaded_file($_FILES[$key]['tmp_name'][$_key], "image/".$fileName)){
				$response['data'][] = $fileName;
			}
		}
	}
	else {
		$fileName = time().rand(0,99999).".".pathinfo($_FILES[$key]["name"], PATHINFO_EXTENSION);
		if(move_uploaded_file($_FILES[$key]['tmp_name'], "image/".$fileName)){
			$response['data'][] = $fileName;
		}
	}
}
if(!empty($response['data'])){
	$response['data'] = implode(',',$response['data']);
}
if(!empty($response)){
	$response['status'] = 1;
	echo json_encode($response);
	exit();
}else {
	$response['data'] = "No Image upload.";
	$response['status'] = 0;
	echo json_encode($response);
	exit();
}
exit();
$fileName = time().".".pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
move_uploaded_file($_FILES['image']['tmp_name'], $fileName);

$fileName1 = time()."1.".pathinfo($_FILES["image1"]["name"], PATHINFO_EXTENSION);
move_uploaded_file($_FILES['image1']['tmp_name'],$fileName1);

$fileName2 = time()."2.".pathinfo($_FILES["image2"]["name"], PATHINFO_EXTENSION);
move_uploaded_file($_FILES['image2']['tmp_name'],$fileName2);

$fileName3 = time()."3.".pathinfo($_FILES["image3"]["name"], PATHINFO_EXTENSION);
move_uploaded_file($_FILES['image3']['tmp_name'], $fileName3);


if(!empty($fileName)){
	$response['data'] = "Image uploaded successfully.";
	$response['image_name'] = $fileName;
	if($_FILES["image1"]["name"]){
		$response['image_name1'] = $fileName1?$fileName1:"";
	}
	if($_FILES["image2"]["name"]){
		$response['image_name2'] = $fileName2?$fileName2:"";
	}
	if($_FILES["image3"]["name"]){
		$response['image_name3'] = $fileName3?$fileName3:"";
	}
	$response['status'] = "1";
	echo json_encode($response);
	exit();
}else {
	$response['data'] = "No Image upload.";
	$response['status'] = "0";
	echo json_encode($response);
	exit();
}
?>
