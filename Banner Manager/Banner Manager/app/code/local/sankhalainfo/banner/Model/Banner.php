<?php

class Sankhalainfo_Banner_Model_Banner extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('banner/banner');
    }
	
	public function getSideBanners()     
     { 
        $objAllbanners = $this->getCollection()->addFieldToFilter('status',1)->addFieldToFilter('position','side');
		
        return $objAllbanners;
        
    }
	
	public function fnGetLastOrders()
	{
		$intLastOrders = Mage::getStoreConfig('banner/whatother_buying/lastorders');
		
		$objOrderCollection = Mage::getModel('sales/order')->getCollection()->setOrder('entity_id','DESC')->setPage(1,$intLastOrders);
		$arrOrderProducts = array();
		if($objOrderCollection->count())
		{
			foreach($objOrderCollection as $objSpecOrder)
			{
				$objOrderItems = $objSpecOrder->getItemsCollection();
				if($objOrderItems->count())
				{
					foreach($objOrderItems as $objSpecItem)
					{
						$arrOrderProducts[] = $objSpecItem->getProductId();
					}
				}
			}		
			$arrOrderProducts = array_unique($arrOrderProducts);
		}
		return $arrOrderProducts;
	}
	
	public function getBannerUrl($strUrl)
	{
		if(substr($strUrl,0,4) != 'http')
		{
			$strUrl = 'http://'.$strUrl;
		}
		return $strUrl;
	}

	public function fnUpdateReviewCityState($arrPost, $intReviewId)
	{
		$objData = Mage::getSingleton('core/resource')->getConnection('core_write');
		$strReviewDetailTable = Mage::getSingleton('core/resource')->getTableName('review_detail');
		
		$sqlQuery = " UPDATE $strReviewDetailTable SET city = '".trim($arrPost['city'])."', state = '".trim($arrPost['state'])."' WHERE review_id = '".$intReviewId."'";
		$objData->query($sqlQuery);
	}
	
	public function fnGetReviewCityState($intReviewId)
	{
		$objData = Mage::getSingleton('core/resource')->getConnection('core_write');
		$strReviewDetailTable = Mage::getSingleton('core/resource')->getTableName('review_detail');
		
		$sqlQuery = " SELECT city, state FROM $strReviewDetailTable WHERE review_id = '".$intReviewId."'";
		$resReviewSpecDetail = $objData->query($sqlQuery);
		$arrReviewSpecDetail = $resReviewSpecDetail->fetch(PDO::FETCH_ASSOC);
		return $arrReviewSpecDetail;
	}
	
	public function getStateName($strStateCode)
	{
		$strState = '';
		$regionCollection = Mage::getModel('directory/region')->getResourceCollection()->addCountryFilter('IN')->load();
		foreach($regionCollection as $objRegion)
		{
			//echo "<br>===>".$objRegion->getCode()."++++++".$strStateCode;
			if($objRegion->getCode() == $strStateCode)			
			{
				$strState = $objRegion->getDefaultName();
				break; 
			}
		}
		return $strState;
	}
	protected function _getHelper()
    {
        return Mage::helper('sendfriend');
    }
	
	public function send()
    {
        /* @var $translate Mage_Core_Model_Translate */
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);

        /* @var $mailTemplate Mage_Core_Model_Email_Template */
        $mailTemplate = Mage::getModel('core/email_template');
		
		$arrSenderInfo = $this->getSender();
		$arrReceipientInfo = $this->getRecipients();
		
        $message = nl2br(htmlspecialchars(trim($arrSenderInfo['message'])));
        $sender  = array(
            'name'  => $this->_getHelper()->htmlEscape(trim($arrSenderInfo['name'])),
            'email' => $this->_getHelper()->htmlEscape(trim($arrSenderInfo['email']))
        );

        $mailTemplate->setDesignConfig(array(
            'area'  => 'frontend',
            'store' => Mage::app()->getStore()->getId()
        ));

        foreach ($arrReceipientInfo['email'] as $k => $email) {
            $name = $arrReceipientInfo['name'][$k];
            $mailTemplate->sendTransactional(
                Mage::getStoreConfig('custom/configuration/contactfriend_template'),
                $sender,
                $email,
                $name,
                array(
                    'name'          => $name,
                    'email'         => $email,                    
                    'message'       => $message,
                    'sender_name'   => $sender['name'],
                    'sender_email'  => $sender['email'],
                )
            );
        }

        $translate->setTranslateInline(true);
        return $this;
    }
}