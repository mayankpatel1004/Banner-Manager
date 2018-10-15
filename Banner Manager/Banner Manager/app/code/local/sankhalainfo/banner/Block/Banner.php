<?php
class Sankhalainfo_Banner_Block_Banner extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getBanner()     
     { 
        if (!$this->hasData('banner')) {
            $this->setData('banner', Mage::registry('banner'));
        }
        return $this->getData('banner');
        
    }
	
	
	
	public function getBanners()     
     { 
        $objAllbanners = Mage::getModel('banner/banner')->getCollection()->addFieldToFilter('status',1)->addFieldToFilter('position','main');
		
        return $objAllbanners;
        
    }
	
	public function getBannerInfo($strFileName)
	{
		$strBannerUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'banners/'.$strFileName;
		list($intWidth,$intHeight) = getimagesize($strBannerUrl);
		$boolHomePage = false;
		$intMaxWidth = 953;		
		
		if($intWidth > $intMaxWidth)
		{
			$intHeight = ($intHeight*$intMaxWidth)/$intWidth;
			$intWidth = $intMaxWidth;
		}
		$arrBannerInfo['url'] = $strBannerUrl;
		$arrBannerInfo['height'] = $intHeight;
		$arrBannerInfo['width'] = $intWidth;
		return $arrBannerInfo;
	}
	
	public function getRandomBanner()
	{		
		$objRandomBanner = Mage::getModel('banner/banner')->getCollection()->addFieldToFilter('status',1);
		$objRandomBanner->getSelect()->order('rand()');
		$objRandomBanner->getSelect()->limit(1);
		return $objRandomBanner;
	}
	
	public function getBannerUrl($strUrl)
	{
		if(substr($strUrl,0,4) != 'http')
		{
			$strUrl = 'http://'.$strUrl;
		}
		return $strUrl;
	}
	public function fnGetRandomString($length = 8, $seeds = 'alphanum')
	{
		
		// Possible seeds
		$seedings['alpha'] = 'abcdefghijklmnopqrstuvwqyz';
		$seedings['numeric'] = '0123456789';
		$seedings['alphanum'] = 'abcdefghijklmnopqrstuvwqyz0123456789';
		$seedings['hexidec'] = '0123456789abcdef';
		
		// Choose seed
		if (isset($seedings[$seeds]))
		{
			$seeds = $seedings[$seeds];
		}
		
		// Seed generator
		list($usec, $sec) = explode(' ', microtime());
		$seed = (float) $sec + ((float) $usec * 100000);
		mt_srand($seed);
		
		// Generate
		$str = '';
		$seeds_count = strlen($seeds);
		
		for ($i = 0; $length > $i; $i++)
		{
			$str .= $seeds{mt_rand(0, $seeds_count - 1)};
		}
		
		return $str;
	}
	
}