<?php
	$con = mysqli_connect('localhost','wasabisushi','eyelle8888',"wasabisushi");

	if (!$con) {
		die('Could not connect:'.mysqli_error($con));
	}
	$sql = "SELECT * FROM oc_order WHERE isPrinted=false";
	$result = $con->query($sql);
	
	if (isset($result)) {
		$row_show = array();
		while ( $rowww = $result->fetch_assoc() ) {
			$row_show[] = $rowww;
		}
	    $final_show = array();
	    $final_shows = array();
		foreach ($row_show as $row) {
			$order_id = $row['order_id'];
			$sql2 = "SELECT * FROM oc_order_product WHERE order_id=".$order_id;
			$result2 = $con->query($sql2);
			while ($rowww = $result2->fetch_assoc()) {
				$order_product[] = $rowww;
			}
			$final_show[] = $row;
			$final_show[] = $order_product;
			$final_shows[] = $final_show;
			unset($order_product);
			unset($final_show);

		}

		print json_encode($final_shows[0]);

		$order_id_ready_to_change = $final_shows[0][0]['order_id'];

		//mysqli_query($con, "UPDATE oc_order SET isPrinted=true WHERE order_id=".$order_id_ready_to_change);

		$sql3 = "UPDATE 'oc_order' SET isPrinted='1' WHERE order_id=".$order_id_ready_to_change;
		$con->query($sql3);
	}


?>