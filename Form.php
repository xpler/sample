<?php

/**
 * BriskCoder
 *
 * NOTICE OF LICENSE
 *
 * @category    Library
 * @package     bcHTML
 * @internal    Xpler Corporation Staff Only
 * @copyright   Copyright (c) 2010 Xpler Corporation. (http://www.xpler.com)
 * @license     http://www.briskcoder.com/license/  proprietary license, All rights reserved.
 */

namespace BriskCoder\Package\Library\bcHTML;


class Form
{

    private $html = NULL;

    public function __construct( $CALLER )
    {
        if ( $CALLER !== 'BriskCoder\Package\Library\bcHTML' ):
            exit( 'DEBUG: forbidden use of internal class class: ' . __CLASS__ );
        endif;
    }

    /**
     * BUTTON TAG TYPE BUTTON
     * <button type="button"></button>
     * @link http://www.w3schools.com/tags/att_button_type.asp W3C Doc
     * @param String $value Value of element
     * @param Array $_attributes HTML global attributes and specific ones related to this tag, ie: $_attributes = array( 'id="your_id"' );
     * @return Void
     */
    public function button( $value, $_attributes = array() )
    {
        $_attributes = !empty( $_attributes ) ? ' ' . implode( ' ', $_attributes ) : NULL;
        $this->html .= '<button type="button"' . $_attributes . '>' . $value . '</button>';
    }

    /**
     * INPUT TAG TYPE CHECKBOX
     * <input type="checkbox"></input> 
     * @link http://www.w3schools.com/tags/att_input_type.asp W3C Doc
     * @param Array $_attributes HTML global attributes and specific ones related to this tag, ie: $_attributes = array( 'id="your_id"' );
     * @return Void
     */
    public function checkbox( $_attributes  )
    {
        $_attributes = !empty( $_attributes ) ? ' ' . implode( ' ', $_attributes ) : NULL;
        $this->html .= '<input type="checkbox"' . $_attributes . '>';
    }

    /**
     * DATALIST TAG
     * <input list="name"><datalist><option></datalist>
     * @link http://www.w3schools.com/tags/tag_datalist.asp W3C Doc
     * @param String $list Input List name reference for datalist <input list="your_name">
     * @param Array $_list_attributes Input List attributes ie: $_list_attributes = array( 'name="your_name"' );
     * @param Array $_datalist_values Detalist options ie: $_datalist_values[] = 'value' 
     * @param Array $_datalist_attributes HTML global attributes and specific ones related to this tag, ie: $_attributes = array( 'id="your_id"' );
     * @return Void
     */
    public function datalist( $list, $_list_attributes, $_datalist_values, $_datalist_attributes )
    {
        $_list_attributes = !empty( $_list_attributes ) ? ' ' . implode( ' ', $_list_attributes ) : NULL;
        $this->html .= '<input list="' . $list . '"' . $_list_attributes . '>';
        $_datalist_attributes = !empty( $_datalist_attributes ) ? ' ' . implode( ' ', $_datalist_attributes ) : NULL;
        $this->html .= '<datalist' . $_datalist_attributes . '>';
        foreach ( $_datalist_values as $val ):
            $this->html .= '<option value="' . $val . '">';
        endforeach;
        $this->html .= '</datalist>';
    }

    /**
     * FIELDSET TAG OPENNING
     * <fieldset><legend></legend>Content</fieldset>
     * @link http://www.w3schools.com/tags/tag_fieldset.asp W3C Doc
     * @param String $legend Fieldset legend
     * @param String $content Fieldset content
     * @param Array $_fieldset_attributes HTML global attributes and specific ones related to this tag, ie: $_attributes = array( 'id="your_id"' );
     * @return Void
     */
    public function fieldset( $legend, $content, $_fieldset_attributes )
    {
        $_fieldset_attributes = !empty( $_fieldset_attributes ) ? ' ' . implode( ' ', $_fieldset_attributes ) : NULL;
        $this->html .= '<fieldset' . $_fieldset_attributes . '>';
        $this->html .= '<legend> ' . $legend . '</legend>';
        $this->html .= $content;
        $this->html .= '</fieldset>';
    }

    /**
     * INPUT TAG TYPE FILE
     * @link http://www.w3schools.com/tags/att_input_type.asp W3C Doc
     * @param Array $_attributes HTML global attributes and specific ones related to this tag, ie: $_attributes = array( 'id="your_id"' );
     * @return Void
     */
    public function file( $_attributes )
    {
        $_attributes = !empty( $_attributes ) ? ' ' . implode( ' ', $_attributes ) : NULL;
        $this->html .= '<input type="file"' . $_attributes . '>';
    }

