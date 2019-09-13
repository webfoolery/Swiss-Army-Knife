<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.plugin.plugin' );
// require_once(JPATH_SITE.DS.'/components/com_swiss/swissHelper.php');

// TODO:: onUserAfterSave AUTO LOGIN NOT WORKING (MAY NEED TO BE IN USER PLUGIN?)
// TODO:: REQUIRE $_GET VARIABLE TO ACCESS ADMIN AREA IF NOT LOGGED IN
// TODO:: IP BASED VISITOR BLACKLIST
// TODO:: MASTER USER LOGIN --> CAN'T BE DONE IN plgSystem AS onUserAuthenticate WON'T TRIGGER UNLESS IN AN AUTHENTICATION PLUGIN...
// TODO:: UNABLE TO SHOW SYSTEM MESSAGE AFTER LOGOUT. ONE CLICK SHOULD TELL PEOPLE THEY'RE LOGGED OUT
// TODO:: replaceContentAnywhere() CURRENTLY INCLUDES THE <HEAD>. USING onContentPrepare MIGHT LIMIT IT TO THE BODY
// TODO:: IP BASED DEBUG OUTPUT
// TODO:: DATABASE LOGGING NEEDS FINISHING
	// CURRENTLY OUTPUTS TO SCREEN, NEED FILE OPTIONS, LIMIT USER OPTIONS ETC.

class plgSystemSwiss extends JPlugin {
	protected $logDB = false;
	
	public function __construct( &$subject, $config ) {
		parent::__construct( $subject, $config );
		// Do some extra initialisation in this constructor if required
        $lang = JFactory::getLanguage();
        $lang->load('plg_system_swiss', JPATH_ADMINISTRATOR);
		
		
		if ($this->params->get('debugOutputEnabled', 0) == 1 && $this->params->get('queries', 0) == 1) {
			// ENABLE THE LOGGING OF QUERIES 
			if (JFactory::getApplication()->isAdmin() == false) {
				JFactory::getDbo()->setDebug(true);
				$this->logDB = true;
			}
		}
	}
	
	public function __destruct() {
		if ($this->logDB) $this->storeDatabaseLog();
	}
	
