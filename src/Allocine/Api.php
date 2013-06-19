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

class Api
{
    /**
     * @var string
     */
    protected $provider = 'http://api.allocine.fr/rest/v3';

    /**
     * @var string
     */
    protected $partner_key;

    /**
     * @var string
     */
    protected $secret_key;

    /**
     * @var string
     */
    //protected $user_agent = 'Dalvik/1.2.0 (Linux; U; Android 2.2.2; Huawei U8800-51 Build/HWU8800B635)';
    protected $user_agent = 'Dalvik/1.6.0 (Linux; U; Android 4.2.2; Nexus 4 Build/JDQ39E)';

    /**
     * 
     * @param string $partner_key
     * @param string $secret_key
     */
    public function __construct($partner_key, $secret_key)
    {
        $this->partner_key = $partner_key;
        $this->secret_key = $secret_key;
    }

    /**
     * Send request
     * 
     * @param  string $method
     * @param  array $params
     * @return string
     */
    public function call($method, array $params)
    {
        $url = $this->provider . '/' . $method;
        
        $params['partner'] = $this->partner_key;
        $params['format'] = 'json';

        ksort($params);

        $params['sed'] = date('Ymd');

        $query = str_replace('%2B', '+', http_build_query($params));

        $sig = urlencode(
            base64_encode(
                sha1($this->secret_key . $query, true)
            )
        );

        $url .= '?' . $query . '&sig=' . $sig;

        $ip = rand(0, 255) . '.' . rand(0, 255) . '.' . rand(0, 255) . '.' . rand(0, 255);

        // do the request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'REMOTE_ADDR: ' . $ip, 
            'HTTP_X_FORWARDED_FOR: ' . $ip
        ));
        $response = curl_exec($ch);
        curl_close($ch);
        echo $response . PHP_EOL;
        $data = json_decode($response, true);
        
        unset($response);

        if (empty($data)) {
            return null;
        }

        return $data;
    }

    /**
     * Search
     * 
     * @param  string $query
     * @param  array $filters
     * @return array
     */
    public function search($query, $filters = null)
    {
        $params = array(
            'q' => $query
        );

        if (!empty($filters)) {
            $params['filter'] = implode(',', (array)$filters);
        }

        return $this->call('search', $params);
    }
}