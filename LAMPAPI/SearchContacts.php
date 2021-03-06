<?php

	$inData = getRequestInfo();
	
	$searchResults = "";
	$emails = "";
	$phones = "";
	$ID = "";

	$searchCount = 0;

	$conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		$stmt = $conn->prepare("select ID,Name,Phone,Email from Contacts where Name like ? and UserID=?");
		$search = "%" . $inData["search"] . "%";
		$stmt->bind_param("ss", $search, $inData["userId"]);
		$stmt->execute();
		
		$result = $stmt->get_result();
		
		while($row = $result->fetch_assoc())
		{
			//print($row["ID"];)
			if( $searchCount > 0 )
			{
				$searchResults .= ",";
				$emails .= ",";
                                $phones .= ",";
				$ID .= ",";

			}
			$searchCount++;
			$searchResults .=  '"' . $row["Name"] . '"';
			$emails .=  '"' . $row["Email"] .'"';
			$phones .=  '"' . $row["Phone"] .'"';
			$ID .=  '"' . $row["ID"] . '"';
			
		}
		
		if( $searchCount == 0 )
		{
			returnWithError( "No Records Found" );
		}
		else
		{
			//returnWithInfo( $searchResults );
			returnWithInfo( $searchResults, $emails, $phones, $ID);
			//returnWithInfo( $row['firstName'], $row['lastName'], $row['ID'] );
		}
		
		$stmt->close();
		$conn->close();
	}

	function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'), true);
	}

	function sendResultInfoAsJson( $obj )
	{
		header('Content-type: application/json');
		echo $obj;
	}
	
	function returnWithError( $err )
	{
		$retValue = '{"id":0,"firstName":"","lastName":"","error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}
	
	function returnWithInfo( $searchResults, $emails, $phones, $ID)
	{
		$retValue = '{"IDs":[' . $ID . '],"Name":[' . $searchResults . '],"Email":[' . $emails . '],"Phone":[' . $phones . '],"error":""}';
		sendResultInfoAsJson( $retValue );
	}
	
?>
