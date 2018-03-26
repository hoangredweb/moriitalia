<?php
/**
 * @copyright Copyright (C) 2008-2009 redCOMPONENT.com. All rights reserved.
 * @license can be read in this package of software in the file license.txt or
 * read on http://redcomponent.com/license.txt
 * Developed by email@recomponent.com - redCOMPONENT.com
 *
 * redPRODUCTFINDER model
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.helper');

class redPRODUCTFINDERHelperFields
{
    function generateSelectBox($data, $data_row, $name, $id, $selected_item, $object,$extras="")
    {

        $name_attribute = ' name="'.$name.'"';

         $selectbox  = "<select".$name_attribute." ".$extras." class=\"selectbox\">
                       <option value=\"0\">".JTEXT::_("ALL")."</option>";
        if ($object) //True or false
        {
            //Iterate through object
            foreach($data as $row)
            {
                if (!isset($row->$id)) //If is not set
                $row->$id = ""; //Set

                if(is_array($selected_item))
                {
                    if(in_array($row->id,$selected_item))
                    {
                     $selected ="selected='selected'";
                    }else{
                     $selected ="";
                    }
                }else{
                    $selected ="";
                }


              //  $selected = ($row->id == $selected_item) ? " selected=\"selected\"" : "";
                $selectbox .= '<option value="'.$row->id.'"'.$selected.'>'.$row->$data_row.'</option>';
            }
        }
        else
        {
            //Iterate through array
            for($i = 0; $i < count($data); $i++)
            {
                if(is_array($selected_item))
                {
                    if(in_array($data[$i],$selected_item))
                    {
                     $selected ="selected='selected'";
                    }else{
                     $selected ="";
                    }
                }else{
                    $selected ="";
                }

                //$selected = ($data[$i] == $selected_item) ? " selected = \"selected\"" : "";
                $selectbox .= '<option value="'.$data[$i].'"'.$selected.'>'.$data[$i].'</option>';
            }
        }


        $selectbox .= "</select>";

        return $selectbox;
    }
    function generateSelectBox_dependent($data, $data_row, $name, $id, $selected_item, $object,$extras="")
    {
        $name_attribute = ' name="'.$name.'"';
        $selectbox  = "<select".$name_attribute." ".$extras." class=\"selectbox\">
                       <option value=\"0\">".JTEXT::_("ALL")."</option>";

        if ($object) //True or false
        {
            //Iterate through object
            foreach($data as $row)
            {
                if (!isset($row->$id)) //If is not set
                $row->$id = ""; //Set

                $selected = ($row->id == $selected_item) ? "selected" : "";
                $selectbox .= '<option value="'.$row->id.'"'.$selected.'>'.$row->$data_row.'</option>';
            }
        }
        else
        {
            //Iterate through array
            for($i = 0; $i < count($data); $i++)
            {
                $selected = ($data[$i] == $selected_item) ? "selected" : "";
                $selectbox .= '<option value="'.$data[$i].'"'.$selected.'>'.$data[$i].'</option>';
            }
        }


        $selectbox .= "</select>";

        return $selectbox;
    }
    function generateTextfield($name_and_id, $value)
    {
        $textfield  = '<input type="text" name="'.$name_and_id.'" id="'.$name_and_id.'" size="32" maxlength="250" value="'.$value.'" />';

        return $textfield;
    }

    function generateTextarea($name_and_id, $value, $number_of_rows, $number_of_cols)
    {
        $textfield  = '<textarea rows="'.$number_of_rows.'" cols="'.$number_of_cols.'" name="'.$name_and_id.'" id="'.$name_and_id.'">'.$value.'</textarea>';

        return $textfield;
    }

    function generateCheckbox($name, $id, $value,$extras="",$chkselected_item="0")
    {


        if(is_array($chkselected_item))
        {
            if(in_array($value,$chkselected_item))
            {
             $checks ="checked='checked'";
            }else{
             $checks ="";
            }
        }else{
            $checks ="";
        }

        $checkbox  = '<input type="checkbox" name="'.$name.'" id="'.$id.'" '.$checks.' value="'.$value.'" '.$extras.' />';

        return $checkbox;
    }

    function generateSubmit($value)
    {
        $submit  = '<input type="submit" value="'.$value.'" />';

        return $submit;
    }

    function generateButton($js, $value)
    {
        $button  = '<input type="button" value="'.$value.'" '.$js.' />';

        return $button;
    }
}
?>
