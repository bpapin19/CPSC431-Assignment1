<!DOCTYPE html>
<html>
<head>
	<title>Gallery</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css?v=<?php echo time(); ?>">
</head>

<body>

<?php

// Load in variables from form fields
$name = $date = $photographer = $location = $file = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$name = test_input($_POST["name"]);
	$date = test_input($_POST["date"]);
	$photographer = test_input($_POST["photographer"]);
	$location = test_input($_POST["location"]);
	$file = $_FILES["fileToUpload"]["name"];
}

function test_input($data) {
	$data = trim($data);
	$data = stripslashes($data);
   	$data = htmlspecialchars($data);
   	return $data;
}

$dir = 'uploads/';

if (!file_exists($dir)) {
	echo "Creating directory $dir";
	mkdir ($dir, 0744);
}

// If uploaded file does not already exist
if (!(file_exists($dir . $_FILES["fileToUpload"]["name"]))) {

	// Create file photoinfo.txt and write photo information to it
	$photoInfo = fopen("photoInfo.txt", "a");
	fwrite($photoInfo, $name . ", ");
	fwrite($photoInfo, $date . ", ");
	fwrite($photoInfo, $photographer . ", ");
	fwrite($photoInfo, $location . ", ");
	fwrite($photoInfo, $file . ", ");
	fwrite($photoInfo, "|");
	fwrite($photoInfo, "\n");

	// Upload file to uploads directory
	if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $dir . $file)) {
		echo '<div class="display-success">
			  <img src="correct.png" />
			  File successfully uploaded!
			  </div>';
	} else {
	echo '<div class="display-error">
		  <img src="error.png" />
		  Error uploading photo.
		  </div>';
	}
}

// Gallery layout with HTML
echo '
	<div class="featured-image-block-grid">
  		<div class="featured-image-block-grid-header small-10 medium-8 large-7 columns text-center">
    		<h2>Gallery</h2>
    		<p>View all photos</p>
  		</div>
  		<div class="button-container">
  			<form name="dropdown">
  			<label for="attributes">Sort by:</label>

  			<!-- Super ugly code that keeps the value of the dropdown selected after form submit -->

			<select name="dropdown" onchange="this.form.submit()">
			  <option value="order"';
			  echo isset($_GET["dropdown"]) && $_GET["dropdown"] == "order" ? "selected" : "";
			  echo '>Order Added</option>
			  <option value="name" ';
			  echo isset($_GET["dropdown"]) && $_GET["dropdown"] == "name" ? "selected" : "";
			  echo '>Name</option>
			  <option value="date" ';
			  echo isset($_GET["dropdown"]) && $_GET["dropdown"] == "date" ? "selected" : "";
			  echo '>Date</option>
			  <option value="photographer" ';
			  echo isset($_GET["dropdown"]) && $_GET["dropdown"] == "photographer" ? "selected" : "";
			  echo '>Photographer</option>
			  <option value="location" ';
			  echo isset($_GET["dropdown"]) && $_GET["dropdown"] == "location" ? "selected" : "";
			  echo '>Location</option>
			</select>
			</form>

  			<button class="upload-button" onclick=location.href="http://ecs.fullerton.edu/~cs431s8/Assignment1/index.html">Upload Photo</button>
  			</div>
  		<div class="row large-up-4 small-up-2">
';

// Sort by Name function
// bug in this sorting algorithm that puts first image last no matter what, can't figure it out
function sortByName($get_each_photo) {
	for ($i=0; $i < sizeof($get_each_photo) -1; $i++) {
		for ($j=0; $j < sizeof($get_each_photo) -$i -1; $j++) {
			$get_each_field1 = explode(", ", $get_each_photo[$j]);   // get first photo fields
			$get_each_field2 = explode(", ", $get_each_photo[$j+1]); // get second photo fields
			for ($str_index = 0; $str_index < strlen($get_each_field1[1]); $str_index++) {
				if (substr($get_each_field1[0], $str_index) > substr($get_each_field2[0], $str_index)) {
					$temp = $get_each_photo[$j];
					$get_each_photo[$j] = $get_each_photo[$j+1];
					$get_each_photo[$j+1] = $temp;
					break;
				} else if (substr($get_each_field1[0], $str_index) < substr($get_each_field2[0], $str_index)) {
					break;
				}
			}
		}
	}
	return $get_each_photo;
}

