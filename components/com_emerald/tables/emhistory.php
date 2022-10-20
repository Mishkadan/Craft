<?php
/**
 * Emerald by MintJoomla
 * a component for Joomla! 1.7 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class EmeraldTableEmHistory extends JTable
{
    function __construct( &$db ) {
        parent::__construct( '#__emerald_url_history', 'id', $db );
    }
}