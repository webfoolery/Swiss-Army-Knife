<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.plugin.plugin' );
// require_once(JPATH_SITE.DS.'/components/com_swiss/swissHelper.php');

class plgSystemSwiss extends JPlugin {
	static $replacements = null;
	
	public function __construct( &$subject, $config ) {
		parent::__construct( $subject, $config );
		// Do some extra initialisation in this constructor if required
        $lang = & JFactory::getLanguage();
        $lang->load('plg_system_swiss', JPATH_ADMINISTRATOR);
	}

	function onAfterInitialise() {
	}

 	function onAfterRoute() {
	}

	function onAfterRender() {
		$app = JFactory::getApplication();
		if ($app->isAdmin()) return; // DO NOT RUN IN ADMIN AREA
		if ($this->params->get('replaceAnywhereEnabled', false) == 1) $this->replaceContentAnywhere();
		if ($this->params->get('cookieDirectiveEnabled', false) == 1) $this->cookieDirective();
		if ($this->params->get('insertCodeEnabled', false) == 1 && is_array($this->replacements)) $this->insertBodyCode();
		return true;
	}

	function onBeforeCompileHead() {
		$app = JFactory::getApplication();
		if (!$app->isSite()) return true; // DO NOT RUN IN ADMIN AREA
		if ($this->params->get('insertCodeEnabled', false) == 1) $this->insertCode();
	}
	
	function insertCode() {
		$insertCss = $this->params->get('css', '');
		$insertJavascript = $this->params->get('javascript', '');
		$insertBodyStart = $this->params->get('bodyStart', '');
		$insertBodyEnd = $this->params->get('bodyEnd', '');
		$doc = JFactory::getDocument();
		if (strlen($insertCss)) $document->addStyleDeclaration ($insertCss);
		if (strlen($insertBodyStart) || strlen($insertBodyEnd)) {
			$this->replacements = array(
				'start'=>$insertBodyStart
				'end'=>$insertBodyStart,
			)
		}
	}
	
	function insertBodyCode() {
		// HANDLE ANY EXTRA CODE REQUESTED TO THE <BODY> OR </BODY>
		$buffer = JResponse::getBody();
		if (is_array($this->replacements)) {
			// TODO: MAYBE USE A REGEX HERE OTHERWISE CONTENT MATCHING <BODY> OR <HEAD> MIGHT GET CHANGED!
			if ($this->replacements['start']) $buffer = str_replace('<body>', '<body>'.$this->replacements['start'], $buffer);
			if ($this->replacements['end']) $buffer = str_replace('</body>', $this->replacements['end'].'</body>', $buffer);
		}
	}
	
 	public function onUserLogin($user, $options = array()) {
		$app = JFactory::getApplication();
		if (!$app->isSite()) return true; // DO NOT RUN IN ADMIN AREA
		if ($this->params->get('afterLoginRedirectEnabled', false) == 1) {
			if (strlen($this->params->get('afterLoginRedirectUrl'))) $app->redirect(JRoute::_($this->params->get('afterLoginRedirectUrl'), false));
		}
		if (strlen($this->params->get('afterLoginSystemMessage'))) {
			$app->enqueueMessage($this->params->get('redirectAfterLoginMessage'));
		}
	}
	