// Sort by Date function
function sortByDate($get_each_photo) {
	for ($i=0; $i < sizeof($get_each_photo) -1; $i++) {
		for ($j=0; $j < sizeof($get_each_photo) -$i -1; $j++) {
			$get_each_field1 = explode(", ", $get_each_photo[$j]);   // get first photo fields
			$get_each_field2 = explode(", ", $get_each_photo[$j+1]); // get second photo fields
			for ($str_index = 0; $str_index < strlen($get_each_field1[1]); $str_index++) {
				if (substr($get_each_field1[1], $str_index) > substr($get_each_field2[1], $str_index)) {
					$temp = $get_each_photo[$j];
					$get_each_photo[$j] = $get_each_photo[$j+1];
					$get_each_photo[$j+1] = $temp;
					break;
				} else if (substr($get_each_field1[1], $str_index) < substr($get_each_field2[1], $str_index)) {
					break;
				}
			}
		}
	}
	return $get_each_photo;
}

// Sort by Photographer function
function sortByPhotographer($get_each_photo) {
	for ($i=0; $i < sizeof($get_each_photo) -1; $i++) {
		for ($j=0; $j < sizeof($get_each_photo) -$i -1; $j++) {
			$get_each_field1 = explode(", ", $get_each_photo[$j]);   // get first photo fields
			$get_each_field2 = explode(", ", $get_each_photo[$j+1]); // get second photo fields
			for ($str_index = 0; $str_index < strlen($get_each_field1[2]); $str_index++) {
				if (substr(strtolower($get_each_field1[2]), $str_index) > substr(strtolower($get_each_field2[2]), $str_index)) {
					$temp = $get_each_photo[$j];
					$get_each_photo[$j] = $get_each_photo[$j+1];
					$get_each_photo[$j+1] = $temp;
					break;
				} else if (substr(strtolower($get_each_field1[2]), $str_index) < substr(strtolower($get_each_field2[2]), $str_index)) {
					break;
				}
			}
		}
	}
	return $get_each_photo;
}

// Sort by Location function
function sortByLocation($get_each_photo) {
	for ($i=0; $i < sizeof($get_each_photo) -1; $i++) {
		for ($j=0; $j < sizeof($get_each_photo) -$i -1; $j++) {
			$get_each_field1 = explode(", ", $get_each_photo[$j]);   // get first photo fields
			$get_each_field2 = explode(", ", $get_each_photo[$j+1]); // get second photo fields
			for ($str_index = 0; $str_index < strlen($get_each_field1[4]); $str_index++) {
				if (substr(strtolower($get_each_field1[3]), $str_index) > substr(strtolower($get_each_field2[3]), $str_index)) {
					$temp = $get_each_photo[$j];
					$get_each_photo[$j] = $get_each_photo[$j+1];
					$get_each_photo[$j+1] = $temp;
					break;
				} else if (substr(strtolower($get_each_field1[3]), $str_index) < substr(strtolower($get_each_field2[3]), $str_index)) {
						break;
				}
			}
		}
	}
	return $get_each_photo;
}


// Get each field from txt file
$photoData = file_get_contents("photoInfo.txt");
$get_each_photo = explode("|", $photoData);
for ($i=0; $i < sizeof($get_each_photo); $i++) {
	$get_each_field = explode(", ", $get_each_photo[$i]);
	// Sort contents of $get_each_photo based on which field is selected in dropdown menu
	switch ($_GET["dropdown"]) {
		case 'order':
			$get_each_field = explode(", ", $get_each_photo[$i]);
			break;

		case 'name':
			$get_each_field = explode(", ", sortByName($get_each_photo)[$i]);
			break;

		case 'date':
			$get_each_field = explode(", ", sortByDate($get_each_photo)[$i]);
			break;

		case 'photographer':
			$get_each_field = explode(", ", sortByPhotographer($get_each_photo)[$i]);
			break;

		case 'location':
			$get_each_field = explode(", ", sortByLocation($get_each_photo)[$i]);
			break;
	}
	
	$photoName = $get_each_field[0];
	$photoDate = $get_each_field[1];
	$photoPhotographer = $get_each_field[2];
	$photoLocation = $get_each_field[3];
	$photoFile = $get_each_field[4];

	if ($photoFile != null) {
		// Add div with photo info to page
		echo '
			<div class="featured-image-block column">
	    		<img class="image" src="'.$dir.$photoFile.'"/>
	    		<div class="title-box-title"><b>'.$photoName.'</b></div>
	    		<div class="title-box-title">'.$photoDate.'</div>
	    		<div class="title-box-title">'.$photoPhotographer.'</div>
	    		<div class="title-box-title">'.$photoLocation.'</div>
			</div>
	    ';
	}
}
echo '
	</div>
	</div>
';
?>


</body>

</html>

