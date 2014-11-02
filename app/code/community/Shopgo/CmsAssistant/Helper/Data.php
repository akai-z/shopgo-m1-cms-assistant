<?php

class Shopgo_CmsAssistant_Helper_Data extends Shopgo_Core_Helper_Abstract
{
    const CONFIG_GROUP_GENERAL                        = 'shopgo_cmsassistant/general/';
    const CONFIG_GROUP_ASSISTANTS                     = 'shopgo_cmsassistant/assistants/';
    const CONFIG_GENERAL_FIELD_ENABLED                = 'enabled';
    const CONFIG_GENERAL_FIELD_ALLOWED_IPS            = 'allowed_ips';
    const CONFIG_GENERAL_FIELD_SYSTEM_CONFIG          = 'system_config';
    const CONFIG_ASSISTANTS_FIELD_BLOCK               = 'block';
    const CONFIG_ASSISTANTS_FIELD_PAGE                = 'page';
    const CONFIG_ASSISTANTS_FIELD_SHOPGO_BANNERSLIDER = 'shopgo_bannerslider';
    
    protected $_logFile = 'cms_assistant.log';

    public function getConfigData($field, $group, $store = null)
    {
        $data = null;

        if (in_array($group, $this->_validConfigGroups())
            && $field) {
            $data = Mage::getStoreConfig($group . $field, $store);
        }

        return $data;
    }

    private function _validConfigGroups()
    {
        return array(
            self::CONFIG_GROUP_GENERAL,
            self::CONFIG_GROUP_ASSISTANTS
        );
    }

    public function getStoreByCode($store)
    {
        $stores = Mage::app()->getStores(true, true);
        $storesCodeId = array();

        foreach ($stores as $code => $_store) {
            $storesCodes[$code] = $_store;
        }

        if (Mage::app()->isSingleStoreMode()) {
            return Mage::app()->getStore();
        }

        if (isset($storesCodes[$store])) {
            return $storesCodes[$store];
        }

        return false;
    }

    public function isTemplateHintsEnabled()
    {
        return Mage::app()->getLayout()
            ->createBlock('core/template')
            ->getShowTemplateHints();
    }

    public function getCmsBlockInfo($id)
    {
        $block = Mage::getModel('cms/block')->load($id);

        $info = array(
            'identifier' => $block->getIdentifier(),
            'id'         => $block->getBlockId()
        );

        return $info;
    }

    public function isShopgoBannerSliderEnabled()
    {
        return Mage::getStoreConfig('bannerslider/general/enabled');
    }
}
