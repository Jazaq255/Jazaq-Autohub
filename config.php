<?php
$host = getenv('DB_HOST') ?: 'localhost';
$username = getenv('DB_USERNAME') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';
$dbname = getenv('DB_NAME') ?: 'car-dealership';

$conn= new mysqli($host,$username,$password,$dbname);
if ($conn->connect_error){
    die("Connection failed:".$conn->connect_error);
}
echo "";
?>