<?php

namespace TBC\IMK\WEB;

use Exception;

class ImkServiceProvider {

    private $api_url;
    private $api_key;
    private $api_user;
    private $api_group;
    private $client;
    private $google_key;

    function setApiUrl($url) {
        $this->api_url = $url;
        return $this;
    }

    function setApiKey($key) {
        $this->api_key = $key;
        return $this;
    }

    function setApiUser($user) {
        $this->api_user = $user;
        return $this;
    }

    function setApiGroup($group) {
        $this->api_group = $group;
        return $this;
    }

    function setApiGoogleKey($key) {
        $this->google_key = $key;
        return $this;
    }

    function init() {
        if (empty($this->api_url)) {
            throw new Exception("IMK API URI is required.");
        }

        if (empty($this->api_user)) {
            throw new Exception("IMK API User is required.");
        }

        if (empty($this->api_group)) {
            throw new Exception("IMK API Group is required.");
        }

        $this->client = new Client([
            "base_uri" => $this->api_url
        ]);

        return $this;
    }

    function getAgentsMyListing() {
        try {
            $params = [
                "orgId" => $this->group,
                "userId" => $this->user
            ];

            $url = '/api/getAgentsMyListing';
            return $this->client->request(
                            'POST', $url, ['form_params' => $params]
            );
        } catch (Exception $e) {
            return [];
        }
    }

    function getClient() {
        return $this->client;
    }

    function getBlogs($params = []) {
        $queryStr = '';
        if (count($params)) {
            $queryStr = "?" . http_build_query($params);
        }

        $blogurl = "api/v1/posts/" . $this->api_user . "/" . $this->api_group . $queryStr;

        return $this->client->request('GET', $blogurl);
    }

    function getRecentPosts() {
        $fp = $this->client->request('GET', "api/v1/posts/recent/" . $this->api_user . "/" . $this->api_group);
        if (isset($fp->posts)) {
            return $fp->posts;
        } else {
            return [];
        }
    }

    function getCategories() {
        return $this->client->request('GET', "api/v1/posts/categories/" . $this->api_user . "/" . $this->api_group
        );
    }

    function getArchives() {
        return $this->client->request('GET', "api/v1/posts/archives/" . $this->api_user . "/" . $this->api_group);
    }

    function getSingleBlog($postId) {
        return $this->client->request('GET', "api/v1/post/" . $postId . "/" . $this->api_user . "/" . $this->api_group);
    }

    function getFeaturedProperties() {
        $data['orgId'] = $this->api_group;
        $data['userId'] = $this->api_user;
        $data['type'] = 'photo';

        return $this->client->request('POST', 'api/getFeaturedProperties', ['form_params' => $data]);
    }

    function getAgents( $fetchFor = 'agent' ) {
        $data = ["fetchFor" => $fetchFor, "orgId" => $this->api_group, "userId" => $this->api_user];
        return $this->client->request('post', "api/readMembers", ['form_params' => $data], ['withSuccess' => true]);
    }

    function getleadership() {
        $data = ["fetchFor" => "leadership", "orgId" => $this->api_group, "userId" => $this->api_user];

        return $this->client->request(
                        'post', "api/readMembers", ['form_params' => $data], ['withSuccess' => true]
        );
    }

    function singleAgent($aId) {
        return $this->client->request(
                        'GET', "api/readMember/" . $aId, [], ['withSuccess' => true]
        );
    }

    function getAgentByLicense($license) {
        try {
            $url = 'api/getAgentInfo/' . $license . '?userOrgId=' . $this->api_group;
            return $this->client->request('GET', $url, [], ['withSuccess' => true]);
        } catch (Exception $e) {
            return [];
        }
    }
    
    function getMyListingProperties($filters) {
        $data['userId'] = $this->user;
        $data['orgId'] = $this->group;
        $data['type'] = 'photo';
        $data['limit'] = $this->limit;
        $data['originatingSystemName'] = 'myListings';
        if (isset($filters['currentPage'])) {
            $data['skip'] = ($filters['currentPage'] - 1 ) * $this->limit;
        } else {
            $data['skip'] = 0;
        }

        $data['filter'] = $filters;
        if (isset($data['filter']['propertySubType']) && isset($data['filter']['propertySubType'][0]) && empty($data['filter']['propertySubType'][0])) {
            unset($data['filter']['propertySubType']);
        }

        if (isset($data['filter']['userId']) && isset($data['filter']['userId'][0]) && empty($data['filter']['userId'][0])) {
            unset($data['filter']['userId']);
        }

        return $this->client->request(
                'POST', 'api/getAllMyListings/properties', ['json' => $data], ['withSuccess' => true]
        );
    }

