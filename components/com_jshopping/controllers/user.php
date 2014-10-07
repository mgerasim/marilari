<?php
/**
* @version      4.5.0 31.05.2014
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class JshoppingControllerUser extends JControllerLegacy{
    
    function display($cachable = false, $urlparams = false){
        $this->myaccount();
    }
    
    function login(){
        $jshopConfig = JSFactory::getConfig();
        $session = JFactory::getSession();
        $mainframe = JFactory::getApplication();
        $params = $mainframe->getParams();
        
        $user = JFactory::getUser();
        if ($user->id){            
            $view_name = "user";
            $view_config = array("template_path"=>JPATH_COMPONENT."/templates/".$jshopConfig->template."/".$view_name);
            $view = $this->getView($view_name, getDocumentType(), '', $view_config);
            $view->setLayout("logout");            
            $view->display();
            return 0;
        }
   
        if (JRequest::getVar('return')){
            $return = JRequest::getVar('return');
        }else{
            $return = $session->get('return');
        }
        
        $show_pay_without_reg = $session->get("show_pay_without_reg");
        
        $seo = JTable::getInstance("seo", "jshop");
        $seodata = $seo->loadData("login");
        if (getThisURLMainPageShop()){
            appendPathWay(_JSHOP_LOGIN);
            if ($seodata->title==""){
                $seodata->title = _JSHOP_LOGIN;
            }
            setMetaData($seodata->title, $seodata->keyword, $seodata->description);
        }else{
            setMetaData($seodata->title, $seodata->keyword, $seodata->description, $params);
        }

        $country = JTable::getInstance('country', 'jshop');
        $list_country = $country->getAllCountries();
        $option_country[] = JHTML::_('select.option',  '0', _JSHOP_REG_SELECT, 'country_id', 'name' );    
        $select_countries = JHTML::_('select.genericlist', array_merge($option_country, $list_country),'country','id = "country" class = "inputbox" size = "1"','country_id','name' );
        foreach ($jshopConfig->user_field_title as $key => $value) {        
            $option_title[] = JHTML::_('select.option', $key, $value, 'title_id', 'title_name' );    
        }
        $select_titles = JHTML::_('select.genericlist', $option_title,'title','class = "inputbox"','title_id','title_name' );
        
        $client_types = array();
        foreach ($jshopConfig->user_field_client_type as $key => $value) {        
            $client_types[] = JHTML::_('select.option', $key, $value, 'id', 'name' );
        }
        $select_client_types = JHTML::_('select.genericlist', $client_types,'client_type','class = "inputbox" onchange="showHideFieldFirm(this.value)"','id','name');
        
        $tmp_fields = $jshopConfig->getListFieldsRegister();
        $config_fields = $tmp_fields['register'];
        
        JPluginHelper::importPlugin('jshoppingcheckout');
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayLogin', array() );
		if ($jshopConfig->show_registerform_in_logintemplate){
            $dispatcher->trigger('onBeforeDisplayRegister', array());
        }
		if ($jshopConfig->show_registerform_in_logintemplate && $config_fields['birthday']['display']){
            JHTML::_('behavior.calendar');
        }
        $view_name = "user";
        $view_config = array("template_path"=>JPATH_COMPONENT."/templates/".$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout("login");
        $view->assign('href_register', SEFLink('index.php?option=com_jshopping&controller=user&task=register',1,0, $jshopConfig->use_ssl));
        $view->assign('href_lost_pass', SEFLInk('index.php?option=com_users&view=reset',0,0, $jshopConfig->use_ssl));
        $view->assign('return', $return);
        $view->assign('Itemid', JRequest::getVar('Itemid'));
        $view->assign('config', $jshopConfig);
        $view->assign('show_pay_without_reg', $show_pay_without_reg);
        $view->assign('select_client_types', $select_client_types);
        $view->assign('select_titles', $select_titles);
        $view->assign('select_countries', $select_countries);
        $view->assign('config_fields', $config_fields);
        $view->assign('live_path', JURI::base());
        $view->assign('urlcheckdata', SEFLink("index.php?option=com_jshopping&controller=user&task=check_user_exist_ajax&ajax=1", 1, 1, $jshopConfig->use_ssl));
        $dispatcher->trigger('onBeforeDisplayLoginView', array(&$view));
		if ($jshopConfig->show_registerform_in_logintemplate){
            $dispatcher->trigger('onBeforeDisplayRegisterView', array(&$view));
        }
        $view->display();
    }
    
    function loginsave(){
        $jshopConfig = JSFactory::getConfig(); 
        $mainframe = JFactory::getApplication();
        JPluginHelper::importPlugin('jshoppingcheckout');
        $dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onBeforeLoginSave', array());
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
       
        if ($return = JRequest::getVar('return', '', 'method', 'base64')) {
            $return = base64_decode($return);
            if (!JURI::isInternal($return)) {
                $return = '';
            }
        }

        $options = array();
        $options['remember'] = JRequest::getBool('remember', false);
        $options['return'] = $return;

        $credentials = array();
        $credentials['username'] = JRequest::getVar('username', '', 'method', 'username');
        $credentials['password'] = JRequest::getString('passwd', '', 'post', JREQUEST_ALLOWRAW);
        
        $dispatcher->trigger( 'onBeforeLogin', array(&$options, &$credentials) );
        
        $error = $mainframe->login($credentials, $options);
        
        setNextUpdatePrices();        

        if ((!JError::isError($error)) && ($error !== FALSE)){
            if ( ! $return ) {
                $return = JURI::base();
            }                        
            $dispatcher->trigger( 'onAfterLogin', array() );            
            $mainframe->redirect( $return );
        }else{
            $dispatcher->trigger( 'onAfterLoginEror', array() );
            $mainframe->redirect( SEFLink('index.php?option=com_jshopping&controller=user&task=login&return='.base64_encode($return),0,1,$jshopConfig->use_ssl) );
        }
    }
    
    function check_user_exist_ajax() {
        $mes = array();
        $username = JRequest::getVar("username");
        $email = JRequest::getVar("email");
        $db = JFactory::getDBO();
		if ($username){
			$query = "SELECT id FROM `#__users` WHERE username = '".$db->escape($username)."'";
			$db->setQuery($query);
			$db->query();
			if ($db->getNumRows()){ 
				$mes[] = sprintf(_JSHOP_USER_EXIST, $username);
			}
		}
        if ($email){
			$query = "SELECT id FROM `#__users` WHERE email = '".$db->escape($email). "'";
			$db->setQuery($query);
			$db->query();
			if ($db->getNumRows()){ 
				$mes[] = sprintf(_JSHOP_USER_EXIST_EMAIL, $email);
			}
		}

        if (count($mes)==0){
            print "1";
        }else{
            print implode("\n",$mes);
        }
        die();
    }
    
    function register(){
        $jshopConfig = JSFactory::getConfig();
        $db = JFactory::getDBO(); 
        $mainframe = JFactory::getApplication();
        $params = $mainframe->getParams();
        
        $seo = JTable::getInstance("seo", "jshop");
        $seodata = $seo->loadData("register");
        if (getThisURLMainPageShop()){
            appendPathWay(_JSHOP_REGISTRATION);
            if ($seodata->title==""){
                $seodata->title = _JSHOP_REGISTRATION;
            }
            setMetaData($seodata->title, $seodata->keyword, $seodata->description);
        }else{
            setMetaData($seodata->title, $seodata->keyword, $seodata->description, $params);
        }

        $usersConfig = JComponentHelper::getParams( 'com_users' );
        if ($usersConfig->get('allowUserRegistration') == '0') {
            JError::raiseError( 403, JText::_( 'Access Forbidden' ));
            return;
        }        
        
        $country = JTable::getInstance('country', 'jshop');
        $list_country = $country->getAllCountries();
        $option_country[] = JHTML::_('select.option',  '0', _JSHOP_REG_SELECT, 'country_id', 'name' );    
        $select_countries = JHTML::_('select.genericlist', array_merge($option_country, $list_country),'country','id = "country" class = "inputbox" size = "1"','country_id','name', $jshopConfig->default_country );
        foreach($jshopConfig->user_field_title as $key => $value){
            $option_title[] = JHTML::_('select.option', $key, $value, 'title_id', 'title_name' );
        }
        $select_titles = JHTML::_('select.genericlist', $option_title,'title','class = "inputbox"','title_id','title_name' );
        
        $client_types = array();
        foreach ($jshopConfig->user_field_client_type as $key => $value) {        
            $client_types[] = JHTML::_('select.option', $key, $value, 'id', 'name' );
        }
        $select_client_types = JHTML::_('select.genericlist', $client_types,'client_type','class = "inputbox" onchange="showHideFieldFirm(this.value)"','id','name');
        
        $tmp_fields = $jshopConfig->getListFieldsRegister();
        $config_fields = $tmp_fields['register'];
        
        JPluginHelper::importPlugin('jshoppingcheckout');
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayRegister', array() );
        
		if ($config_fields['birthday']['display']){
            JHTML::_('behavior.calendar');
        }
        $view_name = "user";
        $view_config = array("template_path"=>JPATH_COMPONENT."/templates/".$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout("register"); 
        $view->assign('config', $jshopConfig);
        $view->assign('select_client_types', $select_client_types);
        $view->assign('select_titles', $select_titles);
        $view->assign('select_countries', $select_countries);
        $view->assign('config_fields', $config_fields);
        $view->assign('live_path', JURI::base());        
        $view->assign('urlcheckdata', SEFLink("index.php?option=com_jshopping&controller=user&task=check_user_exist_ajax&ajax=1",1,1));        
        $dispatcher->trigger('onBeforeDisplayRegisterView', array(&$view));
        $view->display();
    }
    
    function registersave(){
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        $mainframe = JFactory::getApplication();
        $jshopConfig = JSFactory::getConfig();
        $config = JFactory::getConfig();
        $db = JFactory::getDBO();
        $params = JComponentHelper::getParams('com_users');
        $lang = JFactory::getLanguage();
        $lang->load('com_users');
        $post = JRequest::get('post');
        
        JPluginHelper::importPlugin('jshoppingcheckout');
        $dispatcher = JDispatcher::getInstance();

        if ($params->get('allowUserRegistration')==0){
            JError::raiseError( 403, JText::_('Access Forbidden'));
            return;
        }
        
        $usergroup = JTable::getInstance('usergroup', 'jshop');
        $default_usergroup = $usergroup->getDefaultUsergroup();
        $post['username'] = $post['u_name'];
        $post['password2'] = $post['password_2'];
        if ($post['f_name']=="") $post['f_name'] = $post['email'];
        $post['name'] = $post['f_name'].' '.$post['l_name'];
        if ($post['birthday']) $post['birthday'] = getJsDateDB($post['birthday'], $jshopConfig->field_birthday_format);
        
        $dispatcher->trigger('onBeforeRegister', array(&$post, &$default_usergroup));
        
        $row = JTable::getInstance('userShop', 'jshop');
        $row->bind($post);
        $row->usergroup_id = $default_usergroup;
        $row->password = $post['password'];
        $row->password2 = $post['password2'];
        
        if (!$row->check("register")){
            JError::raiseWarning('', $row->getError());
            $this->setRedirect(SEFLink("index.php?option=com_jshopping&controller=user&task=register",1,1, $jshopConfig->use_ssl));
            return 0;
        }
        if ($post["u_name"]==""){
            $post["u_name"] = $post['email'];
            $row->u_name = $post["u_name"];
        }
        if ($post["password"]==""){
            $post["password"] = substr(md5('up'.time()), 0, 8);
        }
        $user = new JUser;
        $data = array();
        $data['groups'][] = $params->get('new_usertype', 2);
        $data['email'] = $post['email'];
        $data['password'] = $post['password'];
        $data['password2'] = $post['password2'];
        $data['name'] = $post['f_name'].' '.$post['l_name'];
        $data['username'] = $post["u_name"];
        $useractivation = $params->get('useractivation');
        $sendpassword = $params->get('sendpassword', 1);

        if (($useractivation == 1) || ($useractivation == 2)){
            jimport('joomla.user.helper');
            $data['activation'] = JApplication::getHash(JUserHelper::genRandomPassword());
            $data['block'] = 1;
        }
        $user->bind($data);
        $user->save();
        
        $row->user_id = $user->id;		
        $row->number = $row->getNewUserNumber();
        unset($row->password);
        unset($row->password2);
        if (!$db->insertObject($row->getTableName(), $row, $row->getKeyName())){
            JError::raiseWarning('', "Error insert in table ".$row->getTableName());
            $this->setRedirect(SEFLink("index.php?option=com_jshopping&controller=user&task=register",1,1,$jshopConfig->use_ssl));
            return 0;
        }

        $data = $user->getProperties();
        $data['fromname'] = $config->get('fromname');
        $data['mailfrom'] = $config->get('mailfrom');
        $data['sitename'] = $config->get('sitename');
        $data['siteurl'] = JUri::base();
        
        if ($useractivation == 2){
            $uri = JURI::getInstance();
            $base = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
            $data['activate'] = $base.JRoute::_('index.php?option=com_jshopping&controller=user&task=activate&token='.$data['activation'], false);

            $emailSubject = JText::sprintf(
                'COM_USERS_EMAIL_ACCOUNT_DETAILS',
                $data['name'],
                $data['sitename']
            );

			if ($sendpassword) {
				$emailBody = JText::sprintf(
					'COM_USERS_EMAIL_REGISTERED_WITH_ADMIN_ACTIVATION_BODY',
					$data['name'],
					$data['sitename'],
					$data['siteurl'].'index.php?option=com_jshopping&controller=user&task=activate&token='.$data['activation'],
					$data['siteurl'],
					$data['username'],
					$data['password_clear']
				);
			} else {
				$emailBody = JText::sprintf(
					'COM_USERS_EMAIL_REGISTERED_WITH_ADMIN_ACTIVATION_BODY_NOPW',
					$data['name'],
					$data['sitename'],
					$data['siteurl'].'index.php?option=com_jshopping&controller=user&task=activate&token='.$data['activation'],
					$data['siteurl'],
					$data['username']
				);
			}
        }else if ($useractivation == 1){
            $uri = JURI::getInstance();
            $base = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
            $data['activate'] = $base.JRoute::_('index.php?option=com_jshopping&controller=user&task=activate&token='.$data['activation'], false);

            $emailSubject = JText::sprintf(
                'COM_USERS_EMAIL_ACCOUNT_DETAILS',
                $data['name'],
                $data['sitename']
            );

			if ($sendpassword) {
				$emailBody = JText::sprintf(
					'COM_USERS_EMAIL_REGISTERED_WITH_ACTIVATION_BODY',
					$data['name'],
					$data['sitename'],
					$data['siteurl'].'index.php?option=com_jshopping&controller=user&task=activate&token='.$data['activation'],
					$data['siteurl'],
					$data['username'],
					$data['password_clear']
				);
			} else {
				$emailBody = JText::sprintf(
					'COM_USERS_EMAIL_REGISTERED_WITH_ACTIVATION_BODY_NOPW',
					$data['name'],
					$data['sitename'],
					$data['siteurl'].'index.php?option=com_jshopping&controller=user&task=activate&token='.$data['activation'],
					$data['siteurl'],
					$data['username']
				);
			}
        } else {
            $emailSubject = JText::sprintf(
                'COM_USERS_EMAIL_ACCOUNT_DETAILS',
                $data['name'],
                $data['sitename']
            );
            
            if ($sendpassword){
                $emailBody = JText::sprintf(
                    'COM_USERS_EMAIL_REGISTERED_BODY',
                    $data['name'],
                    $data['sitename'],
                    $data['siteurl'],
                    $data['username'],
                    $data['password_clear']
                );
            }else{
                $emailBody = JText::sprintf(
                    'COM_USERS_EMAIL_REGISTERED_BODY_NOPW',
                    $data['name'],
                    $data['sitename'],
                    $data['siteurl']
                );
            }
        }

		$dispatcher->trigger('onBeforeRegisterSendMailClient', array(&$post, &$data, &$emailSubject, &$emailBody));
		
        $mailer = JFactory::getMailer();
        $mailer->setSender(array($data['mailfrom'], $data['fromname']));
        $mailer->addRecipient($data['email']);
        $mailer->setSubject($emailSubject);
        $mailer->setBody($emailBody);
        $mailer->isHTML(false);
        $return = $mailer->Send();
        
        if (($params->get('useractivation') < 2) && ($params->get('mail_to_admin') == 1)){
            $emailSubject = JText::sprintf(
                'COM_USERS_EMAIL_ACCOUNT_DETAILS',
                $data['name'],
                $data['sitename']
            );

            $emailBodyAdmin = JText::sprintf(
                'COM_USERS_EMAIL_REGISTERED_NOTIFICATION_TO_ADMIN_BODY',
                $data['name'],
                $data['username'],
                $data['siteurl']
            );
                        
            $query = 'SELECT name, email, sendEmail FROM #__users WHERE sendEmail=1';
            $db->setQuery( $query );
            $rows = $db->loadObjectList();            
            foreach($rows as $rowadm){
				$dispatcher->trigger('onBeforeRegisterSendMailAdmin', array(&$post, &$data, &$emailSubject, &$emailBodyAdmin, &$rowadm));
                $return = JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $rowadm->email, $emailSubject, $emailBodyAdmin);
            }
        }
        
        $dispatcher->trigger('onAfterRegister', array(&$user, &$row, &$post, &$useractivation));
                
        if ( $useractivation == 2 ){
            $message  = JText::_('COM_USERS_REGISTRATION_COMPLETE_VERIFY');
            $return = SEFLink("index.php?option=com_jshopping&controller=user&task=login",1,1,$jshopConfig->use_ssl);
        } elseif ( $useractivation == 1 ){
            $message  = JText::_('COM_USERS_REGISTRATION_COMPLETE_ACTIVATE');
            $return = SEFLink("index.php?option=com_jshopping&controller=user&task=login",1,1,$jshopConfig->use_ssl);
        } else {
            $message = JText::_('COM_USERS_REGISTRATION_SAVE_SUCCESS');
            $return = SEFLink("index.php?option=com_jshopping&controller=user&task=login",1,1,$jshopConfig->use_ssl);
        }
        
        $this->setRedirect($return, $message);
    }
    
    public function activate(){
        $jshopConfig = JSFactory::getConfig();
        $user = JFactory::getUser();
        $uParams = JComponentHelper::getParams('com_users');
        $lang =  JFactory::getLanguage();
        $lang->load( 'com_users' );
        jimport('joomla.user.helper');

        if ($user->get('id')) {
            $this->setRedirect('index.php');
            return true;
        }

        if ($uParams->get('useractivation') == 0 || $uParams->get('allowUserRegistration') == 0) {
            JError::raiseError(403, JText::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN'));
            return false;
        }

        $model = JTable::getInstance('userShop', 'jshop');
        $token = JRequest::getVar('token', null, 'request', 'alnum');

        if ($token === null || strlen($token) !== 32) {
            JError::raiseError(403, JText::_('JINVALID_TOKEN'));
            return false;
        }

        $return = $model->activate($token);

        if ($return === false) {
            $this->setMessage(JText::sprintf('COM_USERS_REGISTRATION_SAVE_FAILED', $model->getError()), 'warning');
            $this->setRedirect('index.php');
            return false;
        }

        $useractivation = $uParams->get('useractivation');

        if ($useractivation == 0){
            $this->setMessage(JText::_('COM_USERS_REGISTRATION_SAVE_SUCCESS'));
            $this->setRedirect(SEFLink("index.php?option=com_jshopping&controller=user&task=login",0,1,$jshopConfig->use_ssl));
        }elseif ($useractivation == 1){
            $this->setMessage(JText::_('COM_USERS_REGISTRATION_ACTIVATE_SUCCESS'));
            $this->setRedirect(SEFLink("index.php?option=com_jshopping&controller=user&task=login",0,1,$jshopConfig->use_ssl));
        }elseif ($return->getParam('activate')){
            $this->setMessage(JText::_('COM_USERS_REGISTRATION_VERIFY_SUCCESS'));
            $this->setRedirect(SEFLink("index.php?option=com_jshopping&controller=user&task=login",0,1,$jshopConfig->use_ssl));
        }else{
            $this->setMessage(JText::_('COM_USERS_REGISTRATION_ADMINACTIVATE_SUCCESS'));
            $this->setRedirect(SEFLink("index.php?option=com_jshopping&controller=user&task=login",0,1,$jshopConfig->use_ssl));
        }
        return true;
    }
    
    function editaccount(){
        checkUserLogin();
        $user = JFactory::getUser();
        $adv_user = JSFactory::getUserShop();
        $jshopConfig = JSFactory::getConfig();
        $mainframe = JFactory::getApplication();
        $params = $mainframe->getParams();
            
        appendPathWay(_JSHOP_EDIT_DATA);        
        $seo = JTable::getInstance("seo", "jshop");
        $seodata = $seo->loadData("editaccount");
        if ($seodata->title==""){
            $seodata->title = _JSHOP_EDIT_DATA;
        }
        setMetaData($seodata->title, $seodata->keyword, $seodata->description);    
        
        if (!$adv_user->country) $adv_user->country = $jshopConfig->default_country;
        if (!$adv_user->d_country) $adv_user->d_country = $jshopConfig->default_country;
        $adv_user->birthday = getDisplayDate($adv_user->birthday, $jshopConfig->field_birthday_format);
        $adv_user->d_birthday = getDisplayDate($adv_user->d_birthday, $jshopConfig->field_birthday_format);
        
        $country = JTable::getInstance('country', 'jshop');
        $list_country = $country->getAllCountries();
        $option_country[] = JHTML::_('select.option', 0, _JSHOP_REG_SELECT, 'country_id', 'name' );
        $option_countryes = array_merge($option_country, $list_country);
        $select_countries = JHTML::_('select.genericlist', $option_countryes,'country','class = "inputbox" size = "1"','country_id', 'name',$adv_user->country );
        $select_d_countries = JHTML::_('select.genericlist', $option_countryes,'d_country','class = "inputbox" size = "1"','country_id', 'name',$adv_user->d_country );

        foreach($jshopConfig->user_field_title as $key => $value){
            $option_title[] = JHTML::_('select.option', $key, $value, 'title_id', 'title_name' );
        }    
        $select_titles = JHTML::_('select.genericlist', $option_title,'title','class = "inputbox"','title_id','title_name',$adv_user->title );
        $select_d_titles = JHTML::_('select.genericlist', $option_title,'d_title','class = "inputbox"','title_id','title_name',$adv_user->d_title );
        
        $client_types = array();
        foreach ($jshopConfig->user_field_client_type as $key => $value) {        
            $client_types[] = JHTML::_('select.option', $key, $value, 'id', 'name' );
        }
        $select_client_types = JHTML::_('select.genericlist', $client_types,'client_type','class = "inputbox" onchange="showHideFieldFirm(this.value)"','id','name', $adv_user->client_type);
                
        $tmp_fields = $jshopConfig->getListFieldsRegister();
        $config_fields = $tmp_fields['editaccount'];
        $count_filed_delivery = $jshopConfig->getEnableDeliveryFiledRegistration('editaccount');
        
        JPluginHelper::importPlugin('jshoppingcheckout');
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforeDisplayEditUser', array(&$adv_user) );
        
        filterHTMLSafe( $adv_user, ENT_QUOTES);        
        
		if ($config_fields['birthday']['display'] || $config_fields['d_birthday']['display']){
            JHTML::_('behavior.calendar');
        }
        $view_name = "user";
        $view_config = array("template_path"=>JPATH_COMPONENT."/templates/".$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout("editaccount");        
		$view->assign('config',$jshopConfig);
        $view->assign('select_countries',$select_countries);
        $view->assign('select_d_countries',$select_d_countries);
        $view->assign('select_titles',$select_titles);
        $view->assign('select_d_titles',$select_d_titles);
        $view->assign('select_client_types', $select_client_types);
        $view->assign('live_path', JURI::base());
        $view->assign('user', $adv_user);
        $view->assign('delivery_adress', $adv_user->delivery_adress);
        $view->assign('action', SEFLink('index.php?option=com_jshopping&controller=user&task=accountsave',0,0,$jshopConfig->use_ssl));
        $view->assign('config_fields', $config_fields);
        $view->assign('count_filed_delivery', $count_filed_delivery);
        $dispatcher->trigger('onBeforeDisplayEditAccountView', array(&$view));
        $view->display();
    }
    
    function accountsave(){
        checkUserLogin();
        $user = JFactory::getUser();
        $db = JFactory::getDBO();
        $app = JFactory::getApplication();
        JPluginHelper::importPlugin('jshoppingcheckout');
        $dispatcher = JDispatcher::getInstance();
        $jshopConfig = JSFactory::getConfig();
        
        $user_shop = JTable::getInstance('userShop', 'jshop');        
        $post = JRequest::get('post');
        if (!isset($post['password'])) $post['password'] = '';
        if (!isset($post['password_2'])) $post['password_2'] = '';
        if ($post['birthday']) $post['birthday'] = getJsDateDB($post['birthday'], $jshopConfig->field_birthday_format);
        if ($post['d_birthday']) $post['d_birthday'] = getJsDateDB($post['d_birthday'], $jshopConfig->field_birthday_format);
        
        $dispatcher->trigger('onBeforeAccountSave', array(&$post));
        
        unset($post['user_id']);
        unset($post['usergroup_id']);
        $user_shop->load($user->id);
        $user_shop->bind($post);
        $user_shop->password = $post['password'];
        $user_shop->password2 = $post['password_2'];

        if (!$user_shop->check("editaccount")) {
            JError::raiseWarning('',$user_shop->getError());
            $this->setRedirect(SEFLink("index.php?option=com_jshopping&controller=user&task=editaccount",0,1,$jshopConfig->use_ssl));
            return 0;
        }
        unset($user_shop->password);
        unset($user_shop->password2);        

        if (!$user_shop->store()){
            JError::raiseWarning(500,_JSHOP_REGWARN_ERROR_DATABASE);
            $this->setRedirect(SEFLink("index.php?option=com_jshopping&controller=user&task=editaccount",0,1,$jshopConfig->use_ssl));
            return 0;
        }
        
        $user = new JUser($user->id);
		if (!$jshopConfig->not_update_user_joomla){
			if ($user_shop->email){
				$user->email = $user_shop->email;
			}
			if ($user_shop->f_name || $user_shop->l_name){
				$user->name = $user_shop->f_name.' '.$user_shop->l_name;
			}
		}
        if ($post['password']!=''){
            $data = array("password"=>$post['password'], "password2"=>$post['password']);
            $user->bind($data);
        }
        $user->save();
        
        $data = array();
        $data['email'] = $user->email;
        $data['name'] = $user->name;
        $app->setUserState('com_users.edit.profile.data', $data);
        
        setNextUpdatePrices();
        $dispatcher->trigger('onAfterAccountSave', array());
                
        $this->setRedirect(SEFLink("index.php?option=com_jshopping&controller=user&task=myaccount",0,1,$jshopConfig->use_ssl), _JSHOP_ACCOUNT_UPDATE);
    }
    
    function orders(){
        $jshopConfig = JSFactory::getConfig();
        checkUserLogin();
        $user = JFactory::getUser();
        $order = JTable::getInstance('order', 'jshop');
        
        appendPathWay(_JSHOP_MY_ORDERS);
        $seo = JTable::getInstance("seo", "jshop");
        $seodata = $seo->loadData("myorders");
        if ($seodata->title==""){
            $seodata->title = _JSHOP_MY_ORDERS;
        }
        setMetaData($seodata->title, $seodata->keyword, $seodata->description);
        
        $orders = $order->getOrdersForUser($user->id);
        $total = 0;
        foreach($orders as $key=>$value){
            $orders[$key]->order_href = SEFLink('index.php?option=com_jshopping&controller=user&task=order&order_id='.$value->order_id,0,0,$jshopConfig->use_ssl);
            $total += $value->order_total / $value->currency_exchange;
        }

        JPluginHelper::importPlugin('jshoppingorder');
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforeDisplayListOrder', array(&$orders) );
        
        $view_name = "order";
        $view_config = array("template_path"=>JPATH_COMPONENT."/templates/".$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout("listorder");
        $view->assign('orders', $orders);
        $view->assign('image_path', $jshopConfig->live_path."images");
        $view->assign('total', $total);
        $dispatcher->trigger('onBeforeDisplayOrdersView', array(&$view));
        $view->display();
    }
    
    function order(){
        $jshopConfig = JSFactory::getConfig();
        checkUserLogin();
        $db = JFactory::getDBO(); 
        $user = JFactory::getUser();
        $lang = JSFactory::getLang();
        JPluginHelper::importPlugin('jshoppingorder');
        $dispatcher = JDispatcher::getInstance();
        
        appendPathWay(_JSHOP_MY_ORDERS, SEFLink('index.php?option=com_jshopping&controller=user&task=orders',0,0,$jshopConfig->use_ssl));
        
        $seo = JTable::getInstance("seo", "jshop");
        $seodata = $seo->loadData("myorder-detail");
        if ($seodata->title==""){
            $seodata->title = _JSHOP_MY_ORDERS;
        }
        setMetaData($seodata->title, $seodata->keyword, $seodata->description);
        
        $order_id = JRequest::getInt('order_id');
        $order = JTable::getInstance('order', 'jshop');
        $order->load($order_id);
        $dispatcher->trigger('onAfterLoadOrder', array(&$order, &$user));
        
        appendPathWay(_JSHOP_ORDER_NUMBER.": ".$order->order_number);
        
        if ($user->id!=$order->user_id){
            JError::raiseError( 500, "Error order number. You are not the owner of this order");
        }
        
        $order->items = $order->getAllItems();
        $order->weight = $order->getWeightItems();
        $order->status_name = $order->getStatus();
        $order->history = $order->getHistory();
        if ($jshopConfig->client_allow_cancel_order && $order->order_status!=$jshopConfig->payment_status_for_cancel_client && !in_array($order->order_status, $jshopConfig->payment_status_disable_cancel_client) ){
            $allow_cancel = 1;
        }else{
            $allow_cancel = 0;
        }
		
		$order->birthday = getDisplayDate($order->birthday, $jshopConfig->field_birthday_format);
        $order->d_birthday = getDisplayDate($order->d_birthday, $jshopConfig->field_birthday_format);
        
        $shipping_method =JTable::getInstance('shippingMethod', 'jshop');
        $shipping_method->load($order->shipping_method_id);
        
        $name = $lang->get("name");
        $description = $lang->get("description");
        $order->shipping_info = $shipping_method->$name;
        
        $pm_method = JTable::getInstance('paymentMethod', 'jshop');
        $pm_method->load($order->payment_method_id);
        $order->payment_name = $pm_method->$name;
        if ($pm_method->show_descr_in_email) $order->payment_description = $pm_method->$description;  else $order->payment_description = "";
        
        $country = JTable::getInstance('country', 'jshop');
        $country->load($order->country);
        $field_country_name = $lang->get("name");
        $order->country = $country->$field_country_name;
        
        $d_country = JTable::getInstance('country', 'jshop');
        $d_country->load($order->d_country);
        $field_country_name = $lang->get("name");
        $order->d_country = $d_country->$field_country_name;
        
        $jshopConfig->user_field_client_type[0]="";
        $order->client_type_name = $jshopConfig->user_field_client_type[$order->client_type];
        
        $order->delivery_time_name = '';
        $order->delivery_date_f = '';
        if ($jshopConfig->show_delivery_time_checkout){
            $deliverytimes = JSFactory::getAllDeliveryTime();
            $order->delivery_time_name = $deliverytimes[$order->delivery_times_id];
            if ($order->delivery_time_name==""){
                $order->delivery_time_name = $order->delivery_time;
            }
        }
        if ($jshopConfig->show_delivery_date && !datenull($order->delivery_date)){
            $order->delivery_date_f = formatdate($order->delivery_date);
        }
        
        $order->order_tax_list = $order->getTaxExt();
        $show_percent_tax = 0;
        if (count($order->order_tax_list)>1 || $jshopConfig->show_tax_in_product) $show_percent_tax = 1;
        if ($jshopConfig->hide_tax) $show_percent_tax = 0;
        $hide_subtotal = 0;
        if (($jshopConfig->hide_tax || count($order->order_tax_list)==0) && $order->order_discount==0 && $order->order_payment==0 && $jshopConfig->without_shipping) $hide_subtotal = 1;
        
        $text_total = _JSHOP_ENDTOTAL;
        if (($jshopConfig->show_tax_in_product || $jshopConfig->show_tax_product_in_cart) && (count($order->order_tax_list)>0)){
            $text_total = _JSHOP_ENDTOTAL_INKL_TAX;
        }
        
        $tmp_fields = $jshopConfig->getListFieldsRegister();
        $config_fields = $tmp_fields["address"];
        $count_filed_delivery = $jshopConfig->getEnableDeliveryFiledRegistration('address');

        if ($jshopConfig->order_display_new_digital_products){
            $product = JTable::getInstance('product', 'jshop');
            foreach($order->items as $k=>$v){
                $product->product_id = $v->product_id;
                $product->setAttributeActive(unserialize($v->attributes));
                $files = $product->getSaleFiles();
                $order->items[$k]->files = serialize($files);
            }
        }

        $dispatcher->trigger('onBeforeDisplayOrder', array(&$order));

        $view_name = "order";
        $view_config = array("template_path"=>JPATH_COMPONENT."/templates/".$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout("order");
        $view->assign('order', $order);
        $view->assign('config', $jshopConfig);
        $view->assign('text_total', $text_total);
        $view->assign('show_percent_tax', $show_percent_tax);
        $view->assign('hide_subtotal', $hide_subtotal);
        $view->assign('image_path', $jshopConfig->live_path . "images");
        $view->assign('config_fields', $config_fields);
        $view->assign('count_filed_delivery', $count_filed_delivery);
        $view->assign('allow_cancel', $allow_cancel);
        $dispatcher->trigger('onBeforeDisplayOrderView', array(&$view));
        $view->display();
    }
    
    function cancelorder(){
        $jshopConfig = JSFactory::getConfig();
        checkUserLogin();
        $db = JFactory::getDBO(); 
        $user = JFactory::getUser();
        $lang = JSFactory::getLang();
        $mainframe = JFactory::getApplication();
        
        if (!$jshopConfig->client_allow_cancel_order) return 0;
        
        $order_id = JRequest::getInt('order_id');
        
        $order = JTable::getInstance('order', 'jshop');
        $order->load($order_id);
        
        appendPathWay(_JSHOP_ORDER_NUMBER.": ".$order->order_number);
        
        if ($user->id!=$order->user_id){
            JError::raiseError( 500, "Error order number");
        }
        $status = $jshopConfig->payment_status_for_cancel_client;
        
        if ($order->order_status==$status || in_array($order->order_status, $jshopConfig->payment_status_disable_cancel_client)){
            $this->setRedirect(SEFLink("index.php?option=com_jshopping&controller=user&task=order&order_id=".$order_id,0,1,$jshopConfig->use_ssl));
            return 0;
        }
        
        $checkout = JModelLegacy::getInstance('checkout', 'jshop');
        $checkout->changeStatusOrder($order_id, $status, 1);
        
        JPluginHelper::importPlugin('jshoppingorder');
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onAfterUserCancelOrder', array(&$order_id));
        
        $this->setRedirect(SEFLink("index.php?option=com_jshopping&controller=user&task=order&order_id=".$order_id,0,1,$jshopConfig->use_ssl), _JSHOP_ORDER_CANCELED);
    }

    function myaccount(){
        $jshopConfig = JSFactory::getConfig();
        checkUserLogin();

        $user = JFactory::getUser();
        $adv_user = JSFactory::getUserShop();
        $lang = JSFactory::getLang();
        
        $country = JTable::getInstance('country', 'jshop');
        $country->load($adv_user->country);
        $field_country_name = $lang->get("name");
        $adv_user->country = $country->$field_country_name;
        
        $group = JTable::getInstance('userGroup', 'jshop');
        $group->load($adv_user->usergroup_id);
        $adv_user->groupname = $group->usergroup_name;
        $adv_user->discountpercent = floatval($group->usergroup_discount);

        $seo = JTable::getInstance("seo", "jshop");
        $seodata = $seo->loadData("myaccount");
        if ($seodata->title==""){
            $seodata->title = _JSHOP_MY_ACCOUNT;
        }
        setMetaData($seodata->title, $seodata->keyword, $seodata->description);
        
        $tmp_fields = $jshopConfig->getListFieldsRegister();
        $config_fields = $tmp_fields['editaccount'];
        
        JPluginHelper::importPlugin('jshoppingcheckout');
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforeDisplayMyAccount', array());

        $view_name = "user";
        $view_config = array("template_path"=>JPATH_COMPONENT."/templates/".$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout("myaccount");
        $view->assign('config', $jshopConfig);
        $view->assign('user', $adv_user);
        $view->assign('config_fields', $config_fields);
        $view->assign('href_user_group_info', SEFLink('index.php?option=com_jshopping&controller=user&task=groupsinfo'));
        $view->assign('href_edit_data', SEFLink('index.php?option=com_jshopping&controller=user&task=editaccount',0,0,$jshopConfig->use_ssl));
        $view->assign('href_show_orders', SEFLink('index.php?option=com_jshopping&controller=user&task=orders',0,0,$jshopConfig->use_ssl));
        $view->assign('href_logout', SEFLink('index.php?option=com_jshopping&controller=user&task=logout'));
        $dispatcher->trigger('onBeforeDisplayMyAccountView', array(&$view));
        $view->display();
    }
    
    function groupsinfo(){
        $jshopConfig = JSFactory::getConfig();
        setMetaData(_JSHOP_USER_GROUPS_INFO, "", "");
        
        $group = JTable::getInstance('userGroup', 'jshop');
        $list = $group->getList();
        
        JPluginHelper::importPlugin('jshoppingcheckout');
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayGroupsInfo', array());

        $view_name = "user";
        $view_config = array("template_path"=>JPATH_COMPONENT."/templates/".$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout("groupsinfo");
        $view->assign('rows', $list);
        $dispatcher->trigger('onBeforeDisplayGroupsInfoView', array(&$view));
        $view->display();
    }
    
    function logout(){
        $mainframe = JFactory::getApplication();
        JPluginHelper::importPlugin('jshoppingcheckout');
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforeLogout', array() );

        $error = $mainframe->logout();

        $session = JFactory::getSession();
        $session->set('user_shop_guest', null);
        $session->set('cart', null);

        if (!JError::isError($error)){
            if ($return = JRequest::getVar('return', '', 'method', 'base64')) {
                $return = base64_decode($return);
                if (!JURI::isInternal($return)) {
                    $return = '';
                }
            }

            setNextUpdatePrices();

            $dispatcher->trigger( 'onAfterLogout', array() );

            if ( $return && !( strpos( $return, 'com_user' )) ) {
                $mainframe->redirect( $return );
            }else{
                $mainframe->redirect(JURI::base());
            }
        }
    }
    
}
?>