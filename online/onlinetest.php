<?php 
require 'dbconfig.php';
// Set the response content type to JSON
header("Content-Type: application/json");


// Get the HTTP method and input data
$method = $_SERVER['REQUEST_METHOD'];
//$input = json_decode(file_get_contents('php://input'), true);

// Handle PATH_INFO safely
$request = array();
if (isset($_SERVER['PATH_INFO'])) {
    $request = explode('/', trim($_SERVER['PATH_INFO'], '/'));
}

    
// Handle the request
switch ($method) {
    

    case 'POST':
        // Insert data into the database
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Retrieve form data
            $name = $_POST['name']??'';
            $price = $_POST['price']??'';
            $description = $_POST['description']??'';

        } else {
            http_response_code(400);
            echo json_encode(array('status'=>'error',"message" => "Invalid request method"));
        }
        
        if(!empty($name)){
            // To check Product from the database
            $sql = "SELECT name FROM products WHERE name='$name'";
            $result = $conn->query($sql);

            
            $row = $result->fetch_assoc();
            if ($row) {
                http_response_code(200);
                echo json_encode(array('status'=>'error',"message" => "Products Exist"));
            }else{
                $sql = "INSERT INTO products (name, price,description) VALUES ('$name', '$price','$description')";
                if ($conn->query($sql)) {
                    http_response_code(200);
                    echo json_encode(array('status'=>'success',"message" => "Product created successfully"));
                } else {
                    http_response_code(400);
                    echo json_encode(array('status'=>'error',"message" => "Error creating Product"));
                }
            }
                
           
            
        }else{
            http_response_code(400);
            echo json_encode(array('status'=>'error',"message" => "Please give Product name"));
        }
        break;
    case 'GET':
        $product_id = $request[0]??NULL;
        // Fetch data from the database
        if($product_id){
            $sql = "SELECT * FROM products WHERE id='$product_id'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $products = array();
                if ($row = $result->fetch_assoc()) {
                    $products = $row;
                }
                
                http_response_code(200);
                echo json_encode(array('status'=>'success',"message" => "Product",'data'=>$products));
                
            } else {
                http_response_code(400);
                echo json_encode(array('status'=>'error',"message" => "No Product found"));
            }
        }else{
            $sql = "SELECT * FROM products";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $products = array();
                while ($row = $result->fetch_assoc()) {
                    $products[] = $row;
                }
                
                http_response_code(200);
                echo json_encode(array('status'=>'success',"message" => "Product list",'data'=>$products));
            } else {
                http_response_code(400);
                echo json_encode(array('status'=>'error',"message" => "No Products found"));
            }
        }
        break;
    case 'PUT':
        // Update data in the database
        $product_id = $request[0]; // Get the ID from the URL
        if($product_id){
            // Read the raw input from the request body
            $input = file_get_contents('php://input');

            // Parse the raw input into an associative array
            parse_str($input, $data);

            // Check if the data is valid
            if (empty($data)) {
                http_response_code(400);
                echo json_encode(array('status'=>'error',"message" => "No data received"));
                exit;
            }
            $sql = "SELECT name,price,description FROM products WHERE id='$product_id'";
            $result = $conn->query($sql);
            $row = $result->fetch_assoc();
            $name = $row['name'];
            $price = $row['price'];
            $description = $row['description'];
            if(isset($data['name'])){
                $name = $data['name'];
            }
            if(isset($data['price'])){
                $price = $data['price'];
            }
            if(isset($data['description'])){
                $description = $data['description'];
            }
            
            if($name){
                $sql = "SELECT name FROM products WHERE name='$name' AND id NOT IN ($product_id)";
                $result = $conn->query($sql);
                $row = $result->fetch_assoc();
                if($row){
                    http_response_code(400);
                    echo json_encode(array('status'=>'error',"message" => "Product Exist"));
                }else{
                    $sql = "UPDATE products SET name='$name', price='$price',description='$description' WHERE id=$product_id";
                    if ($conn->query($sql)) {
                        http_response_code(200);
                        echo json_encode(array('status'=>'success',"message" => "Product updated successfully"));
                    } else {
                        http_response_code(400);
                        echo json_encode(array('status'=>'error',"message" => "Error updating Product"));
                    }
                }
            }else{
                $sql = "UPDATE products SET price='$price',description='$description' WHERE id=$product_id";
                if ($conn->query($sql)) {
                    http_response_code(200);
                    echo json_encode(array('status'=>'success',"message" => "Product updated successfully"));
                } else {
                    http_response_code(400);
                    echo json_encode(array('status'=>'error',"message" => "Error updating Product"));
                }
            }
        }else{
            http_response_code(400);
                echo json_encode(array('status'=>'error',"message" => "Invalid Product Id"));
        }
        break;

    case 'DELETE':
        // Delete data from the database
        $product_id = $request[0]; // Get the ID from the URL

        $sql = "DELETE FROM products WHERE id=$product_id";
        if ($conn->query($sql)) {
            http_response_code(200);
            echo json_encode(array('status'=>'success',"message" => "Product deleted successfully"));
        } else {
            http_response_code(400);
            echo json_encode(array('status'=>'error',"message" => "Error deleting Product"));
        }
        break;

    default:
        // Invalid method
        http_response_code(405);
        echo json_encode(array('status'=>'error',"message" => "Method not allowed"));
        break;
}
?>