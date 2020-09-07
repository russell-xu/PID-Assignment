<?php
$target_dir = "../img/";
$target_file = $target_dir . basename($_FILES["product_images"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

// Check if image file is a actual image or fake image
if (isset($_POST["submit"])) {
  $check = getimagesize($_FILES["product_images"]["tmp_name"]);
  if ($check !== false) {
    echo "<script type='text/javascript'>alert('File is an image - {$check["mime"]}');</script>";
    $uploadOk = 1;
  } else {
    echo "<script type='text/javascript'>alert('File is not an image.');</script>";
    $uploadOk = 0;
  }
}

// Check if file already exists
if (file_exists($target_file)) {
  echo "<script type='text/javascript'>alert('Sorry, file already exists.');</script>";
  $uploadOk = 0;
}

// Check file size
if ($_FILES["product_images"]["size"] > 500000) {
  echo "<script type='text/javascript'>alert('Sorry, your file is too large.');</script>";
  $uploadOk = 0;
}

// Allow certain file formats
if (
  $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
  && $imageFileType != "gif"
) {
  echo "<script type='text/javascript'>alert('Sorry, only JPG, JPEG, PNG & GIF files are allowed.');</script>";
  $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
  echo "<script type='text/javascript'>alert('Sorry, your file was not uploaded.');</script>";
  // if everything is ok, try to upload file
} else {
  if (move_uploaded_file($_FILES["product_images"]["tmp_name"], $target_file)) {
    echo "<script type='text/javascript'>alert('The file " . basename($_FILES["product_images"]["name"]) . " has been uploaded.');</script>";
  } else {
    echo "<script type='text/javascript'>alert('Sorry, there was an error uploading your file.');</script>";
  }
}