	function replaceContentAnywhere() {
		$buffer = JResponse::getBody();
		if (strpos($buffer, '{swiss.') === false) return;
		$user  = &JFactory::getUser();
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
			'{swiss.today}', 
		);
		$replace = array(
			$user->username, 
			$user->name, 
			$user->lastvisitDate, 
			$user->email, 
			date('d/m/Y'), 
		);
		$buffer = str_replace($search, $replace, $buffer);
		JResponse::setBody($buffer);
	}
	
    function cookieDirective()  {
        $message = $this->params->get('warningMessage', '');
        $privacyLink = $this->params->get('detailsUrl', 'index.php');
        $backgroundColor = $this->params->get('backgroundColor', '1D1D1D');
        $textColor = $this->params->get('textColor', 'FFFFFF');
        $urlColor = $this->params->get('urlColor', 'FFFFFF');
        $continueColor = $this->params->get('continueColor', 'white');
		$buttonTextColor = "fff";
		$buttonTextShadow = "000";
        $width = $this->params->get('width', '0');
        $bottomCurves = $this->params->get('bottomcurves', '0');
        $topCurves = $this->params->get('topcurves', '0');
        $infoIcon = $this->params->get('infoicon', '1');
        $negativeVerticalMargin = $this->params->get('negativeVerticalMargin', '0');
        $overlayMode = $this->params->get('overlayMode', 'top');
		$position = $this->params->get('position', 'bottom');
		$buttonText = $this->params->get('buttontext', 'Agree');
		$cookieAcceptanceLabel = $this->params->get('iacceptcookies', 'I accept cookies from this site');
		$cookieDetailsText = $this->params->get('cookiedetailtext', 'To find out more about the cookies we use and how to delete them, see our');
		$cookieDetailsLinkText = $this->params->get('cookiedetaillink', 'Privacy Policy');
		$cookiedetaillinktitle = $this->params->get('cookiedetaillinktitle', 'View our privacy policy page');
		$infoiconaltext = $this->params->get('infoiconaltext', 'EU Cookie Directive Plugin Information');
        
        //deal with the width options
        if ($width == "0") {
            $width = "100%";
        } else {
            $width = $width . "px";
        }
		
        //Set the button text color
        if ($continueColor == "white") {
            $buttonTextColor = "000000";
			$buttonTextShadow = "ffffff";
        } else {
            $buttonTextColor = "ffffff";
			$buttonTextShadow = "000000";
        }		
        
        $document    =& JFactory::getDocument();
        $doctype    = $document->getType();
        $app =& JFactory::getApplication();
		
        $ICON_FOLDER = JURI::root() . 'plugins/system/EUCookieDirective/EUCookieDirective/images/';		
        
        if ( $app->getClientId() === 0 ) {

            // Only render for HTML output
            //if ( $doctype !== 'html' ) { return; }
            //ob_start();

            $style = "\n".'<style type="text/css">
                div#cookieMessageContainer{
                    font:normal 12px/16px Arial,Verdana,sans-serif;';
            
            /*        
            if ($overlayMode == "top") {
                $positionMode = 'position:absolute;';
            } else {
                $positionMode = 'position:fixed;';
            }
            */
            
            $positionMode = 'position:fixed;';
            $style .=  "\n".$positionMode;
            $style .= "\n".'       
                    z-index:999999;'
                    . $position .': 0;
                    right:0;
                    margin:0 auto;
					padding: 5px;
                    -webkit-border-bottom-right-radius: ' . $bottomCurves . 'px;
                    -webkit-border-bottom-left-radius: ' . $bottomCurves . 'px;
                    -moz-border-radius-bottomright: ' . $bottomCurves . 'px;
                    -moz-border-radius-bottomleft: ' . $bottomCurves . 'px;
                    border-bottom-right-radius: ' . $bottomCurves . 'px;
                    border-bottom-left-radius: ' . $bottomCurves . 'px;
                    -webkit-border-top-right-radius: ' . $topCurves . 'px;
                    -webkit-border-top-left-radius: ' . $topCurves . 'px;
                    -moz-border-radius-topright: ' . $topCurves . 'px;
                    -moz-border-radius-topleft: ' . $topCurves . 'px;
                    border-top-right-radius: ' . $topCurves . 'px;
                    border-top-left-radius: ' . $topCurves . 'px
                }
                #cookieMessageContainer table, #cookieMessageContainer tr, #cookieMessageContainer td {border:0px !important}
                #cookieMessageText p,.accept{font:normal 12px/16px Arial,Verdana,sans-serif;margin:0;padding:0 0 6px;text-align:left;vertical-align:middle}
                .accept label{vertical-align:middle}
                #cookieMessageContainer .cookie_button{background: url('. $ICON_FOLDER . 'continue_button_' . $continueColor . '.png);text-shadow: #' . $buttonTextShadow . ' 0.1em 0.1em 0.2em; color: #' . $buttonTextColor . '; padding: 5px 12px;height: 14px;float: left;}
                #cookieMessageContainer table,#cookieMessageContainer td{margin:0;padding:0;vertical-align:middle}
                #cookieMessageAgreementForm{margin:0 0 0 10px}
                #cookieMessageInformationIcon{margin:0 10px 0 0;height:29px}
                #continue_button{vertical-align:middle;cursor:pointer;margin:0 0 0 10px}
                #info_icon{vertical-align:middle;margin:5px 0 0}
                #buttonbarContainer{height:29px;margin:0 0 -10px}
                input#AcceptCookies{margin:0 10px;vertical-align:middle}
                #cookieMessageContainer .messageTable {border:0px;border-spacing:0px;border-color:#' . $backgroundColor . ';}
                #messageTable tr, #messageTable td {border:0px;cellspacing:0px;cellpadding:0px;background-color:#' . $backgroundColor . ';}
                #cookieMessageText {color:#' . $textColor . ' !important;}
                #cookieMessageText p, .accept {color:#' . $textColor . ' !important;}
                .cookieMessageText {color:#' . $textColor . ' !important;}
                p.cookieMessageText {color:#' . $textColor . ' !important;}
				.accept {float: left;padding: 5px 6px 4px 15px;}
				a#cookieMessageDetailsLink {color:#' . $urlColor . ' !important;}
            </style>'."\n";
            
            //Define paths for portability
            
            
            $hide = "\n".'<style type="text/css">
                    div#cookieMessageContainer{display:none}
                </style>'."\n";
            
            $SCRIPTS_FOLDER = JURI::root() . 'plugins/system/EUCookieDirective/EUCookieDirective/';
            $cookiescript = '<script type="text/javascript" src="' . $SCRIPTS_FOLDER . 'EUCookieDirective.js"></script>'."\n";
           
            $strOutputHTML = "";
            $strOutputHTML .= '<div id="cookieMessageOuter" style="width:100%">';
            $strOutputHTML .= '<div id="cookieMessageContainer" style="width:' . $width . ';background-color:#' . $backgroundColor . ';color:#fff">';
            $strOutputHTML .= '<table width="100%">';
            $strOutputHTML .= '<tr>';
            $strOutputHTML .= '<td colspan="2">';
            $strOutputHTML .= '<div id="cookieMessageText" style="padding:6px 10px 0 15px;">';
            $strOutputHTML .= '<p style="color:#fff;">' . $message . ' ' . $cookieDetailsText . ' <a id="cookieMessageDetailsLink" style="color:#fff; text-decoration: underline;" title="' . $cookiedetaillinktitle .'" href="' . $privacyLink . '">' . $cookieDetailsLinkText . '</a>.</p>';
            $strOutputHTML .= '</div>';
            $strOutputHTML .= '</td>';
            $strOutputHTML .= '</tr>';

            $strOutputHTML .= '<tr>';
            $strOutputHTML .= '<td>';
            $strOutputHTML .= '<span class="accept"><span class="cookieMessageText">' . $cookieAcceptanceLabel . '</span></span></label> ';			
            $strOutputHTML .= '<div border="0" class="cookie_button" id="continue_button" onclick="SetCookie(\'cookieAcceptanceCookie\',\'accepted\',9999);">' . $buttonText . '</div>';
            $strOutputHTML .= '</p></td>';
            $strOutputHTML .= '<td align="right">';
			
			if ($infoIcon == '1') {				
				$strOutputHTML .= '<div id="cookieMessageInformationIcon" style="float:right"><a href="http://www.channeldigital.co.uk/developer-resources/eu-cookie-directive-module.html" target="_blank" title="Open ' . $infoiconaltext . ' in a new tab or window"><img id="info_icon" src="' . $ICON_FOLDER . 'info_icon.png" alt="' . $infoiconaltext . '" border="0" width="20" height="20" /></a></div>';
			}
			
            $strOutputHTML .= '</td>';
            $strOutputHTML .= '</tr>';
            $strOutputHTML .= '</table>';
            $strOutputHTML .= '</div>';
            $strOutputHTML .= '</div>';
            
            //Only write the HTML Output if the cookie has not been set as "accepted"
            if(!isset($_COOKIE['cookieAcceptanceCookie']) || $_COOKIE['cookieAcceptanceCookie'] != "accepted")
            { 
                $body = JResponse::getBody();
                $body = str_replace('</head>', $style.'</head>', $body);
                $body = str_replace('</body>', $strOutputHTML.$cookiescript.'</body>', $body);
                JResponse::setBody($body);
            }
            elseif($_COOKIE['cookieAcceptanceCookie'] == "accepted"){
                $body = JResponse::getBody();
                $body = str_replace('</head>', $hide.'</head>', $body);
                JResponse::setBody($body);
            }

        }
    }

	function onAfterDispatch() {
	}

	function onBeforeRender() {
		$app = JFactory::getApplication();
		if ($app->isAdmin()) return;
		if (JRequest::getVar('option', false) == 'com_users' && JRequest::getVar('view', false) == 'registration') swiss::killMessage('Registration failed');
		// $doc = JFactory::getDocument();
		// $doc->addStyleDeclaration('dd.warning ul{display:none;}');
	}

	function onSearch() {
	}

	function onSearchAreas() {
	}

	function onGetWebServices() {
	}

}