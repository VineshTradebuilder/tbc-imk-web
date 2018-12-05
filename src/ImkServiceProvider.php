<?php
namespace TBC\IMK\WEB;

class ImkServiceProvider{
    private $api_url;
    private $api_key;    
    private $api_user;
    private $api_group;

    function test(){
        echo "test successfully.";
    }
    function setApiUrl( $url ){
        $this->api_url = $url;
    }
    function setApiKey( $key ){
        $this->api_key = $key;
    }
    function setApiUser( $user ){
        $this->api_user = $user;
    }
    function setApiGroup( $group ){
        $this->api_group = $group;
    }
    function printDetail(){
        print_r( [
            $this->api_url, 
            $this->api_key,
            $this->api_user,
            $this->api_group
        ] );
    }
}

?>