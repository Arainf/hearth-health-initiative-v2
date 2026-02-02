


<?php


function dropdown_rs($rs,$value_display=array(),$name="",$selected_value="",$select_text="",$other_attributes="")
{
    $selectopt_tmp = ($selected_value==0 || $selected_value=='') ? "selected=\"selected\"" : '';
    $output = "";
    $output .= "<select name=\"$name\" id=\"$name\" $other_attributes >\r\n";
    if($select_text<>'') {
        $output .= "    <option value=\"\" $selectopt_tmp>- $select_text -</option>\r\n";
    }
    if ($rs)
    {
        $str_value = $value_display['value'];

        foreach ($rs as $row)
        {
            $select_option = "";
            if ($selected_value > 0 || $selected_value != '') {

                if ($row->$str_value== $selected_value) {
                    $select_option = "selected=\"selected\"";
                }elseif (strcmp(strtoupper($row->$str_value),strtoupper($selected_value))==0) {
                    $select_option = "selected=\"selected\"";
                }
            }
            $output .= "    <option value=\"".htmlentities($row->$str_value)."\" $select_option >".$row->$str_display."</option>\r\n";
            $i++;
        }
    }
    $output .= "</select>\r\n";
    return $output;
}


function dropdown_arr($options=array(), $values=array(), $name="", $selected_value=NULL, $select_text="", $other_attributes="")
{
    $selectopt_tmp = ($selected_value==0 || $selected_value=='') ? "selected=\"selected\"" : '';
    $output = "";
    $output .= "<select name=\"$name\" id=\"$name\" $other_attributes >\r\n";
    $output .= "    <option value=\"\" $selectopt_tmp>$select_text</option>\r\n";

    if ($values)
    {
        $i = 0;
        foreach ($values as $value)
        {
            $select_option = "";
            if ($selected_value > 0) {
                if ($values[$i] == $selected_value) {
                    $select_option = "selected=\"selected\"";
                }
            }
            $output .= "    <option value=\"$values[$i]\" $select_option >$options[$i]</option>\r\n";
            $i++;
        }
    }
    $output .= "</select>\r\n";
    return $output;
}
