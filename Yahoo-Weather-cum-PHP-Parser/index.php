<html>
<head>


<script>
function check() {
	var file_check = document.getElementById("location_name").value;
	file_check.replace(/^\s+/, "");
	
	var file_name_location_type = document.forms[0].location_type.value;
	
	if(file_check =='' || file_check==null ||file_check==undefined){
		alert("Please enter a location");
		return false;
		}
	
	var text_type = /^[a-zA-Z0-9_\-\'\s\.\,]+$/;
	var text_length = /^\d{5}$/;
	
	if((file_name_location_type=="Zip_Code") && (!text_length.test(file_check))){
		alert("Please enter only 5 digit Zip Code");
		return false;
		}
	
	var s = text_type.test(file_check);
	
	if((file_name_location_type=="City") && (!s)){
		alert("The City name is invalid.");
		return false;
		}
		
		return true;
}
</script>
</head>
<body>



<table border="2">
<caption><h1>Weather Search</h1></caption>
<tr>
	<td><form action="" id="weatherform" method="GET" onsubmit="return check()">
		Location:<input type="text" name="location" id="location_name"><br/><br/>
		Location Type:<select name="location_type">
		<option value="City" selected>City</option>
		<option value="Zip_Code">Zip Code</option>
		</select>
		<br/>
		Temperature Unit:<input type="radio" name="temperature" value="fahrenheit" checked="true">Fahrenheit
						 <input type="radio" name="temperature" value="celcius">Celcius<br/>
		<input type="submit" name="submit_button" value="Submit" />
		</form>
	</td>
</table>
</body>
</html>

<?php

if(isset($_GET["submit_button"])){
//insert the code to give a user understandable message if the form fields were kept empty
$location=$_GET["location"];
//echo $location;
$location_type=$_GET["location_type"];
//echo $location_type;
$temperature=$_GET["temperature"];
//echo $temperature;
$isSkip=false;
$no_of_result=0;	//variable to store the count of the number of returned results

if(($location_type=="Zip_Code")&&(strlen($location)!=5))
	echo "Invalid Zip Code. Please enter the correct Zip Code";
	
else{
$url=null;
	
function rss($arg,$temperature,$no_of_result){
$isSkip = false;
	if($temperature=="celcius")				
		$weather_url = "http://weather.yahooapis.com/forecastrss?w=".$arg."&u=c";
	else	
		$weather_url = "http://weather.yahooapis.com/forecastrss?w=".$arg."&u=f";
	//echo $weather_url;
	
	$weather_rss = simplexml_load_file(urlencode($weather_url));		//weather_rss stores the xml file received from the rss feed
	//print_r($weather_rss);
	
	
	//$content = simplexml_load_string((string)$weather_rss, null, LIBXML_NOCDATA);
	
	//$weather_rssStr = (string)($weather_rss->channel->item->description);
	//print_r($weather_rssStr);
	
	
	//echo $no_of_result;
	
	$yahoo = $weather_rss->channel->item;
	$latitude = $yahoo->xpath('geo:lat');
	//echo $latitude[0];
	$longitude = $yahoo->xpath('geo:long');
	$link_details = $weather_rss->channel->link;
	//echo $link_details;
	
	$weather_rss->registerXPathNamespace("yweather","http://xml.weather.yahoo.com/ns/rss/1.0"); //understand what namespaces do
	$weather_rss->registerXPathNamespace("geo","http://www.w3.org/2003/01/geo/wgs84_pos#");
	
	$yahoo_city = $weather_rss->channel->xpath("yweather:location");
	//echo $yahoo_city[0]['city'];
	$yahoo_region = $weather_rss->channel->xpath("yweather:location");
	//echo $yahoo_region[0]['region'];
	$yahoo_country = $weather_rss->channel->xpath("yweather:location");
	//echo $yahoo_country[0]['country'];
	$yahoo_temperature = $weather_rss->channel->item->xpath("yweather:condition");
	//echo $yahoo_temperature[0]['text'];
	$yahoo_temp_units = $weather_rss->channel->xpath("yweather:units");
	//echo $yahoo_temp_units[0]['temperature'];
	$yahoo_temp_value = $weather_rss->channel->item->xpath("yweather:condition");
	//echo $yahoo_temp_value[0]['temp'];
	$yahoo_desc = $weather_rss->channel->item->description;
	//print_r($yahoo_description);
	//echo "asds";
	$tp = $weather_rss->channel->item;
	//print_r($tp);
	
	//the following code is for retrieving the img src value from the description tag of CDATA in the received XML file
	$htmlParser = new DOMDocument();
	@$htmlParser->loadHTML($yahoo_desc);
	$html = simplexml_import_dom($htmlParser);
	$href = $html->body->img[0]['src'];
	//echo $href;

	if(empty($yahoo_temp_value)||empty($yahoo_temp_units)||empty($yahoo_temperature)){
		$no_of_result--;
		$isSkip = true;
	}
	else{
	@$yahoos = array($latitude[0], $longitude[0], $link_details, $yahoo_city[0]['city'], $yahoo_region[0]['region'], $yahoo_country[0]['country'], $yahoo_temperature[0]['text'], $yahoo_temp_units[0]['temperature'], $yahoo_temp_value[0]['temp'], $href);
	
	for($i=0;$i<count($yahoos);$i++){
		if($yahoos[$i]=="")
			$yahoos[$i]="N/A";
	}
	//$pattern = "/*/";
	/*preg_match($pattern,$yahoo_description, $matches);
	print_r($matches);*/
	
	//$no_of_result++;
	
	$a=@$yahoo_temperature[0]['text'];
	echo		"<tr><td><a href='".$weather_url."' target=_blank><img src = '".$href."' alt='".$a."' title='".$a."'></td>";
	echo		"<td>".$yahoos[6]." ".$yahoos[8]." ".$yahoos[7]."</td>";
	echo		"<td>".$yahoos[3]."</td>";
	echo		"<td>".$yahoos[4]."</td>";
	echo		"<td>".$yahoos[5]."</td>";
	echo		"<td>".$yahoos[0]."</td>";
	echo		"<td>".$yahoos[1]."</td>";
	echo		"<td><a href=".$yahoos[2]." target=_blank>Details</td>";
	
	$isSkip = false;	

}
return $isSkip;
}
//NOT WORKING !!


	if($location_type=="Zip_Code") {
		$url="http://where.yahooapis.com/v1/concordance/usps/".$_GET["location"]."?appid=XMF4YLfV34HCGTQDmoBuQz2YSMqPn4CI4ldcnSExM9bXlPBPtGv6iNk7g04oIyQJNKJObopF2CE-";
		@$xmlresponse = simplexml_load_file(urlencode($url));
		//print_r($xmlresponse);
		@$items = $xmlresponse->woeid;
		//echo $items;
		echo "<table border=\"1\">";
		echo 	"<tr>";
		echo		"<th>Weather</th><th>Temperature</th><th>City</th><th>Region</th><th>Country</th><th>Latitude</th><th>Longitude</th><th>Link to Details</th>";
		echo	"</tr>";
	
		$result=1;
		$skip = rss($items,$temperature,$no_of_result);
			if($skip)
				$result--;
				echo "<caption>".$result." result(s) for City ".$location."</caption>";
		echo 	"</tr>";
		echo "</table>";
		//$regexp = "/^$/";
	
	
	
	}

else {
	$url="http://where.yahooapis.com/v1/places\$and(.q('.$location.'),.type(7));start=0;count=5?appid=XMF4YLfV34HCGTQDmoBuQz2YSMqPn4CI4ldcnSExM9bXlPBPtGv6iNk7g04oIyQJNKJObopF2CE-";
	$xmlresponse = simplexml_load_file(urlencode($url));		//xmlresponse contains the tags, one of which has the woeid tag and corresponding value
	//print_r($xmlresponse);
	
	if(!$xmlresponse)
		echo "Zero results found!";
	else{	
		foreach($xmlresponse->children() as $place) {
			$items[] = $place->woeid;
			//print_r($items);
		}
		//echo count($items);;
	
		//$no_of_result=count($items);
		
		echo "<table border=\"1\">";
		//echo "<caption>".$no_of_result." result(s) for City ".$location."</caption>";
		echo 	"<tr>";
		echo		"<th>Weather</th><th>Temperature</th><th>City</th><th>Region</th><th>Country</th><th>Latitude</th><th>Longitude</th><th>Link to Details</th>";
		echo	"</tr>";

		//$no_of_result=0;
		$skip = false;
		$no_of_result = count($items);
		$result = count($items);
		for($i=0;$i<count($items);$i++){
			$skip = rss($items[$i],$temperature,$no_of_result);
			if($skip==true)
				$result--;
			
		}
		
		echo "<caption>".$result." result(s) for City ".$location."</caption>";
		
		
	
		echo 	"</tr>";
		echo "</table>";
	}
}
}
}
?>