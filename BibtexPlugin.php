<?php
/**
 * BibtexPlugin
 *
 * Provides ability to export items in Bibtex
 *
 * @copyright Copyright 2017-2020 Limonade and Co
 * @author Franck Dupont <technique@limonadeandco.fr>
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package BibtexPlugin
 */

define('BIBTEXT_PLUGIN_DIR', dirname(__FILE__));


// Include Bibtex PHP Library
$path  = BIBTEXT_PLUGIN_DIR . '/libraries/bibtex/';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);
require_once 'Structures/BibTex.php';


/**
 * The Bibtex plugin.
 * @package Omeka\Plugins\Bibtex
 */
class BibtexPlugin extends Omeka_Plugin_AbstractPlugin
{

    /**
     * @var array Hooks for the plugin.
     */
    protected $_hooks = array();


    /**
     * @var array Filters for the plugin.
     */
    protected $_filters = array(
                              'action_contexts',
                              'response_contexts',
    );


    /**
     * Add "Bibtex" to available export links
     */
    public function filterActionContexts($contexts, $args)
    {
        if($args['controller'] instanceof ItemsController) {
            $contexts['show'][] = 'bibtex';
        }
        return $contexts;
    }


    /**
     * Fires Bibtex response
     */
    public function filterResponseContexts($contexts)
    {
        $contexts['bibtex'] = array('suffix' => 'bibtex', 'headers' => array('Content-Type' => 'text/plain'));
        return $contexts;

    }
}
