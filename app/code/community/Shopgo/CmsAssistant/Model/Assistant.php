<?php

class Shopgo_CmsAssistant_Model_Assistant extends Mage_Core_Model_Abstract
{
    const CORE_HTTP_HELPER            = 'core/http';
    const URL_PATH_CMS_ASSISTANT_EDIT = 'adminhtml/shopgo_cms_assistant_index/edit';

    public function isEnabled($storeId = 0)
    {
        $helper = Mage::helper('cmsassistant');

        $enabled = $helper->getConfigData(
            Shopgo_CmsAssistant_Helper_Data::CONFIG_GENERAL_FIELD_ENABLED,
            Shopgo_CmsAssistant_Helper_Data::CONFIG_GROUP_GENERAL,
            Mage::app()->getStore($storeId)
        );

        return $enabled
            && !$helper->isTemplateHintsEnabled()
            && $this->isIpAllowed()
            && !$this->systemConfigsCheck();
    }

    public function isIpAllowed()
    {
        $result = 1;

        $allowedIps = Mage::helper('cmsassistant')->getConfigData(
            Shopgo_CmsAssistant_Helper_Data::CONFIG_GENERAL_FIELD_ALLOWED_IPS,
            Shopgo_CmsAssistant_Helper_Data::CONFIG_GROUP_GENERAL,
            Mage::app()->getStore()
        );

        if ($allowedIps) {
            $allowedIps = array_map('trim', explode(',', $allowedIps));
        }

        $remoteAddress = Mage::helper(self::CORE_HTTP_HELPER)->getRemoteAddr();

        if (!empty($allowedIps) && !in_array($remoteAddress, $allowedIps)) {
            $result = 0;
        }

        return $result;
    }

    public function systemConfigsCheck()
    {
        $helper = Mage::helper('cmsassistant');
        $store  = Mage::app()->getStore();

        $systemConfigs = $helper->getConfigData(
            Shopgo_CmsAssistant_Helper_Data::CONFIG_GENERAL_FIELD_SYSTEM_CONFIG,
            Shopgo_CmsAssistant_Helper_Data::CONFIG_GROUP_GENERAL,
            $store
        );

        $systemConfigs = preg_split("/\r\n|\n|\r/", ' ', $systemConfigs);

        foreach ($systemConfigs as $config) {
            if (Mage::getStoreConfig($config, $store)) {
                return 1;
            }
        }

        return 0;
    }

    public function isBlockAssistantEnabled()
    {
        return $this->_isAssistantEnabled(
            Shopgo_CmsAssistant_Helper_Data::CONFIG_ASSISTANTS_FIELD_BLOCK
        );
    }

    public function isPageAssistantEnabled()
    {
        return $this->_isAssistantEnabled(
            Shopgo_CmsAssistant_Helper_Data::CONFIG_ASSISTANTS_FIELD_PAGE
        );
    }

    public function isShopgoBannerSliderAssistantEnabled()
    {
        return Mage::helper('cmsassistant')->isShopgoBannerSliderEnabled()
            && $this->_isAssistantEnabled(
                Shopgo_CmsAssistant_Helper_Data::CONFIG_ASSISTANTS_FIELD_SHOPGO_BANNERSLIDER
            );
    }

    private function _isAssistantEnabled($field)
    {
        $helper = Mage::helper('cmsassistant');
        $store  = Mage::app()->getStore();

        return $helper->getConfigData(
            $field,
            Shopgo_CmsAssistant_Helper_Data::CONFIG_GROUP_ASSISTANTS,
            $store
        );
    }
}