	public function storeDatabaseLog() {
		$db	= JFactory::getDbo();
		$log = $db->getLog();
		if (!$log) return
		$queryList = [];
		foreach ($log as $k => $sql) {
			// echo $k.' = '.$sql.'<hr />';
			if (strpos(strtolower($sql), 'select') !== 10) {
				if (strpos(strtolower($sql), 'show') !== 10) {
					$queryList[] = str_replace(array("\r\n", "\r", "\n"), ' ', $sql);
				}
			}
		}
		if (count($queryList)) {
			$logFile = '../dblog.sql';
			// SAVE QUERIES TO A FILE
			// file_put_contents($logFile, '--'.date('Y-m-d H:i:s').PHP_EOL, FILE_APPEND);
			// file_put_contents('../dblog.sql', implode(PHP_EOL, $queryList).PHP_EOL, FILE_APPEND);
			echo '<div class="dbLog"><h1>Database log:</h1>'.implode('<hr style="margin:0;padding:0;border-bottom:1px solid black;" />', $queryList).'</div>';
		}
	}



/*
// THE METHODS BELOW ARE AVAILABLE FOR EXPLOITATION BUT CURRENTLY UNUSED

	public function onContentPrepare($context, &$article, &$params, $page = 0) {
		// This is the first stage in preparing content for output and is the most common point for content orientated plugins to do their work. 
		// Since the article and related parameters are passed by reference, event handlers can modify them prior to display.
	}

	public function onContentAfterTitle($context, &$article, &$params, $limitstart = 0) {
		// This event only exists in Joomla Joomla 3.x. 
		// This is a request for information that should be placed between the content title and the content body. 
		// Although parameters are passed by reference, this is not the event to modify article data. Use onContentPrepare for that purpose. 
		// Note this event has special purpose in com_content for use in handling the introtext.
	}

	public function onContentBeforeDisplay($context, &$article, &$params, $limitstart = 0) {
		// This is a request for information that should be placed immediately before the generated content. 
		// For views that generate HTML, this might include the use of styles that are specified as part of the content or related parameters. 
		// Although parameters are passed by reference, this is not the event to modify article data. Use onContentPrepare for that purpose.
	}

	public function onContentAfterDisplay($context, &$article, &$params, $limitstart = 0) {
		// This is a request for information that should be placed immediately after the generated content. 
		// For views that generate HTML, this might include the closure of styles that are specified as part of the content or related parameters. 
		// Although parameters are passed by reference, this is not the event to modify article data. Use onContentPrepare for that purpose.
	}

	public function onContentBeforeSave($context, $article, $isNew, $data) {
		// This is an event that is called right before the content is saved into the database. 
		// You can abort the save by returning false. In the case of JModelLegacy for example 
		// the error will then be set by calling $this->setError($table->getError); to be displayed to user.
	}

	public function onContentPrepareForm($form, $data) {
		// Called before a JForm is rendered. It can be used to modify the JForm object in memory before rendering. 
		// For example, use JForm->loadFile() to add fields or JForm->removeField() to remove fields. 
		// Or use JForm->setFieldAttribute() or other JForm methods to modify fields for the form.
	}

	public function onContentPrepareData($form, $data) {
		// Called after the data for a JForm has been retrieved. 
		// It can be used to modify the data for a JForm object in memory before rendering. 
		// This is usually used in tandem with the onContentPrepareForm method - this event adds the data to the already altered JForm.
	}

	public function onContentBeforeDelete($context, $data) {
		// This is an event that is called right before the content is deleted from the database. 
		// You can abort the delete by returning false. 
		// In the case of JModelLegacy for example the error will then be set by calling $this->setError($table->getError); to be displayed to user.
	}

	public function onContentAfterDelete($context, $article) {
		// This is an event that is called after the content is deleted from the database. 
		// An example use case would be redirecting user to the appropriate place after deleting.
	}

	public function onContentChangeState($context, $article) {
		// This is an event that is called after content has its state change (e.g. Published to Unpublished).
	}

	public function onContentSearch($text, $phrase, $ordering, $areas) {
		// This event is triggered by a variety of search related operations. 
		// It is a request for a plugin to return the result of a search request. 
		// The rows must return the following fields, which are used in a common display routine:
			// browsernav
			// catslug
			// created
			// href
			// section
			// slug
			// text
			// title
	}

	function onBeforeRender() {
		// This event is triggered immediately before the framework has rendered the application
	}

	function onAfterDispatch() {
		// This event is triggered after the framework has dispatched the application
	}

	function onSearchAreas() {
	}
	
 	public function onUserLogout($credentials=array(), $options=array()) {
		// This event is triggered before the user is logged out of the system.
		// If any plugin returns false, the global logout fails and the onUserLogoutFailure event is fired; if it succeeds, onUserAfterLogout event is triggered instead
	}
	
	function onUserAfterLogin($options = array()) {
		// This event is triggered whenever a user is successfully logged in
		// Options is array with:
			// remember
			// return
			// entry_url
			// action
			// user - JUser Object
			// responseType
	}

*/




	function onAfterInitialise() {
		// This event is triggered after the framework has loaded and initialised and the router has routed the client request
		$app = JFactory::getApplication();
		if ($app->isAdmin()) return; // DO NOT RUN IN ADMIN AREA
		$user = JFactory::getUser();
		$siteOffline = $this->params->get('siteOfflineEnabled', 0);
		if ($siteOffline != '0' && strpos($this->params->get('allowedIpAddresses'), $_SERVER['REMOTE_ADDR']) === false) $this->siteOffline($siteOffline);
	}
	
	function siteOffline($offlineMethod) {
		if ($offlineMethod == 'html') {
			echo $this->params->get('offlineHTML', 'Site offline. Please try later.');
		}
		if ($offlineMethod == 'redirect') {
			$redirectUrl = $this->params->get('offlineRedirectUrl');
			$app = JFactory::getApplication();
			$app->redirect($redirectUrl);
			// return;
			// header('Location: '.$redirectUrl);
		}
		exit();
	}

