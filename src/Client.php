<?php

namespace TBC\IMK\WEB;

use Exception;

class Client {

    private $base_uri = "";
    private $timeout = 30;

    function __construct($options) {
        if (isset($options['base_uri']) && is_string($options['base_uri']) && !empty($options['base_uri'])) {
            $this->base_uri = $options['base_uri'];
        } else {
            throw new Exception("Base URI is required");
        }
        if (isset($options['timeout']) && is_string($options['timeout']) && !empty($options['timeout'])) {
            $this->timeout = $options['timeout'];
        }
    }

    function request($method = 'get', $url, $params = [], $options = []) {
        try {
            $parsed_url = parse_url($url);
            if (!isset($parsed_url['scheme'])) {
                $url = $this->base_uri . $url;
            }

            $ch = curl_init($url);
            $headers = array();
            if (isset($options['headers']) && count($options['headers'])) {
                $headers = array_merge($headers, $options['headers']);
            }
            $data_string = '';
            if (strtolower($method) == 'post') {
                if (isset($params['json'])) {
                    $data_string = json_encode($params['json']);
                    array_push($headers, 'Content-Length: ' . strlen($data_string));
                    array_push($headers, 'Content-Type: application/json');
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                } else if (isset($params['form_params'])) {
                    $data_string = http_build_query($params['form_params']);
                    curl_setopt($ch, CURLOPT_POST, 1);
                }

                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            }

            curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $result = curl_exec($ch);

            curl_close($ch);

//            echo $url, print_r( $headers ) , " output => ", print_r($result);

            if ($result) {
                if (isset($options['raw']) && $options['raw'] == true) {
                    return $result;
                } else {
                    if ($output = json_decode($result)) {
                        if (isset($options['withSuccess']) && $options['withSuccess'] == true) {
                            return $output;
                        } else {
                            if (isset($output->success) && $output->success) {
                                return $output->data;
                            } else {
                                throw new Exception("Something went wrong.1");
                            }
                        }
                    } else {
                        throw new Exception("Something went wrong.2");
                    }
                }
            } else {
                return [];
            }
        } catch (Exception $e) {
            return [];
        }
    }

}