    /**
     * FORM TAG OPENNING
     * @link http://www.w3schools.com/tags/tag_form.asp W3C Doc
     * @param String $content Fieldset content
     * @param Array $_attributes HTML global attributes and specific ones related to this tag, ie: $_attributes = array( 'id="your_id"' );
     * @return Void
     */
    public function form( $content, $_attributes = array() )
    {
        $_attributes = !empty( $_attributes ) ? ' ' . implode( ' ', $_attributes ) : NULL;
        $this->html .= '<form' . $_attributes . '>' . $content . '</form>';
    }

    /**
     * GET MARKUP
     * @return string
     */
    public function getMarkup()
    {
        $return = $this->html;
        $this->html = NULL;
        return $return;
    }

    /**
     * INPUT TAG TYPE HIDDEN
     * @link http://www.w3schools.com/tags/att_input_type.asp W3C Doc
     * @param Array $_attributes HTML global attributes and specific ones related to this tag, ie: $_attributes = array( 'id="your_id"' );
     * @return Void
     */
    public function hidden( $_attributes )
    {
        $_attributes = !empty( $_attributes ) ? ' ' . implode( ' ', $_attributes ) : NULL;
        $this->html .= '<input type="hidden"' . $_attributes . '>';
    }

    /**
     * KEYGEN TAG
     * <keygen name="security">
     * @link http://www.w3schools.com/tags/tag_keygen.asp WC Doc     
     * @param Array $_attributes HTML global attributes and specific ones related to this tag, ie: $_attributes = array( 'id="your_id"' ); 
     * @return Void
     */
    public function keygen( $_attributes )
    {
        $_attributes = !empty( $_attributes ) ? ' ' . implode( ' ', $_attributes ) : NULL;
        $this->html .= '<keygen' . $_attributes . '>';
    }

    /**
     * LABEL TAG
     * @link http://www.w3schools.com/tags/tag_label.asp W3C Doc
     * @param String $for Binds label to specific element
     * @param String $label Label text
     * @param array $_attributes HTML global attributes and specific ones related to this tag, ie: $_attributes = array( 'id="your_id"' );
     * @return Void
     */
    public function label( $for, $label, $_attributes = array() )
    {
        $_attributes = !empty( $_attributes ) ? ' ' . implode( ' ', $_attributes ) : NULL;
        $this->html .= '<label for="' . $for . '"' . $_attributes . '>' . $label . '</label>';
    }

    /**
     * INPUT TAG TYPE NUMBER
     * @link http://www.w3schools.com/tags/att_input_type.asp W3C Doc
     * @param Array $_attributes HTML global attributes and specific ones related to this tag, ie: $_attributes = array( 'id="your_id"' );
     * @return Void
     */
    public function number( $_attributes )
    {
        $_attributes = !empty( $_attributes ) ? ' ' . implode( ' ', $_attributes ) : NULL;
        $this->html .= '<input type="number"' . $_attributes . '>';
    }

    /**
     * INPUT TAG TYPE PASSWORD
     * @link http://www.w3schools.com/tags/att_input_type.asp W3C Doc
     * @param Array $_attributes HTML global attributes and specific ones related to this tag, ie: $_attributes = array( 'id="your_id"' );
     * @return Void
     */
    public function password( $_attributes )
    {
        $_attributes = !empty( $_attributes ) ? ' ' . implode( ' ', $_attributes ) : NULL;
        $this->html .= '<input type="password"' . $_attributes . '>';
    }

    /**
     * INPUT TAG TYPE RADIO
     * @link http://www.w3schools.com/tags/att_input_type.asp W3C Doc
     * @param Array $_attributes HTML global attributes and specific ones related to this tag, ie: $_attributes = array( 'id="your_id"' );
     * @return Void
     */
    public function radio( $_attributes )
    {
        $_attributes = !empty( $_attributes ) ? ' ' . implode( ' ', $_attributes ) : NULL;
        $this->html .= '<input type="radio"' . $_attributes . '>';
    }

    /**
     * INPUT TAG TYPE RANGE
     * @link http://www.w3schools.com/tags/att_input_type.asp W3C Doc
     * @param Array $_attributes HTML global attributes and specific ones related to this tag, ie: $_attributes = array( 'id="your_id"' );
     * @return Void
     */
    public function range( $_attributes )
    {
        $_attributes = !empty( $_attributes ) ? ' ' . implode( ' ', $_attributes ) : NULL;
        $this->html .= '<input type="range"' . $_attributes . '>';
    }

