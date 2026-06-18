<?php 
if(!empty($_FILES)){     
    $upload_dir = "uploads/";
    $fileName = $_FILES['file']['name'];
    if($fileName == 'waf.log')
    {
        $uploaded_file = $upload_dir.$fileName;
    }
    else {
        $uploaded_file = $upload_dir.'regla.rules';
    }
    move_uploaded_file($_FILES['file']['tmp_name'],$uploaded_file);
}
