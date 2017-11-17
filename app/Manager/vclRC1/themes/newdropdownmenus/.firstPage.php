<?php

////////////////////////////////////////////////////////////////////////////////
///
/// \fn firstPageHeader()
///
/// \brief Builds the html for the first page header
///
////////////////////////////////////////////////////////////////////////////////

function firstPageHeader () {

    echo "<!DOCTYPE html>\n";
    echo "<html lang=\"en\">\n";
    echo "<head>\n";
    echo "  <title>Bootstrap Example</title>\n";
    echo "  <meta charset=\"utf-8\">\n";
    echo "  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">\n";
    echo "  <link rel=\"stylesheet\" href=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css\">\n";
    echo "  <script src=\"https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js\"></script>\n";
    echo "  <script src=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js\"></script>\n";
    echo "  <link rel=\"stylesheet\" href=\"themes/newdropdownmenus/css/firstPage.css\">\n";
    echo "</head>\n";

    echo "<body>\n";

    // echo "<meta charset=\"utf-8\" />\n";
    // echo "<meta http-equiv=\"Content-type\" content=\"text/html; charset=utf-8\" />\n";
    // echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\" />\n";
    //
    // echo "<!-- Latest compiled and minified CSS -->\n";
    // echo "<link rel=\"stylesheet\" href=\"//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/\n";
    // echo "bootstrap.min.css\">\n";
    //
    // echo "<!-- Optional theme -->\n";
    // echo "<link rel=\"stylesheet\" href=\"//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/\n";
    // echo "bootstrap-theme.min.css\">\n";


    echo "  <h1>Hello, world!</h1>\n";


}


////////////////////////////////////////////////////////////////////////////////
///
/// \fn bootDropDown()
///
/// \brief Builds a sample dropdown button
///
////////////////////////////////////////////////////////////////////////////////

// function bootDropDown($name, $dataArr, $domid="", $extra="", $selectedid=-1,
//                          $skip=0, $multiple=0) {
//
//     $h = '';
//
//     print "name: ".$name."<br />\n";				//&& include all until next //&&
// 	print "dataArr: \n";
// 		print_r($dataArr);
// 	print "<br />\n";
// 	print "domid: ".$domid."<br />\n";
// 	print "extra: ".$extra."<br />\n";
// 	print "selectedid: ".$selectedid."<br />\n";
// 	print "skip: ".$skip."<br />\n";
// 	print "multiple: ".$multiple."<br />\n";		//&&
//
//     if(! empty($domid))
// 		$domid = "id=\"$domid\"";
// 	if($multiple)
// 		$multiple = "multiple";
// 	else
// 		$multiple = "";
// 	if($name != '')
// 		$h .= "      <select class=\"selectpicker\" name=$name $multiple $domid $extra>\n";
// 	else
// 		$h .= "      <select class=\"selectpicker\" $multiple $domid $extra>\n";
// 	foreach(array_keys($dataArr) as $id) {
// 		if(($dataArr[$id] != 0 && empty($dataArr[$id])))
// 			continue;
// 		if($id == $selectedid)
// 		   $h .= "        <option value=\"$id\" selected=\"selected\">";
// 		else
// 		   $h .= "        <option value=\"$id\">";
// 		if(is_array($dataArr[$id])) {
// 			if(array_key_exists('prettyname', $dataArr[$id]))
// 				$h .= $dataArr[$id]['prettyname'] . "</option>\n";
// 			elseif(array_key_exists('name', $dataArr[$id]))
// 				$h .= $dataArr[$id]['name'] . "</option>\n";
// 			elseif(array_key_exists('hostname', $dataArr[$id]))
// 				$h .= $dataArr[$id]['hostname'] . "</option>\n";
// 		}
// 		else
// 			$h .= $dataArr[$id] . "</option>\n";
// 	}
// 	$h .= "      </select>\n";
// 	print $h;
//
// }

////////////////////////////////////////////////////////////////////////////////
///
/// \fn bootDropDownCopy()
///
/// \brief Builds a sample dropdown button
///
////////////////////////////////////////////////////////////////////////////////

function bootDropDownCopy() {

    echo "<div class=\"dropdown\">\n";
    echo "  <button class=\"btn btn-primary dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\">\n";
    echo "      Example Dropdown\n";
    echo "      <span class=\"caret\"></span>\n";
    echo "  </button>\n";

    echo "  <ul class=\"dropdown-menu\">\n";
    echo "      <li><a href=\"#\">HTML</a></li>\n";
    echo "      <li><a href=\"#\">CSS</a></li>\n";
    echo "      <li><a href=\"#\">JavaScript</a></li>\n";
    echo "  </ul>\n";
    echo "</div> <!-- dropdown -->\n";

}

////////////////////////////////////////////////////////////////////////////////
///
/// \fn firstPageFooter()
///
/// \brief Builds the html for the first page footer
///
////////////////////////////////////////////////////////////////////////////////

function firstPageFooter () {

    echo "<h1>This is a footer!</h1>\n";
    echo "<br />\n";

    echo "  </body>\n";
    echo "</html>\n";

}


 ?>
