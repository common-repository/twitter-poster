<?php
/*
* Copyright (c) <2008> Justin Poliey <jdp34@njit.edu>
*
* Permission is hereby granted, free of charge, to any person
* obtaining a copy of this software and associated documentation
* files (the "Software"), to deal in the Software without
* restriction, including without limitation the rights to use,
* copy, modify, merge, publish, distribute, sublicense, and/or sell
* copies of the Software, and to permit persons to whom the
* Software is furnished to do so, subject to the following
* conditions:
*
* The above copyright notice and this permission notice shall be
* included in all copies or substantial portions of the Software.
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
* EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
* OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
* NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
* HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
* WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
* FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
* OTHER DEALINGS IN THE SOFTWARE.
*/
 
class Twitter {
  /* Username:password format string */
  var $credentials;
  
  /* Contains the last HTTP status code returned */
  var $http_status;
  
  /* Contains the last API call */
  var $last_api_call;
  
  /* Contains the application calling the API */
  var $application_source;
 
  /* Twitter class constructor */
  function Twitter($username, $password, $source=false) {
    $this->credentials = sprintf("%s:%s", $username, $password);
    $this->application_source = $source;
  }
  
  
  function updateStatus($status) {
    $status = urlencode(stripslashes(urldecode($status)));
    $api_call = sprintf("http://twitter.com/statuses/update.xml?status=%s", $status);
    return $this->APICall($api_call, true, true);
  }
  

  function showUser($format, $id, $email = NULL) {
    if ($email == NULL) {
      $api_call = sprintf("http://twitter.com/users/show/%s.%s", $id, $format);
    }
    else {
      $api_call = sprintf("http://twitter.com/users/show.xml?email=%s", $email);
    }
    return $this->APICall($api_call, true);
  }
  
  function createFriendship($format, $id) {
      $api_call = sprintf("http://twitter.com/friendships/create/%s.%s", $id, $format);
      return $this->APICall($api_call, true, true);
  }
    
  function destroyFriendship($format, $id) {
      $api_call = sprintf("http://twitter.com/friendships/destroy/%s.%s", $id, $format);
      return $this->APICall($api_call, true, true);
  }
    
  function getFriendsIds($format, $id = NULL) {
        // take care of the id parameter
        if ($id != NULL) {
          $api_call = sprintf("http://twitter.com/friends/ids/%s.%s", $id, $format);
        }
        else {
          $api_call = sprintf("http://twitter.com/friends/ids.%s", $format);
        }
  
        return $this->APICall($api_call, true);
  }
  
  function friendshipExists($format, $user_a, $user_b) {
      $api_call = sprintf("http://twitter.com/friendships/exists.%s?user_a=%s&user_b=%s", $format, $user_a, $user_b);
      return $this->APICall($api_call, true);
  }

  function APICall($api_url, $require_credentials = false, $http_post = false) {
    $curl_handle = curl_init();
    if($this->application_source){
      $api_url .= "&source=" . $this->application_source;
    }
    curl_setopt($curl_handle, CURLOPT_URL, $api_url);
    if ($require_credentials) {
      curl_setopt($curl_handle, CURLOPT_USERPWD, $this->credentials);
    }
    if ($http_post) {
      curl_setopt($curl_handle, CURLOPT_POST, true);
    }
    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl_handle, CURLOPT_HTTPHEADER, array('Expect:'));
    $twitter_data = curl_exec($curl_handle);
    $this->http_status = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);
    $this->last_api_call = $api_url;
    curl_close($curl_handle);
    return $twitter_data;
  }
  
  function lastStatusCode() {
    return $this->http_status;
  }
  
  function lastAPICall() {
    return $this->last_api_call;
  }
}
?>