<?php

namespace Thomazot\Core;

class Curl
{
    public function request( $method, $path, $port, $parameters=array())
    {
        $conn = curl_init();
        curl_setopt( $conn, CURLOPT_URL, $path);
        curl_setopt( $conn, CURLOPT_TIMEOUT, 30);
        curl_setopt( $conn, CURLOPT_PORT, $port);
        curl_setopt( $conn, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt( $conn, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt( $conn, CURLOPT_FORBID_REUSE, 1);

        if(is_array($parameters) && count($parameters) > 0) {
            curl_setopt($conn, CURLOPT_POSTFIELDS, json_encode($parameters));
        } else {
            curl_setopt($conn, CURLOPT_POSTFIELDS, null);
        }

        $data = null;
        $response = curl_exec($conn);

        if($response !== false) {
            $data = json_decode($response, true);
            if(!$data) {
                $data = array('error' => $response, "code" => curl_getinfo($conn, CURLINFO_HTTP_CODE));
            }
        }

        curl_close($conn);

        if(isset($data['error'])) {
            throw new \Exception($data['error']);
        }

        if(empty($data)) {
            throw new \Exception("Curl Could not reach the server: $path");
        }

        return $data;

    }

    /**
     * Adicionado classe de consulta simples de acordo com a classe originária:
     *
     * @param  string $url  $url da busca
     * @param  array  $post Dados via POST
     * @param  array  $get  Dados via GET
     * @return string
     */

    public function simple($url, $post=array(), $get=array())
    {
        $url = explode('?', $url, 2);

        if( count($url) === 2 ){

            $temp_get = array();
            parse_str($url[1], $temp_get);
            $get = array_merge($get, $temp_get);

        }

        //die($url[0]."?".http_build_query($get));

        $ch = curl_init( $url[0] . "?" . http_build_query($get) );

        curl_setopt ($ch, CURLOPT_POST, 1);
        curl_setopt ($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec ($ch);

        curl_close($ch);

        return $response;
    }
}