    /**
     * BUTTON TAG TYPE RESET
     * @link http://www.w3schools.com/tags/att_button_type.asp W3C Doc
     * @param String $value Value of an input element
     * @param Array $_attributes HTML global attributes and specific ones related to this tag, ie: $_attributes = array( 'id="your_id"' );
     * @return Void
     */
    public function reset( $value, $_attributes = array() )
    {
        $_attributes = !empty( $_attributes ) ? ' ' . implode( ' ', $_attributes ) : NULL;
        $this->html .= '<button type="reset"' . $_attributes . '>' . $value . '</button>';
    }

    /**
     * TAG SELECT
     * @link http://www.w3schools.com/tags/tag_select.asp W3C Doc
     * @param Array $_options Select options ie: $_options[value] = 'text' || if setting optGroup then <br>
     * $_options[optGroup_label] = array( value => 'text'), if array value is identified as array type the optgroup is set.
     * @param Array $_select_attributes HTML global attributes and specific ones related to this tag, ie: $_attributes = array( 'id="your_id"' );
     * @param Array $_option_attributes Options & optgroup attributes ie:  $_option_attributes[$_options key] = array('disabled="disabled"', 'label="your_label"') <br>
     * As longs as $_option_attributes has the matching key from $_options it works for optgroup parameters and option parameters.
     * @return Void
     */
    public function select( $_options, $_select_attributes , $_option_attributes )
    {
        $_select_attributes = !empty( $_select_attributes ) ? ' ' . implode( ' ', $_select_attributes ) : NULL;
        $this->html .= '<select' . $_select_attributes . '>';
        foreach ( $_options as $val => $text ):
            if ( (array) $text === $text ):
                $option_attributes = !empty( $_option_attributes[$val] ) ? ' ' . implode( ' ', $_option_attributes[$val] ) : NULL;
                $this->html .= '<optgroup  label="' . $val . '"' . $option_attributes . '>';
                foreach ( $text as $v => $t ):
                    $option_attributes = !empty( $_option_attributes[$v] ) ? ' ' . implode( ' ', $_option_attributes[$v] ) : NULL;
                    $this->html .= '<option value="' . $v . '"' . $option_attributes . '>' . $t . '</option>';
                endforeach;
                $this->html .= '</optgroup>';
                continue;
            endif;
            $option_attributes = !empty( $_option_attributes[$val] ) ? ' ' . implode( ' ', $_option_attributes[$val] ) : NULL;
            $this->html .= '<option value="' . $val . '"' . $option_attributes . '>' . $text . '</option>';
        endforeach;
        $this->html .= '</select>';
    }

    /**
     * BUTTON TAG TYPE SUBMIT
     * @link http://www.w3schools.com/tags/att_button_type.asp W3C Doc
     * @param String $value Value of an input element
     * @param Array $_attributes HTML global attributes and specific ones related to this tag, ie: $_attributes = array( 'id="your_id"' );
     * @return Void
     */
    public function submit( $value, $_attributes )
    {
        $_attributes = !empty( $_attributes ) ? ' ' . implode( ' ', $_attributes ) : NULL;
        $this->html .= '<button type="submit"' . $_attributes . '>' . $value . '</button>';
    }

    /**
     * INPUT TAG TYPE TEXT
     * @link http://www.w3schools.com/tags/att_input_type.asp W3C Doc
     * @param array $_attributes HTML global attributes and specific ones related to this tag, ie: $_attributes = array( 'id="your_id"' );
     * @return Void
     */
    public function text( $_attributes )
    {
        $_attributes = !empty( $_attributes ) ? ' ' . implode( ' ', $_attributes ) : NULL;
        $this->html .= '<input type="text"' . $_attributes . '>';
    }

    /**
     * TEXTAREA TAG
     * @link http://www.w3schools.com/tags/tag_textarea.asp W3C Doc
     * @param String $value Value of an input element
     * @param Array $_attributes HTML global attributes and specific ones related to this tag, ie: $_attributes = array( 'id="your_id"' );
     * @return Void 
     */
    public function textarea( $value, $_attributes  )
    {
        $_attributes = !empty( $_attributes ) ? ' ' . implode( ' ', $_attributes ) : NULL;
        $this->html .= '<textarea' . $_attributes . '>' . $value . '</textarea>';
    }

}
