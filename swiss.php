<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.plugin.plugin' );
// require_once(JPATH_SITE.DS.'/components/com_swiss/swissHelper.php');

// TODO:: onUserAfterSave AUTO LOGIN NOT WORKING (MAY NEED TO BE IN USER PLUGIN?)
// TODO:: CUSTOM HOME PAGE BASED ON USER GROUP (ADMIN, REGISTERED ETC.)
// TODO:: REQUIRE $_GET VARIABLE TO ACCESS ADMIN AREA
// TODO:: IP BASED DEBUG OUTPUT
// TODO:: IP BASED VISITOR BLACKLIST
// TODO:: MASTER USER LOGIN --> CAN'T BE DONE IN plgSystem AS onUserAuthenticate WON'T TRIGGER UNLESS IN AN AUTHENTICATION PLUGIN...
// TODO:: TAKE SITE OFFLINE, OFFER IP ACCESS AND REDIRECT AND HTML ENTRY

class plgSystemSwiss extends JPlugin {
	
	public function __construct( &$subject, $config ) {
		parent::__construct( $subject, $config );
		// Do some extra initialisation in this constructor if required
        $lang = & JFactory::getLanguage();
        $lang->load('plg_system_swiss', JPATH_ADMINISTRATOR);
	}

	function onBeforeRender() {
	}

	function onAfterDispatch() {
	}

	function onSearch() {
	}

	function onSearchAreas() {
	}

	function onGetWebServices() {
	}
	
/* 	function onUserLogout($credentials=array(), $options=array()) {} */

	function onAfterInitialise() {
		$app = JFactory::getApplication();
		if ($app->isAdmin()) return; // DO NOT RUN IN ADMIN AREA
		$user = JFactory::getUser();
	}

 	function onAfterRoute() {
		$app = JFactory::getApplication();
		if ($app->isAdmin()) return; // DO NOT RUN IN ADMIN AREA
		$user = JFactory::getUser();
		$jinput = $app->input;
		
		// PROCESS ANY LOGIN REDIRECTING
		if ($this->params->get('afterLoginRedirectEnabled', false) == 1) {
			if ($jinput->get('option') == 'com_users' && $jinput->get('task') == 'user.login') {
				$afterLoginRedirectUsergroup = (array)$this->params->get('afterLoginRedirectUsergroup', null);
				$afterLoginRedirectUrl = $this->params->get('afterLoginRedirectUrl', false);
				// USER IS NOT YET LOGGED IN SO WE NEED TO CHECK GROUPS OURSELF...
				$db = JFactory::getDbo();
				$db->setQuery("SELECT id FROM #__users WHERE username = '".$jinput->get('username')."'");
				if ($userId = $db->loadResult()) {
					$notLoggedInUser = JFactory::getUser($userId);
					if (array_intersect($afterLoginRedirectUsergroup, $notLoggedInUser->groups)) {
						if (strlen($afterLoginRedirectUrl)) {
							JRequest::setVar('return', urlencode(base64_encode($afterLoginRedirectUrl)));
						}
					}
				}
			}
		}
		
		// PROCESS ANY LOGOUT REDIRECTING
		if ($this->params->get('afterLogOutRedirectEnabled', false) == 1) {
			if ($jinput->get('option') == 'com_users' && $jinput->get('task') == 'user.logout') {
				$afterLogOutRedirectUsergroup = (array)$this->params->get('afterLogOutRedirectUsergroup', null);
				$afterLogOutRedirectUrl = $this->params->get('afterLogOutRedirectUrl', false);
				if (array_intersect($afterLogOutRedirectUsergroup, $user->groups)) {
					// USER IS IN AT LEAST 1 OF THE SPECIFIED GROUPS
					if (strlen($afterLogOutRedirectUrl)) {
						$app->logout($user->id); // OTHERWISE THEY'LL STAY LOGGED IN!
						$app->redirect($afterLogOutRedirectUrl);
					}
				}
			}
		}
		
		// REDIRECT HOME PAGE FOR REGISTERED USERS
		if ($this->params->get('registeredUserHomepageEnabled', false) == 1) {
			$task = JRequest::getVar('task', false);
			if ($user->guest || $task=='user.logout') return;
			$menu = $app->getMenu();
			$currentMenuItem = $menu->getActive();
			$defaultHomePage = $menu->getDefault();
			if($currentMenuItem == $defaultHomePage){
				$app->redirect(JRoute::_("index.php?Itemid=".$this->params->get('registeredUserHomeUrl', $defaultHomepPage),false));
			}
		}
		return true;
	}

