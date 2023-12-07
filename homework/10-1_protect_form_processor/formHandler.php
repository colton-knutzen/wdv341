<?php
//reCAPTCHA
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$recaptcha_token = $_POST['recaptcha_token'];

	if (empty($recaptcha_token)) {
		echo "reCAPTCHA validation failed. Please try again.";
		exit;
	}

	$secret_key = "6LczZuUoAAAAAPQHTTBCG91PghDCAMKGmycbUz8k";
	$url = 'https://www.google.com/recaptcha/api/siteverify';
	$data = [
		'secret' => $secret_key,
		'response' => $recaptcha_token
	];

	$options = [
		'http' => [
			'header' => "Content-type: application/x-www-form-urlencoded\r\n",
			'method' => 'POST',
			'content' => http_build_query($data)
		]
	];

	$context = stream_context_create($options);
	$response = file_get_contents($url, false, $context);
	$result = json_decode($response, true);

	if ($result['success'] != true) {
		echo "reCAPTCHA validation failed. Please try again.";
		exit;
	}

	//Form Processing
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		//process info

		//process honeypot first to check validation before processing rest of the inputs
		$favorite_class = $_POST['favorite_class'];
		//echo $favorite_class;

		if (empty($favorite_class)) {
			$first_name = $_POST['first_name'];
			$last_name = $_POST['last_name'];
			$school_name = $_POST['school_name'];
			$email = $_POST['email'];
			$academicStanding = $_POST['academicStanding'];
			$major = $_POST['major'];

			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				if (isset($_POST['programInfo'])) {
					$programInfo = $_POST['programInfo'];
				}
			}

			$commentSection = $_POST['commentSection'];
			/*echo $first_name;
			echo $last_name;
			echo $school_name;
			echo $email;
			echo $academicStanding;
			echo $major;
			echo $programInfo;
			echo $commentSection;*/

			function successMessage()
			{
				global $first_name, $last_name, $email;
				echo "<h2>Thank you " . $first_name . " " . $last_name . ". ";
				echo "A signup confirmation has been sent to " . $email . ". Thank you for your support!</h2>";
			}

			function dataTable()
			{
				echo "<table border='1' style='border-collapse: collapse;'>";
				echo "<tr><th>Field Name</th><th>Value of Field</th></tr>";

				$fieldsToExclude = array("favorite_class", "button", "recaptcha_token");
				$fieldMappings = array("first_name" => "First Name", "last_name" => "Last Name", "school_name" => "School Name", "email" => "Email", "academicStanding" => "Academic Standing", "major" => "Major", "programInfo" => "Program Info", "programAdvisor" => "Program Advisor", "commentSection" => "Comments");

				foreach ($_POST as $key => $value) {
					if (!in_array($key, $fieldsToExclude)) {
						$fieldName = isset($fieldMappings[$key]) ? $fieldMappings[$key] : $key;

						if ($key === "programInfo" || $key === "programAdvisor") {
							$value = "Yes";
						}
						echo '<tr>';
						echo '<td style="padding: 5px;">', $fieldName, '</td>';
						echo '<td style="padding: 5px;">', $value, '</td>';
						echo "</tr>";
					}
				}

				echo "</table>";
				echo "<p>&nbsp;</p>";
			}
		}
		//Honeypot failed, end all processing
		else {
			echo "We're sorry, there was an error. Please try submitting again.";
			exit;
		}
	}
	//Form was submitted not by the Post method 
	else {
		echo "We're sorry, there was an error. Please try submitting again.";
		exit;
	}
}
?>

<!DOCTYPE html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Document</title>
	<style>
		body {
			text-align: center;
		}

		table {
			margin: 0 auto;
		}
	</style>
</head>

<body>

	<?php successMessage(); ?>

	<p style="padding-top: 40px;">Table here to review values inputed</p>
	<?php dataTable(); ?>

</body>

</html>