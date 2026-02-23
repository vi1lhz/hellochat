<?php
include_once("db_connect.php");

$firstname = $middlename = $lastname = $gender = $dob = "";
$username = $password = $email = $status = $type = "";

if (isset($_GET["id"])){
	$get_sql = "SELECT * FROM tbl_account WHERE id = ".$_GET["id"];
	$result = $conn->query($get_sql);

	if ($result->num_rows > 0) {
	  while($row = $result->fetch_assoc()) {
		  $username = $row["username"];
		  $email = $row["email"];
		  $status = $row["status"];
		  $type = $row["type"];
	  }
	}

	$get_sql = "SELECT * FROM tbl_user WHERE account_id = ".$_GET["id"];
	$result = $conn->query($get_sql);
	if ($result->num_rows > 0) {
	  while($row = $result->fetch_assoc()) {
		  $firstname = $row["firstname"];
		  $middlename = $row["middlename"];
		  $lastname = $row["lastname"];
		  $gender = $row["gender"];
		  $dob = $row["dob"];
	  }
	}
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username   = test_input($_POST["username"]);
    $password   = sha1(test_input($_POST["password"]));
    $firstname  = test_input($_POST["firstname"]);
    $middlename = test_input($_POST["middlename"]);
    $lastname   = test_input($_POST["lastname"]);
    $gender     = test_input($_POST["gender"]);
	$dob        = test_input($_POST["dob"]);
	$email		= test_input($_POST["email"]);
	$status		= test_input($_POST["status"]);
	$type		= test_input($_POST["type"]);
    $date_updated = date("Y-m-d H:i:s");

    if (!empty($_GET['id'])) {
        $id = intval($_GET['id']);
        $update_sql = "UPDATE tbl_account SET 
            username='$username', 
            password='$password', 
            firstname='$firstname', 
            middlename='$middlename', 
            lastname='$lastname', 
            gender='$gender', 
            date_updated='$date_updated' 
            WHERE id=$id";

        if ($conn->query($update_sql) === TRUE) {
			header("Location: form.php");
			exit();
            echo "Record updated successfully";
        } else {
            echo "Error updating record: " . $conn->error;
        }

    } else {
        $date_created = date("Y-m-d H:i:s");
		$date_updated = date("Y-m-d H:i:s");
        $insert_sql = "INSERT INTO tbl_account 
            (username, password, email, status, type, date_created, date_updated)
            VALUES ('$username','$password','$email','$status','$type','$date_created','$date_updated')";

		
		if ($conn->query($insert_sql) === TRUE){
			$account_id = $conn->insert_id;

			$insert_sql = "INSERT INTO tbl_user 
            (account_id,firstname, middlename, lastname, gender, dob)
            VALUES ('$account_id','$firstname','$middlename','$lastname','$gender','$dob')";

        	if ($conn->query($insert_sql) === TRUE) {
            echo "New record created successfully";
        	} else {

            echo "Error inserting record: " . $conn->error;
        	}
		}

		
    }
}


function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
?>
<table border="1">
	<th>ID</th>
	<th>Username</th>
	<th>Firstname</th>
	<th>Middlename</th>
	<th>Lastname</th>
	<th>Gender</th>
	<th>Date of Birth</th>
	<th>Email Address</th>
	<th>Status</th>
	<th>Type</th>
	<th>Date Created</th>
	<th>Date Updated</th>
	<th>Action</th>
	<?php
	$sql = "SELECT a.id, a.username, a.email, a.status, a.type, a.date_created, a.date_updated, 
	u.firstname, u.middlename, u.lastname, u.gender, u.dob
	FROM tbl_account a
	JOIN tbl_user u ON u.account_id = a.id";

	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
	  while($row = $result->fetch_assoc()) {
	?>
	<tr>
		<td><?php echo $row["id"]; ?></td>
		<td><?php echo $row["username"]; ?></td>
		<td><?php echo $row["firstname"]; ?></td>
		<td><?php echo $row["middlename"]; ?></td>
		<td><?php echo $row["lastname"]; ?></td>
		<td><?php if ($row["gender"] == 0){ echo "Male"; } else { echo "Female"; } ?></td>
		<td><?php echo date("F j, Y", strtotime($row["dob"])); ?></td>
		<td><?php echo $row["email"]; ?></td>
		<td><?php echo $row["status"]; ?></td>
		<td><?php echo $row["type"]; ?></td>
		<td><?php echo date("F j, Y g:i:s", strtotime($row["date_created"])); ?></td>
		<td><?php echo date("F j, Y g:i:s", strtotime($row["date_updated"])); ?></td>
		<td><a href='form.php?id=<?php echo $row["id"]; ?>'>EDIT</a></td>
	</tr>
	<?php
	  }
	}

	$conn->close();
	?>
</table><br/>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]). (!empty($_GET['id']) ? '?id=' . intval($_GET['id']) : ''); ?>" method="POST">
    
	<h2>User Information</h2>
		First Name: <input type="text" value="<?php echo $firstname;?>" name="firstname" required><br/>
		Middle Name: <input type="text" value="<?php echo $middlename;?>" name="middlename" ><br/>
		Last Name: <input type="text" value="<?php echo $lastname;?>" name="lastname" required><br/>
		Gender: <select name="gender" required>
					<option value="" selected>Please select one</option>
					<option value="Male">Male</option>
					<option value="Female">Female</option>
				</select><br/>
		Date of Birth: <input type="date" value = "<?php echo $dob;?>" name="dob" required><br/>
    <h2>Account Information</h2>
        Username: <input type="text" value="<?php echo $username;?>" name="username" required><br/>
	    Password: <input type="password" name="password" required><br/>
        Email: <input type="text" value="<?php echo $email;?>" name="email" required><br/>
		Status: <input type="number" value=1 name="status" max = "11" min = "0" required><br/>
		Type: <input type="number" value=2 name="type" max = "11" min = "0" required><br/><br/>
	<input type="submit" />
</form>