    function getProperties($filters) {
        $data['userId'] = $this->api_user;
        $data['orgId'] = $this->api_group;
        $data['type'] = 'photo';
        $data['limit'] = $this->limit;
        if (isset($filters['currentPage'])) {
            $data['skip'] = ($filters['currentPage'] - 1 ) * $this->limit;
        } else {
            $data['skip'] = 0;
        }

        $data['filter'] = $filters;
        return $this->client->request('POST', 'api/getAll/properties', ['json' => $data], ['withSuccess' => true]);
    }

    function getOpenHouseData($mlsId) {
        $params = [
            "listingId" => $mlsId,
            "orgId" => $this->api_group,
            "userId" => $this->api_user
        ];
        return $this->client->request('POST', 'api/openHomes', ['json' => $params], ['withSuccess' => true]);
    }

    function getComingSoon($filters = []) {
        $data['userId'] = $this->api_user;
        $data['orgId'] = $this->api_group;
        $data['type'] = 'photo';
        $data['limit'] = $this->limit;

        if (isset($filters['currentPage'])) {
            $data['skip'] = ($filters['currentPage'] - 1 ) * $this->limit;
        } else {
            $data['skip'] = 0;
        }

        $data['filter'] = $filters;
        return $this->client->request('POST', 'api/getUpcomingProperties', ['json' => $data]);
    }

    function getSingleProperty($pId, $mlsId) {
        $params = [
            "_id" => $pId,
            "listingId" => $mlsId,
            "orgId" => $this->api_group,
            "userId" => $this->api_user
        ];
        $fp = $this->client->request(
                'POST', 'api/getSingle/properties', ['json' => $params]
        );
        if (count($fp)) {
            return $fp[0];
        } else {
            return [];
        }
    }

    function getSingleMyListing($pId, $mlsId) {
        $params = [
            "_id" => $pId,
            "listingId" => $mlsId,
            "orgId" => $this->api_group,
            "userId" => $this->api_user
        ];
        $fp = $this->client->request(
                'POST', 'api/getSingleMyListing/properties', ['json' => $params]
        );
        if (count($fp)) {
            return $fp[0];
        } else {
            return [];
        }
    }

    function getSinglePropertyComingsoon($pId, $mlsId) {
        $params = [
            "_id" => $pId,
            "listingId" => $mlsId,
            "orgId" => $this->api_group,
            "userId" => $this->api_user
        ];
        $fp = $this->client->request(
                'POST', 'api/getSingleMyListing/properties', ['json' => $params]
        );
        if (count($fp)) {
            return $fp[0];
        } else {
            return [];
        }
    }

    function greatSchool($street, $city, $state) {
        try {

            if (!$street) {
                throw new Exception("Please provice street or locality.");
            }

            if (!$city) {
                throw new Exception("Please provice city.");
            }

            if (!$state) {
                throw new Exception("Please provice state.");
            }
            $street = explode(",", $street);
            $street = reset($street);
            $config = [
                "key" => Config::get('great_school/key'),
                "limit" => 10,
                'address' => $street,
                'city' => $city,
                "state" => $state,
                "radius" => "30",
                "schoolType" => "public-private"
            ];

            $url = Config::get('great_school/url') . '/nearby?' . http_build_query($config);

            $data = $this->client->request("GET", $url, [], ["raw" => true]);

            $arryaData = (array) simplexml_load_string($data);

            if ($arryaData) {
                return $arryaData['school'];
            }
        } catch (Exception $e) {
            print_r($e->getMessage());
        }
    }

    function wiki($city, $state) {
        try {
            $state = Helper::state_abbr($state, 'name');
            $city = Helper::un_slug($city, true);
            $params = [
                "format" => 'json',
                "action" => "query",
                "prop" => 'extracts',
                "exintro" => "explaintext",
                "titles" => $city . ", " . $state
            ];
            $queryStr = '';
            if (count($params)) {
                $queryStr = "?" . http_build_query($params);
            }

            $url = 'http://en.wikipedia.org/w/api.php' . $queryStr;

            $data = $this->client->request("Get", $url);
            if ($data && Helper::input($data, 'query') && Helper::input($data->query, 'pages')) {
                return reset($data->query->pages);
            } else {
                return [];
            }
        } catch (Exception $e) {
            print_r($e->getMessage());
        }
    }

    /*
      @param $address
     */

