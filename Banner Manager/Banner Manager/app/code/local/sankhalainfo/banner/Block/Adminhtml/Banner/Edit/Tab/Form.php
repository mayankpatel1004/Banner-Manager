<?php

class Sankhalainfo_Banner_Block_Adminhtml_Banner_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('banner_form', array('legend'=>Mage::helper('banner')->__('Banner information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('banner')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('banner')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
	  
	   $fieldset->addField('position', 'select', array(
          'label'     => Mage::helper('banner')->__('Position'),
          'name'      => 'position',
          'values'    => array(
              array(
                  'value'     => 'main',
                  'label'     => Mage::helper('banner')->__('Main Banner'),
              ),

              array(
                  'value'     => 'side',
                  'label'     => Mage::helper('banner')->__('Side Banner'),
              ),
          ),
      ));
     
	  
	  $fieldset->addField('link_url', 'text', array(
          'label'     => Mage::helper('banner')->__('Banner Url'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'link_url',
      ));
	  
	  
	  
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('banner')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('banner')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('banner')->__('Disabled'),
              ),
          ),
      ));
     
      
     
      if ( Mage::getSingleton('adminhtml/session')->getBannerData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getBannerData());
          Mage::getSingleton('adminhtml/session')->setBannerData(null);
      } elseif ( Mage::registry('banner_data') ) {
          $form->setValues(Mage::registry('banner_data')->getData());
      }
      return parent::_prepareForm();
  }
}