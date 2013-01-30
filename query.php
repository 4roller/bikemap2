<?
	if($_GET) {
		$get = $_GET['cat'];
		$getArr = explode(',', $_GET['cat']);
        $out = '{';

        foreach( $getArr as $cat ) {
            
    		switch($cat) {
	    		case 'fatal':
		    		$file = file_get_contents('./data/fatal.json');
			    	break;
			    case 'severe':
				    $file = file_get_contents('./data/severe.json');
				    break;
			    default:
				    $file = file_get_contents('./data/fatal.json');
				    break;
		    }
		    $out .= '"' . $cat . '" : ' . $file . ',';
            
        }
        if($out[strlen($out) -1] == ',') {
            $out = substr($out, 0, strlen($out) -1);
        }
        echo $out . '}';
        
   }

?>