    function getNormalizedCity($city) {
        try {
            $fields = [
                "locality", "administrative_area_level_1"
            ];

            $config = [
                'address' => $city,
                'sensor' => false,
                'key' => $this->google_key
            ];

            $queryStr = '';
            if (count($config)) {
                $queryStr = "?" . http_build_query($config);
            }

            $url = "https://maps.googleapis.com/maps/api/geocode/json" . $queryStr;

            $data = $this->client->request("Get", $url);

            $address = [];
            if (Helper::input($data, 'status') == 'OK' && Helper::input($data, 'results') && isset($data->results[0])) {

                foreach ($data->results[0]->address_components as $address_component) {
                    if (Helper::input($address_component, 'types') && isset($address_component->types[0])) {
                        if (in_array($address_component->types[0], $fields)) {
                            switch ($address_component->types[0]) {
                                case 'locality':
                                    $address["city"] = $address_component->long_name;
                                    break;
                                case 'administrative_area_level_1':
                                    $address["state"] = $address_component->long_name;
                                    break;
                                case 'administrative_area_level_2':
                                    $address["address"] = $address_component->long_name;
                                    break;
                                case 'country':
                                    $address["country"] = $address_component->long_name;
                                    break;
                            }
                        }
                    }
                }

                if (Helper::input($data->results[0], 'geometry') && Helper::input($data->results[0]->geometry, 'location')) {
                    $address['location'] = (array) Helper::input($data->results[0]->geometry, 'location');
                }
                if (Helper::input($data->results[0], 'formatted_address')) {
                    $address['full_address'] = $data->results[0]->formatted_address;
                }
            }
            return ($address);
        } catch (Exception $e) {
            print_r($e->getMessage());
        }
    }

    function walkScore($address, $lat, $lng) {
        try {

            $config = [
                'format' => 'json',
                'address' => $address,
                'lat' => $lat,
                'lon' => $lng,
                'transit' => 1,
                'bike' => 1,
                'wsapikey' => Config::get('ws/key')
            ];

            $queryStr = '';
            if (count($config)) {
                $queryStr = "?" . http_build_query($config);
            }

            $url = 'http://api.walkscore.com/score' . $queryStr;
            $data = $this->client->request("Get", $url);
            return $data;
        } catch (Exception $e) {
            print_r($e->getMessage());
        }
    }

    /*
      @param [zip=> 132132, country_code=>"xx" ]]
      @param [geo=>[lat=>xxx,lon=>xxx]]
      @param [address=>"xxxx"]
     */

    function weather($address) {
        try {
            $config = [
                'units' => 'metric',
                'cnt' => 7,
                'APPID' => Config::get('weather/key')
            ];

            if (isset($address['address'])) {
                $config['q'] = $address['address'];
            } else if (isset($address['geo'])) {
                $config['lat'] = $address['geo']['lat'];
                $config['lon'] = $address['geo']['lng'];
            } else if (isset($address['zip'])) {
                if (!isset($address['country_code'])) {
                    throw new Exception("Country code required");
                }
                $config['zip'] = $address['zip'] . "," . $address['country_code'];
            }

            $queryStr = '';
            if (count($config)) {
                $queryStr = "?" . http_build_query($config);
            }

            $url = 'http://api.openweathermap.org/data/2.5/weather' . $queryStr;
            $data = $this->client->request("Get", $url);
            return $data;
        } catch (Exception $e) {
            print_r($e->getMessage());
        }
    }

    private function yelp_request($address) {
        try {
            $unsigned_url = Config::get('yelp/API_HOST') . $address;
            $token = new OAuthToken(Config::get('yelp/TOKEN'), Config::get('yelp/TOKEN_SECRET'));
            $consumer = new OAuthConsumer(Config::get('yelp/CONSUMER_KEY'), Config::get('yelp/CONSUMER_SECRET'));
            $signature_method = new OAuthSignatureMethod_HMAC_SHA1();
            $oauthrequest = OAuthRequest::from_consumer_and_token($consumer, $token, 'GET', $unsigned_url);

            // Sign the request
            $oauthrequest->sign_request($signature_method, $consumer, $token);

            // Get the signed URL
            $signed_url = $oauthrequest->to_url();

            $data = $this->client->request("Get", $signed_url);
            return $data;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    function getNMediaKit($params = null) {
        try {
            $queryStr = '';
            if (count($params)) {
                $queryStr = "?" . http_build_query($params);
            }

            $mediaUrl = "api/v1/market-reports/" . $this->user . "/" . $this->group . $queryStr;
            return $this->client->request('GET', $mediaUrl, [], ['withSuccess' => true]);
        } catch (Exception $e) {
            return [];
        }
    }

    function yelp_search($term, $location) {

        try {
            if (!$term) {
                throw new Exception("Please find term (banks, schools, food and restaurants etc.), this field is required!");
            }
            $url_params = ['term' => $term, 'location' => $location, "limit" => Config::get('yelp/SEARCH_LIMIT')];
            $search_path = "search?" . http_build_query($url_params);

            return $this->yelp_request($search_path);
        } catch (Exception $e) {
            print_r($e->getMessage());
        }
    }

}

?>