<?php
/**
 * This records the last query made. Any new query will override this.
 */

 $ignored = AC()->ignored;
 $query   = AC()->response['query']->query;

foreach($query as $key => $value){
	if(!in_array($key,  $ignored)){
		if(is_array($value)){
			foreach(AC_Helper::convert_bracket_syntax($value, $key) as $fields){
				if (!AC_Helper::substr_in_array(AC()->ignored, $fields['key']) ) {
					echo "<input type=\"hidden\" name=\"" . $fields['key'] ."\" value=\"" . $fields['value'] . "\">";
				}
			}
		}

		else {
			echo "<input type=\"hidden\" name=\"" . $key . "\" value=\"" . $value . "\">";
		}
	}
}