 	function onAfterRoute() {
		 // This event is triggered after the framework has loaded and initialised and the router has routed the client request
		$app = JFactory::getApplication();
		if ($app->isAdmin()) return; // DO NOT RUN IN ADMIN AREA
		$user = JFactory::getUser();
		$jinput = $app->input;
		
		// PROCESS ANY LOGIN REDIRECTING
		if ($this->params->get('afterLoginRedirectEnabled', false) == 1) {
			if ($jinput->get('option') == 'com_users' && $jinput->get('task') == 'user.login') {
				$afterLoginRedirectUsergroup = (array)$this->params->get('afterLoginRedirectUsergroup', null);
				$afterLoginRedirectUrl = $this->params->get('afterLoginRedirectUrl', '');
// echo 'index.php?Itemid='.$afterLoginRedirectUrl;exit;
				// USER IS NOT YET LOGGED IN SO WE NEED TO CHECK GROUPS OURSELF...
				$db = JFactory::getDbo();
				$db->setQuery("SELECT id FROM #__users WHERE username = '".$jinput->get('username')."'");
				if ($userId = $db->loadResult()) {
					$notLoggedInUser = JFactory::getUser($userId);
					if (array_intersect($afterLoginRedirectUsergroup, $notLoggedInUser->groups)) {
						JRequest::setVar('return', base64_encode('index.php?Itemid='.$afterLoginRedirectUrl));
					}
				}
			}
		}
		
		if ($jinput->get('option') == 'com_users' && ($jinput->get('task') == 'user.logout' || (!$user->guest && $jinput->get('view') == 'login'))) {
			// CHECK ANY LOGOUT REDIRECTING
			if ($this->params->get('afterLogOutRedirectEnabled', false) == 1) {
				$afterLogOutRedirectUsergroup = (array)$this->params->get('afterLogOutRedirectUsergroup', null);
				$afterLogOutRedirectUrl = $this->params->get('afterLogOutRedirectUrl', 'index.php?option=com_users&view=login');
				if (array_intersect($afterLogOutRedirectUsergroup, $user->groups) && strlen($afterLogOutRedirectUrl)) {
					// USER IS IN AT LEAST 1 OF THE SPECIFIED GROUPS
					if ($this->params->get('oneClickLogoutEnabled', false) != 1) {
						$app->logout($user->id); // LOG THE USER OUT
						// $app->redirect($afterLogOutRedirectUrl);
						$app->redirect(JRoute::_('index.php?Itemid='.$afterLogOutRedirectUrl, false));
					}
					else $return = $afterLogOutRedirectUrl;
				}
			}
			// CHECK TO SEE IF ONE-CLICK LOGOUT IS ENABLED
			if ($this->params->get('oneClickLogoutEnabled', false) == 1) {
				$error = $app->logout();
				if (!($error instanceof Exception)) {
					if (!isset($return)) {
						$return = JRequest::getVar('return', '', 'method', 'base64');
						$return = base64_decode($return);
						if (!JURI::isInternal($return)) $return = '';
					}
					else $return = 'index.php?Itemid='.$return;
					$app->redirect(JRoute::_($return, false));
				}
				else $app->redirect(JRoute::_('index.php?option=com_users&view=login', false));
			}
		}
		
		
		// REDIRECT HOME PAGE FOR REGISTERED USERS
		if ($this->params->get('registeredUserHomepageEnabled', false) == 1) {
			$task = JRequest::getVar('task', false);
			if ($user->guest || $task=='user.logout') return;
			$registeredUserHomeGroup1 = $this->params->get('registeredUserHomeGroup1', false);
			$registeredUserHomeGroup2 = $this->params->get('registeredUserHomeGroup2', false);
			$registeredUserHomeGroup3 = $this->params->get('registeredUserHomeGroup3', false);
			$menu = $app->getMenu();
			$currentMenuItem = $menu->getActive();
			$defaultHomePage = $menu->getDefault();
			if($currentMenuItem == $defaultHomePage){
				if (array_intersect($registeredUserHomeGroup1, $user->groups)) $app->redirect(JRoute::_("index.php?Itemid=".$this->params->get('registeredUserHomeUrl1', $defaultHomePage),false));
				if (array_intersect($registeredUserHomeGroup2, $user->groups)) $app->redirect(JRoute::_("index.php?Itemid=".$this->params->get('registeredUserHomeUrl2', $defaultHomePage),false));
				if (array_intersect($registeredUserHomeGroup3, $user->groups)) $app->redirect(JRoute::_("index.php?Itemid=".$this->params->get('registeredUserHomeUrl3', $defaultHomePage),false));
			}
		}
		return true;
	}

