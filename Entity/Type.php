<?php
/**
 * @copyright 2012 Instaclick Inc.
 */

namespace IC\Bundle\Core\FieldBundle\Entity;

/**
 * The Types of Field Entity
 *
 * @author Guilherme Blanco <guilhermeblanco@gmail.com>
 * @author Yuan Xie <yuanxie@live.ca>
 * @author Oleksii Strutsynskyi <oleksiis@gmail.com>
 */
class Type
{
    /**
     * Field render type for text box
     */
    const TEXT = 'text';

    /**
     * Field render type for text area
     */
    const TEXTAREA = 'textarea';

    /**
     * Field render type for radio button
     */
    const RADIO = 'radio';

    /**
     * Field render type for check box
     */
    const CHECKBOX = 'checkbox';

    /**
     * Field render type for drop down menu
     */
    const SELECT = 'select';

    /**
     * Field render type for multiple check boxes
     */
    const CHECKBOX_MULTIPLE = 'checkbox_multiple';

    /**
     * Field render type for list box
     */
    const SELECT_MULTIPLE = 'select_multiple';
}
