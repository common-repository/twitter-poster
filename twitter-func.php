<?php
require 'twitter.lib.php';
if (!class_exists('SimpleXMLElement')) {
	require_once 'simplexml.class.php';
}

class TwitterFunc {
	/* Username format string */
	var $username;
	/* password format string */
	var $password;
	/* twitter class */
	var $twitter;

	var $user_details;
	/* Twitter class constructor */
	function TwitterFunc($user, $pass) {
		$this->username = $user;
		$this->password = $pass;
		//Iniciates the twitter function
		$this->twitter = new Twitter($this->username, $this->password);
	}
	
	function addFriends($keyword, $num_friends) {
		$search_num = $num_friends;
		$twitter = $this->twitter;
		$c = curl_init();
		$encoded = urlencode($keyword);
		//Refer to the twitter search API documentation to change the URL.
		$search_url = "http://search.twitter.com/search.atom?q=$encoded&rpp=$search_num";
		curl_setopt($c, CURLOPT_URL, $search_url);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 3);
		curl_setopt($c, CURLOPT_TIMEOUT, 5);
		$response = curl_exec($c);
		$responseInfo = curl_getinfo($c);
		curl_close($c);
		$added = 0;
//		try {
			if (intval($responseInfo['http_code']) == 200) {
				if (class_exists('SimpleXMLElement')) {
					//file_put_contents('tw_response.txt', $search_url . "\n" . $response);
					$xml = new SimpleXMLElement($response);
				} else {
					// PHP 4 method
					$sx = new simplexml();
					$xml = $sx->xml_load_data($response);
				}
			}

			//print_r($xml);
			$num_found = count($xml->entry);

			$i = 0;
			while ($i < $num_found) {
				//Finds the username of the people from the search
				$frienduser = explode('/', $xml->entry[$i]->author->uri);
				$friendname = $frienduser[3];
				//Checks to see if you are friends already or not.
				  if ($twitter->friendshipExists('xml', $this->username, $friendname) == '<friends>false</friends>') {
					 //If you are not friends it then makes you friends.
					$twitter->createFriendship('xml', $friendname);
					$added++;
					if ($added >= $num_friends) {
						return $added;
					}
				 } 
				$i++;
			}
//		} catch (Exception $e) {
//    			error_log('Caught exception adding friends: ',  $e->getMessage());
//		}
		return $added;
	}	
	
	function post($message, $url) {
		$twitter = $this->twitter;
		$twitter->updateStatus("$message - $url");
		return True;
	}
	
	
	function getRssUrl() {
		$this->getUserDetails();
		return 'http://twitter.com/statuses/user_timeline/' . $this->user_details->id . '.rss';		
	}

	function getNumPosts() {
		$this->getUserDetails();
		return $this->user_details->statuses_count;		
	}
	
	function getNumFollowers() {
		$this->getUserDetails();
		return $this->user_details->followers_count;		
	}

	function getNumFollowing() {
		$this->getUserDetails();
		return $this->user_details->friends_count;		
	}

	function getUserDetails() {
		if (!isset($this->user_details)) {
			$twitter = $this->twitter;
			$xml = $twitter->showUser("xml", $this->username);
			
			
			
			if (class_exists('SimpleXMLElement')) {
				$this->user_details = new SimpleXMLElement($xml);
			} else {
				// Use PHP 4 method
				$sx = new simplexml();
				$this->user_details = $sx->xml_load_data($xml);
			}
		}
	}
	
	
	function stopFollowingNum($numUsers) {
		$following = $this->getFollowing();
		$users = $following->id;
		//echo "Found " . count($users) . " following\n";
		if (is_array($users)) {
			$i = 0;
			while ($i < $numUsers) {
				$last = array_pop($users);
				//echo "Stopping following : $last\n";
				$resp = $this->stopFollowing($last);
				$i++;
			}
		}
	}

	function getFollowing() {
		$twitter = $this->twitter;
		$xml = $twitter->getFriendsIds("xml", $this->username);
		
		if (class_exists('SimpleXMLElement')) {
			$following = new SimpleXMLElement($xml);
		} else {
			// Use PHP 4 method
			$sx = new simplexml();
			$following = $sx->xml_load_data($xml);
		}

		return $following;
	}

	function stopFollowing($id) {
		$twitter = $this->twitter;
		$response = $twitter->destroyFriendship("xml", $id);

		return $response;
	}

}
?>