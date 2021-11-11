<?php
	$mh = mysqli_init();
        $conn = mysqli_connect("localhost", "XXX", "XXX", "XXX");
	$URL = "https://api.themoviedb.org/3/person/latest?api_key=XXX&language=en-US";
	$ch = curl_init();
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL, $URL);
        $results = curl_exec($ch);

	$id_index = strpos($results, "\"id\":");
	$max_person_id = intval(substr($results, $id_index+5));

	for ($i=0; $i<=$max_person_id; ++$i) {
		$URL = "https://api.themoviedb.org/3/person/".$i."?api_key=XXX&language=en-US";
		curl_setopt($ch, CURLOPT_URL, $URL);
		$results = curl_exec($ch);
		if (substr($results, 0, 16) === "{\"success\":false") {
			echo "Name with id ".$i." has been deleted.\n";
			continue;
		}
		$id_index =  strpos($results, "\"id\":");
		$person_id = intval(substr($results, $id_index+5));
		$person_name_index = strpos($results, "\"name\":\"") + 8;
		$person_name = "";
		while ($results[$person_name_index] !== "\"") {
			$person_name = $person_name.($results[$person_name_index]);
			++$person_name_index;
		}

		$sql = 'insert person_data  (person_name, person_id) values ("'.$person_name.'", '.$person_id.')';
		$retval = mysqli_query($conn, $sql);
		if ($retval == false) {
			echo "Error inserting data for person id ".$i.". \n";
		}
	}

?>
