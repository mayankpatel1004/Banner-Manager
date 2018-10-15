<?php
class Sankhalainfo_Banner_IndexController extends Mage_Core_Controller_Front_Action
{
	const XML_PATH_EMAIL_RECIPIENT  = 'contacts/email/recipient_email';
    const XML_PATH_EMAIL_SENDER     = 'contacts/email/sender_email_identity';
    const XML_PATH_DISTRIBUTOR_EMAIL_TEMPLATE   = 'banner/email_templates/distributor_template';

    public function indexAction()
    {
    	$this->loadLayout();     
		$this->renderLayout();
    }
	
	public function distributorpostAction()
    {
        $post = $this->getRequest()->getPost();
        if ( $post ) 
		{
			if( !(Mage::getStoreConfig("fontis_recaptcha/recaptcha/when_loggedin")  && (Mage::getSingleton('customer/session')->isLoggedIn())) )
        	{            
                $privatekey = Mage::getStoreConfig("fontis_recaptcha/setup/private_key");
                // check response
                $resp = Mage::helper("fontis_recaptcha")->recaptcha_check_answer(  $privatekey,
                                                                                    $_SERVER["REMOTE_ADDR"],
                                                                                    $_POST["recaptcha_challenge_field"],
                                                                                    $_POST["recaptcha_response_field"]
                                                                                );
                if ($resp == true)
                { // if recaptcha response is correct, use core functionality
                  
                }
                else
                { // if recaptcha response is incorrect, reload the page

                    Mage::getSingleton('customer/session')->addError(Mage::helper('contacts')->__('Your reCAPTCHA entry is incorrect. Please try again.'));
					 $this->_redirect('distributor');
	                return;
                    
                }
            }                   
		
            $translate = Mage::getSingleton('core/translate');
            /* @var $translate Mage_Core_Model_Translate */
            $translate->setTranslateInline(false);
            try {
                $postObject = new Varien_Object();
                $postObject->setData($post);

                $error = false;

                if (!Zend_Validate::is(trim($post['name']) , 'NotEmpty')) {
                    $error = true;
                }

                if (!Zend_Validate::is(trim($post['comment']) , 'NotEmpty')) {
                    $error = true;
                }

                if (!Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
                    $error = true;
                }

                if (Zend_Validate::is(trim($post['hideit']), 'NotEmpty')) {
                    $error = true;
                }

                if ($error) {
                    throw new Exception();
                }
                $mailTemplate = Mage::getModel('core/email_template');
                /* @var $mailTemplate Mage_Core_Model_Email_Template */
                $mailTemplate->setDesignConfig(array('area' => 'frontend'))
                    ->setReplyTo($post['email'])
                    ->sendTransactional(
                        Mage::getStoreConfig(self::XML_PATH_DISTRIBUTOR_EMAIL_TEMPLATE),
                        Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
                        Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT),
                        null,
                        array('data' => $postObject)
                    );

                if (!$mailTemplate->getSentSuccess()) {
                    throw new Exception();
                }

                $translate->setTranslateInline(true);

                Mage::getSingleton('customer/session')->addSuccess(Mage::helper('contacts')->__('Your inquiry was submitted and will be responded to as soon as possible. Thank you for Your Interest.'));
                $this->_redirect('distributor');

                return;
            } catch (Exception $e) {
                $translate->setTranslateInline(true);

                Mage::getSingleton('customer/session')->addError(Mage::helper('contacts')->__('Unable to submit your request. Please, try again later'));
                $this->_redirect('distributor');
                return;
            }

        } else {
            $this->_redirect('distributor');
        }
    }
	
	/*pubilc function applyshippingAction()
	{
		$data = $this->getRequest()->getPost();
		
		$country = $data['country_id'];
		$city = '';
		$regionId = $region = '';
		
		
		
		Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress()
		->setCountryId($country)
		->setCity($city)
		->setPostcode($postcode)
		->setRegionId($regionId)
		->setRegion($region)
		->setShippingMethod($code)
		->setShippingDescription('Free Shipping')
		->setCollectShippingRates(true);
		Mage::getSingleton('checkout/session')->getQuote()->save();
		$this->_redirect('checkout/cart');
	}
*/	
	public function sendmailAction()
    {      
        $model      = Mage::getModel('banner/banner');
        $data       = $this->getRequest()->getPost();
		
        if (!$data) {
            $this->_forward('noRoute');
            return;
        }

        $model->setSender($this->getRequest()->getPost('sender'));
        $model->setRecipients($this->getRequest()->getPost('recipients'));
		
        try 
		{            
          	 $model->send();
        }
        catch (Mage_Core_Exception $e) {
           
        }
    }
}