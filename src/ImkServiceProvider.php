<?php

namespace TBC\IMK\WEB;

use Exception;
use TBC\IMK\WEB\Input;

class ImkServiceProvider {

    private $api_url;
    private $api_key;
    private $api_user;
    private $api_group;
    private $client;
    private $google_key;
    private $ws_key;
    private $weather_key;
    private $great_school_url = 'http://api.greatschools.org/schools';
    private $great_school_key;
    private $yelp_key;

    function setApiYelpKey($key) {
        $this->yelp_key = $key;
        return $this;
    }

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

    function setApiWSKey($key) {
        $this->ws_key = $key;
        return $this;
    }

    function setApiWeatherKey($key) {
        $this->weather_key = $key;
        return $this;
    }

    function setApiGreatSchoolKey($key) {
        $this->great_school_key = $key;
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

    function getFeaturedProperties( $params = [] ) {
        $data['orgId'] = $this->api_group;
        $data['userId'] = $this->api_user;
        $data['type'] = 'photo';

        if( count( $params ) ) {
        	$data = array_merge($data, $params);
        }

        return $this->client->request('POST', 'api/getFeaturedProperties', ['form_params' => $data]);
    }

    function getAgents($fetchFor = 'agent') {
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
        return $this->client->request('POST', 'api/getUpcomingProperties', ['json' => $data], ['withSuccess' => true]);
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
                "key" => $this->great_school_key,
                "limit" => 10,
                'address' => $street,
                'city' => $city,
                "state" => $state,
                "radius" => "30",
                "schoolType" => "public-private"
            ];

            $url = $this->great_school_url . '/nearby?' . http_build_query($config);

            $data = $this->client->request("GET", $url, [], ["raw" => true]);

            $arryaData = (array) simplexml_load_string($data);

            if (isset($arryaData['faultString'])) {
                throw new Exception(json_encode($arryaData));
            }

            if ($arryaData) {
                return $arryaData['school'];
            }
        } catch (Exception $e) {
            throw $e;
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
            return [];
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

            $data = $this->client->request("Get", $url, [], ["raw" => true]);
            $data = json_decode($data);
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

    private function walkScore($address, $lat, $lng) {
        try {
            $config = [
                'format' => 'json',
                'address' => $address,
                'lat' => $lat,
                'lon' => $lng,
                'transit' => 1,
                'bike' => 1,
                'wsapikey' => $this->ws_key
            ];

            $queryStr = '';
            if (count($config)) {
                $queryStr = "?" . http_build_query($config);
            }

            $url = 'http://api.walkscore.com/score' . $queryStr;
            $data = $this->client->request("Get", $url, [], ['raw' => true]);
            return $data;
        } catch (Exception $e) {
            return [];
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
                'APPID' => $this->weather_key
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

    function getNMediaKit($params = null) {
        try {
            $queryStr = '';
            if (count($params)) {
                $queryStr = "?" . http_build_query($params);
            }

            $mediaUrl = "api/v1/market-reports/" . $this->user . "/" . $this->group . $queryStr;
            return $this->client->request('GET', $mediaUrl);
        } catch (Exception $e) {
            return [];
        }
    }

    private function yelp_request($address) {
        try {
            $unsigned_url = "https://api.yelp.com/v3/businesses/" . $address;

            $request_headers = array();
            $request_headers[] = sprintf('Authorization: Bearer %s', $this->yelp_key);

            $data = $this->client->request("Get", $unsigned_url, [], ["headers" => $request_headers, "withSuccess" => true]);
            return $data;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    function yelp_search($term, $location) {

        try {
            if (!$term) {
                throw new Exception("Please find term (banks, schools, food and restaurants etc.), this field is required!");
            }
            $url_params = ['term' => $term, 'location' => $location, "limit" => 20];
            $search_path = "search?" . http_build_query($url_params);

            return $this->yelp_request($search_path);
        } catch (Exception $e) {
            print_r($e->getMessage());
            return [];
        }
    }

    function getNearBy() {
        $nearBy = $this->yelp_search(Input::get('term'), Input::get('full_address'));
        if ($nearBy) {
            return json_encode($nearBy);
        } else {
            Helper::setHeader(400);
            return json_encode(["success" => "false"]);
        }
    }

    function getWiki() {
        $walkScore = $this->walkScore(Input::get('full_address'), Input::get('location')['lat'], Input::get('location')['lng']);
        if ($walkScore) {
            return json_encode($walkScore);
        } else {
            Helper::setHeader(400);
            return json_encode(["success" => "false"]);
        }
    }

    function getSchools() {
        try {
            $schools = $this->greatSchool(Input::get('full_address'), Input::get('city'), Input::get('state'));

            if ($schools) {
                return json_encode($schools);
            } else {
                throw new Exception("Invalid Data");
            }
        } catch (Exception $ex) {
            Helper::setHeader(400);
            echo json_encode(["success" => "false", "message" => $ex->getMessage()]);
        }
    }

    function getWalkScore() {
        $walkScore = $this->walkScore(Input::get('full_address'), Input::get('location')['lat'], Input::get('location')['lng']);
        if ($walkScore) {
            return ($walkScore);
        } else {
            Helper::setHeader(400);
            return json_encode(["success" => "false"]);
        }
    }

}

?>