	function onAfterRender() {
		$app = JFactory::getApplication();
		if ($app->isAdmin()) return; // DO NOT RUN IN ADMIN AREA
		if ($this->params->get('replaceAnywhereEnabled', false) == 1) $this->replaceContentAnywhere();
		if ($this->params->get('cookieDirectiveEnabled', false) == 1) $this->cookieDirective();
		
		// INSERT ANY REQUESTED BODY CODE
		if ($this->params->get('insertCodeEnabled', false) == 1) {
			$insertBodyStart = $this->params->get('bodyStart', '');
			$insertBodyEnd = $this->params->get('bodyEnd', '');
			$buffer = JResponse::getBody();
			if (strlen($insertBodyStart)) {
				// NEED TO BE CLEVER FOR THIS INSERT AS BODY CAN HAVE EXTRA ELEMENTS
				$bodyTagEndPosition = strpos($buffer, ">", strpos($buffer, "<body")) + 1;
				if ($bodyTagEndPosition) {
					$buffer = substr($buffer, 0, $bodyTagEndPosition).$insertBodyStart.substr($buffer, $bodyTagEndPosition);
				}
			}
			if (strlen($insertBodyEnd)) $buffer = str_replace('</body>', $insertBodyEnd.'</body>', $buffer);
			JResponse::setBody($buffer);
		}
		return true;
	}

