<?php

$conn = mysqli_connect(
    "localhost",
    "root",
    "",
    "expense_tracker"
);

if(!$conn){
    die("Connection Failed");
}

?>