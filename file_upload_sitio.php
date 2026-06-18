<?php 
if(!empty($_FILES)){     
    $upload_dir = "uploads/";
    $fileName = $_FILES['file']['name'];
    $uploaded_file = $upload_dir.$fileName;
    move_uploaded_file($_FILES['file']['tmp_name'],$uploaded_file);
}