	function onAfterRender() {
		/* This event is triggered after the framework has rendered the application. */
		/* When this event is triggered the output of the application is available in the response buffer. */
		$app = JFactory::getApplication();
		if ($this->params->get('enableAdminLink', false) == 1) $this->enableAdminLink();
		if ($app->isAdmin()) return; // DO NOT RUN IN ADMIN AREA
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
		// This event is triggered before the framework creates the Head section of the Document
		$app = JFactory::getApplication();
		if ($app->isAdmin()) return; // DO NOT RUN IN ADMIN AREA
		$doc = JFactory::getDocument();
		$user = JFactory::getUser();
		
		// APPLY THE SESSION KEEPALIVE OR SESSION END ALERT IF NEEDED
		if (!$user->guest) {
			$keepAliveActive = false;
			$activeScripts = serialize($doc->_script);
			// echo "<pre>";echo $this->pp($doc->_script);echo "</pre>";
			if (strpos($activeScripts, 'function keepAlive') !== false) $keepAliveActive = true; // J!2.5 CHECK
			if (strpos($activeScripts, '{r.open("GET","./",true);r.send(null)}') !== false) $keepAliveActive = true; // J!3.x CHECK
			if (!$keepAliveActive) {
				if ($this->params->get('enableKeepAliveAllPages', false) == 1) {
					// echo $this->pp($this->params->get('keepaliveUsergroup'));
					if (count($this->params->get('keepaliveUsergroup', array())) == 0 || array_intersect($this->params->get('keepaliveUsergroup', array()), $user->groups)) {
						JHtml::_('behavior.keepalive');
					}
				}
				else if($this->params->get('enableSessionEndAlert', 0) != false && array_intersect($this->params->get('sessionEndAlertUsergroup', array()), $user->groups)) {
					$alertType = $this->params->get('enableSessionEndAlert', 'modal');
					// exit($alertType);
					$config = JFactory::getConfig();
					$sessionTime = $config->get('lifetime');
					// $sessionTime = 3; // FOR TESTING
					$alertTime = $this->params->get('sessionEndStartTime', 120);
					$timeout = (($sessionTime * 60) - (int)$alertTime) * 1000; // BECAUSE JS USES MILLISECONDS
					if ($this->params->get('sessionEndTestMode', false) == 1) {
						$alertTime = 10;
						$timeout = 5000;
					}
					if ($alertType == 'alert') {
						$sessionAlertJavascript = "
							setTimeout(fireKeepAlive,".$timeout.");
							function fireKeepAlive() {
								triggerSessionEndAlert = setTimeout(fireSessionEnded,(".$alertTime * 1000 ."));
								var keepAlive = confirm('Your user session has been inactive and you will be logged out shortly.\\n\\nClick OK to stay logged in or Cancel to allow the session to end.');
								if (keepAlive) {
									clearTimeout(triggerSessionEndAlert);
									var myAjax = new Request({method: 'get', url: 'index.php'}).send();
									setTimeout(fireKeepAlive,".$timeout.");
								}
							}
							function fireSessionEnded() {alert('You have been logged out due to inactivity');}
							";
					}
					else {
						JHtml::_('behavior.modal');
						// $alertText = '<p style="text-align:center;padding:10px;">Your user session has been inactive and you will be logged out automatically in a few minutes.<br /><br /><button type="button" id="allowMaintainSession" onclick="keepSessionAlive();window.parent.SqueezeBox.close();">Keep me logged in</button><button type="button" id="allowExpireSession" onclick="window.parent.SqueezeBox.close();">Allow my session to end</button></p>';
						$alertText = $this->params->get('sessionEndAlertModalText', '<p class="sessionEndWarningText">Your user session has been inactive and you will be logged out automatically in {countdown}');
						$sessionEndedModalText = $this->params->get('sessionEndedModalText', '<p class="sessionEndExpiredText">Your session has ended. You will have to log in again to continue.</p>');
						$sessionEndAlertModalSize = $this->params->get('sessionEndAlertModalSize', '400x150');
						$sessionEndedModalSize = $this->params->get('sessionEndedModalSize', '400x150');
						list($alertModalWidth, $alertModalHeight) = explode("x", $sessionEndAlertModalSize);
						list($endedModalWidth, $endedModalHeight) = explode("x", $sessionEndedModalSize);
						$alertText = str_replace('{countdown}', '<span id="logoutTimer">'.floor($alertTime/60).':'.str_pad($alertTime%60, 2, '0', STR_PAD_LEFT).'</span>', $alertText);
						$alertText .= '<p class="sessionEndAlertButtonContainer"><button type="button" id="allowMaintainSession" onclick="return keepSessionAlive();">Keep me logged in</button><button type="button" id="allowExpireSession" onclick="return allowExpire();">Allow my session to end</button></p>';
						$sessionAlertJavascript = <<<JAVASCRIPT
							setTimeout(showModal,$timeout);
							function showModal() {
								sessionEndingSoonAlert = new Element('div#sessionEndingAlertContainer').set('html',  '$alertText');
								SqueezeBox.open(sessionEndingSoonAlert, {handler: 'adopt', size: {x: $alertModalWidth, y: $alertModalHeight}});
								setTimeout(function(){countdownTimer = setInterval(countdown, 1000);},1000);
							}
							function countdown() {
								if (typeof countdown.current === 'undefined') countdown.current = $alertTime;
								countdown.current --;
								if (countdown.current < 0) showSessionEnded();
								var minutes = Math.floor(countdown.current / 60);
								var seconds = countdown.current % 60;
								if ($('logoutTimer')) $('logoutTimer').set('text', minutes+':'+('0'+seconds).slice(-2));
								// console.log( minutes+':'+('0'+seconds).slice(-2));
							}
							function showSessionEnded() {
								// console.log('session ended!');
								clearInterval(countdownTimer);
								window.parent.SqueezeBox.close();
								sessionhasEndedAlert = new Element('div#sessionHasEnded').set('html',  '$sessionEndedModalText');
								SqueezeBox.open(sessionhasEndedAlert, {handler: 'adopt', size: {x: $endedModalWidth, y: $endedModalHeight}});
							}
							function keepSessionAlive() {
								clearInterval(countdownTimer);
								var myAjax = new Request({method: 'get', url: 'index.php'}).send();
								window.parent.SqueezeBox.close();
								setTimeout(showModal,$timeout);
							}
							function allowExpire() {
								// clearInterval(countdownTimer);
								window.parent.SqueezeBox.close();
							}
							// window.addEvent('domready', function() {
								// SqueezeBox.initialize();
							// });
JAVASCRIPT;
						$sessionAlertCss = ".sessionEndAlertButtonContainer{text-align:center;}
								.sessionEndWarningText{text-align:center;padding:10px;line-height:100%;}
								.sessionEndExpiredText{text-align:center;padding:10px;line-height:100%;}
								.sessionEndAlertButtonContainer{margin:0;padding:0;}
								button#allowMaintainSession, button#allowExpireSession{padding:5px;margin:0 3px;}";
						$doc->addStyleDeclaration ($sessionAlertCss);
					}
					$doc->addScriptDeclaration ($sessionAlertJavascript);
					
				}
			}
		}
		
		// INSERT ANY REQUESTED JAVASCRIPT OR CSS
		if ($this->params->get('insertCodeEnabled', false) == 1) {
			$insertCss = $this->params->get('css', '');
			$insertCssExternal = $this->params->get('cssExternal', '');
			$insertJavascript = $this->params->get('javascript', '');
			$insertJavascriptExternal = $this->params->get('javascriptExternal', '');
			if (strlen($insertCss)) $doc->addStyleDeclaration ($insertCss);
			if (strlen($insertJavascript)) $doc->addScriptDeclaration ($insertJavascript);
			if (strlen($insertJavascriptExternal)) {
				$sources = explode("\r\n", $insertJavascriptExternal);
				foreach ($sources as $source) $doc->addScript ($source);
			}
			if (strlen($insertCssExternal)) {
				$sources = explode("\r\n", $insertCssExternal);
				foreach ($sources as $source) $doc->addStyleSheet ($source);
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
					z-index:999;
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
		 // This event is triggered after the user is authenticated against the Joomla! user-base
		$app = JFactory::getApplication();
		if (!$app->isSite()) return true; // DO NOT RUN IN ADMIN AREA
		
		// HANDLE DISPLAY OF LOGIN MESSAGE
		if ($this->params->get('afterLoginSystemMessageEnabled', 0) != false) {
			$afterLoginSystemMessage = $this->params->get('afterLoginSystemMessage');
			if (strlen($afterLoginSystemMessage)) {
				$afterLoginSystemMessageType = $this->params->get('afterLoginSystemMessageEnabled', 'message');
				$app->enqueueMessage($afterLoginSystemMessage, $afterLoginSystemMessageType);
			}
		}
	}
	
 	// public function onUserLogout($credentials=array(), $options=array()) {
		// FIRES BEFORE LOGOUT PROCESS
		// exit('onUserLogout triggered!');
	// }
	
	// function onUserAfterLogin($options = array()) {
		// THIS DOESN'T SEEM TO EXIST!
		// exit('onUserAfterLogin triggered!');
	// }

	function onUserLoginFailure() {
		// This event is triggered whenever a user authentication request is failed by any plugin
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
				// if (strlen($failedLoginSystemMessage)) $app->redirect($failedLoginRedirectUrl, $failedLoginSystemMessage, $failedLoginSystemMessageType);
				if (strlen($failedLoginSystemMessage)) $app->redirect(JRoute::_('index.php?Itemid='.$failedLoginRedirectUrl, false), $failedLoginSystemMessage, $failedLoginSystemMessageType);
				// else $app->redirect($failedLoginRedirectUrl);
				else $app->redirect(JRoute::_('index.php?Itemid='.$failedLoginRedirectUrl, false));
			}
		}
		return true;
	}
	
	function onUserAfterSave($user, $isNew, $success, $msg) {
		// This event is triggered after an update of a user record, or when a new user has been stored in the database
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
	
	function pp($arr){
		$retStr = '<ul>';
		if (is_array($arr) || is_object($arr)){
			foreach ($arr as $key=>$val){
				if (is_array($val)) $retStr .= '<li>' . $key . ' [array] => ' . $this->pp($val) . '</li>';
				elseif (is_object($val)) $retStr .= '<li>' . $key . ' [object] => ' . $this->pp($val) . '</li>';
				else $retStr .= '<li>' . $key . ' => ' . $val . '</li>';
			}
		}
		$retStr .= '</ul>';
		return $retStr;
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
	
    function enableAdminLink()  {
		// FORCE BACKEND ADMIN LINK TO ALWAYS WORK
		$body = JResponse::getBody();
		// exit('xxx'.$body).
		// CSS IS ADDED IN THE onBeforeCompileHead METHOD
		// $body = str_replace('<a class="admin-logo disabled">', '<a class="admin-logo" href="index.php">', $body);
		$body = str_replace('admin-logo disabled', 'admin-logo" href="index.php', $body);
		JResponse::setBody($body);
		return true;
    }
}