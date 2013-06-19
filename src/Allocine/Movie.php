<?php
/**
 * @package Allocine
 * @author Keyser Söze
 * @copyright Copyright (c) 2013 Keyser Söze
 * Displays <a href="http://creativecommons.org/licenses/MIT/deed.fr">MIT</a>
 * @license http://creativecommons.org/licenses/MIT/deed.fr MIT
 */

/**
* @namespace
*/
namespace Allocine;


class Movie extends Api
{
    /**
     * Search movie
     * 
     * @param  string $query
     * @return array
     */
    public function search($query, $filters = null)
    {
        return parent::search($query, 'movie');
    }

    /**
     * Get movie
     * 
     * @param  integer $id
     * @return array
     */
    public function get($id)
    {
        return $this->call('movie', array(
            'code'      => $id,
            'profile'   => 'large',
            'filter'    => 'movie',
            'striptags' => 'synopsis,synopsisshort'
        ));
    }
}