	function onBeforeCompileHead() {
		$app = JFactory::getApplication();
		if ($app->isAdmin()) return; // DO NOT RUN IN ADMIN AREA
		$doc = JFactory::getDocument();
		
		// INSERT ANY REQUESTED JAVASCRIPT
		if ($this->params->get('insertCodeEnabled', false) == 1) {
			$insertCss = $this->params->get('css', '');
			$insertJavascript = $this->params->get('javascript', '');
			$insertJavascriptExternal = $this->params->get('javascriptExternal', '');
			if (strlen($insertCss)) $doc->addStyleDeclaration ($insertCss);
			if (strlen($insertJavascript)) $doc->addScriptDeclaration ($insertJavascript);
			if (strlen($insertJavascriptExternal)) {
				$sources = explode('---', $insertJavascriptExternal);
				foreach ($sources as $source) $doc->addScript ($source);
			}
		}
		
		// FIX THE GENERATOR METATAG
		if ($this->params->get('generatorTagEnabled', false) == 1) {
			$doc->setGenerator($this->params->get('metaGenerator'));
		}
		
		// ADD CSS FOR COOKIE DIRECTIVE IF NEEDED
		if ($this->params->get('cookieDirectiveEnabled', false) == 1) {
			$cookieDirectiveStyle="
				#swissEuCookieDirective {
					position:fixed;
					".$this->params->get('cookieBoxPosition', 'bottom:0').";
					width:".$this->params->get('cookieBoxWidth', '100%').";
					padding:".$this->params->get('cookieBoxPadding', '10px').";
					background-color:".$this->params->get('cookieBoxColour', 'black').";
					border-radius:".$this->params->get('cookieBoxRadius', '0').";
					-moz-border-radius:".$this->params->get('cookieBoxRadius', '0').";
					-webkit-border-radius:".$this->params->get('cookieBoxRadius', '0').";
					margin:0 auto;
					left:0;
					right:0;
					text-align: center;
					display:block;
				}
				p.swissCookieInfoText {
					display:inline-block;
					text-align:".$this->params->get('cookieTextAlign', 'center').";
					font-size:".$this->params->get('cookieTextSize', '1em').";
					color:".$this->params->get('cookieTextColour', 'white').";
					margin:0;
					padding:0;
				}
				body{".(strpos($this->params->get('cookieBoxPosition'), 'bottom') !== false ? "margin-bottom:".$this->params->get('cookieBodyMargin') : "margin-top:".$this->params->get('cookieBodyMargin')).";}
			";
			$doc->addStyleDeclaration ($cookieDirectiveStyle);
		}
	}
	
 	public function onUserLogin($user, $options = array()) {
		$app = JFactory::getApplication();
		if (!$app->isSite()) return true; // DO NOT RUN IN ADMIN AREA
		
		// HANDLE DISPLAY OF LOGIN MESSAGE
		if ($this->params->get('afterLoginSystemMessageEnabled', false) == 1) {
			$afterLoginSystemMessage = $this->params->get('afterLoginSystemMessage');
			if (strlen($afterLoginSystemMessage)) {
				$afterLoginSystemMessageType = $this->params->get('afterLoginSystemMessageType', 'message');
				$app->enqueueMessage($afterLoginSystemMessage, $afterLoginSystemMessageType);
			}
		}
	}
	
	function onUserAfterLogin($options = array()) {
		// exit('onUserAfterLogin triggered!'); // THIS DOESN'T SEEM TO EXIST!
	}

	function onUserLoginFailure() {
		$app = JFactory::getApplication();
		if (!$app->isSite()) return true; // DO NOT RUN IN ADMIN AREA
		
		// HANDLE FAILED LOGIN
		if ($this->params->get('failedLoginRedirectEnabled', false) == 1) {
			$failedLoginSystemMessage = $this->params->get('failedLoginSystemMessage', false);
			$failedLoginSystemMessageType = $this->params->get('failedLoginSystemMessageType', 'error');
			$failedLoginRedirectUrl = $this->params->get('failedLoginRedirectUrl', false);
			$failedLoginDelay = $this->params->get('failedLoginDelay', 0);
			if ($failedLoginDelay != "0" ) {
				sleep ((int)$failedLoginDelay);
			}
			if (strlen($failedLoginSystemMessage) && !$failedLoginRedirectUrl) {
				$app->enqueueMessage($failedLoginSystemMessage, $failedLoginSystemMessageType);
			}
			if (strlen($failedLoginRedirectUrl)) {
				if (strlen($failedLoginSystemMessage)) $app->redirect($failedLoginRedirectUrl, $failedLoginSystemMessage, $failedLoginSystemMessageType);
				else $app->redirect($failedLoginRedirectUrl);
			}
		}
		return true;
	}
	
	function onUserAfterSave($user, $isNew, $success, $msg) {
		$app = JFactory::getApplication();
		// AUTOMATICALLY LOG THE NEW USER IN
		if($app->isSite() && $isNew && $this->params->get('autoLoginEnabled') == 1) {
			$credentials = array(
				"username" => $user["email"], 
				"password" => $user["password_clear"]
			);
			$app->login($credentials);
		}
		return true;
	}
	
	function replaceContentAnywhere() {
		$replaceCustom = $this->params->get('replaceCustom', false);
		$buffer = JResponse::getBody();
		if (strpos($buffer, '{swiss.') === false && !strlen($replaceCustom)) return;
		$user  = JFactory::getUser();
		/* if (strpos($buffer, '{swiss.user.dob') !== false) {
			// GET THE USERS PROFILE DATA
			$db = JFactory::getDbo();
			$db->setQuery("SELECT profile_value FROM #__user_profiles WHERE profile_key = 'swiss.dob' AND user_id = ".(int)$user->id);
		} */
		$search = array(
			'{swiss.username}', 
			'{swiss.name}', 
			'{swiss.lastvisitdate}', 
			'{swiss.email}', 
			'{swiss.today.dd/mm/yyyy}', 
			'{swiss.today.day}', 
			'{swiss.today.date}', 
			'{swiss.today.month}', 
			'{swiss.today.year}', 
		);
		
		$replace = array(
			($user->guest ? 'Guest' : $user->username), 
			($user->guest ? 'Guest' : $user->name), 
			($user->guest ? 'Unknown' : $user->lastvisitDate), 
			($user->guest ? 'Unknown' : $user->email), 
			date('d/m/Y'), 
			date('l'), 
			date('jS'), 
			date('F'), 
			date('Y'), 
		);
		// ADD IN ANY CUSTOM SEARCH/REPLACE STRINGS
		if (strlen($replaceCustom)){
			$rows = explode("\r\n", $replaceCustom);
			foreach ($rows as $row) {
				$data = explode('=>', $row);
				$search[] = $data[0];
				$replace[] = $data[1];
				// $search[] = substr($row, 0, strpos($row, '=>'));
				// $replace[] = substr($row, strpos($row, '=>')+2, strlen($row)-strpos($row, '=>'));
			}
		}
		// echo"<pre>";print_r($search);echo"</pre>";echo"<pre>";print_r($replace);echo"</pre>";exit;
// TODO: SOME SORT OF CLEANUP OF UNUSED {swiss.? STRINGS?
		$buffer = str_replace($search, $replace, $buffer);
		JResponse::setBody($buffer);
	}
	
    function cookieDirective()  {
		// IF THEY'VE ACCEPTED ALREADY RETURN!
		if(isset($_COOKIE['cookieAcceptance']) && $_COOKIE['cookieAcceptance'] == "accepted") return true;
		$cookieInfoText = $this->params->get('cookieInfoText', '');
		$cookieButtonText = $this->params->get('cookieButtonText', '');
		$cookieSetter = "document.cookie='cookieAcceptance=accepted;expires=".date('D, j M Y H:i:s', strtotime('+6 months'))."; path=/';document.getElementById('swissEuCookieDirective').style.display='none';";
		$output = '
			<div id="swissEuCookieDirective">
				<p class="swissCookieInfoText">
					'.$cookieInfoText.'
					<button class="swissAcceptCookies '.$this->params->get('cookieButtonClass', '').'" type="button" onclick="'.$cookieSetter.'">'.$cookieButtonText.'</button>
				</p>
			</div>
		';
		$body = JResponse::getBody();
		// CSS IS ADDED IN THE onBeforeCompileHead METHOD
		$body = str_replace('</body>', $output.'</body>', $body);
		JResponse::setBody($body);
		return true;
    }
	
	
	function killMessage($messageText) {
		// REMOVE A MESSAGE FROM THE MESSAGE QUEUE
		// USAGE: killMessage('matching part of the message text');
		// exit('here');
		$app = JFactory::getApplication();
		$appReflection = new ReflectionClass(get_class($app));
		$_messageQueue = $appReflection->getProperty('_messageQueue');
		$_messageQueue->setAccessible(true);
		$messages = $_messageQueue->getValue($app);
		foreach($messages as $key=>$message) {
			// echo 'Key: '.$key.' => '.$message['message']." (message)<hr />";
			// if ($message['message'] == $messageText) {
			if (strpos($message['message'], $messageText) !== false) {
				unset($messages[$key]);
				// echo "----> UNSET IT <----";
			}
		}
		$_messageQueue->setValue($app,$messages);
	}
}