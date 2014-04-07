<?php

class Shopgo_CmsAssistant_Helper_Data extends Mage_Core_Helper_Abstract
{
    const LOG_FILE                                    = 'cms_assistant.log';
    const CONFIG_GROUP_GENERAL                        = 'shopgo_cmsassistant/general/';
    const CONFIG_GROUP_ASSISTANTS                     = 'shopgo_cmsassistant/assistants/';
    const CONFIG_GENERAL_FIELD_ENABLED                = 'enabled';
    const CONFIG_GENERAL_FIELD_ALLOWED_IPS            = 'allowed_ips';
    const CONFIG_GENERAL_FIELD_SYSTEM_CONFIG          = 'system_config';
    const CONFIG_ASSISTANTS_FIELD_BLOCK               = 'block';
    const CONFIG_ASSISTANTS_FIELD_PAGE                = 'page';
    const CONFIG_ASSISTANTS_FIELD_SHOPGO_BANNERSLIDER = 'shopgo_bannerslider';

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

    public function isTemplateHintsEnabled()
    {
        return Mage::app()->getLayout()
            ->createBlock('core/template')
            ->getShowTemplateHints();
    }

    public function getCmsBlockIdentifier($id)
    {
        return Mage::getModel('cms/block')
            ->load($id)->getIdentifier();
    }

    public function isShopgoBannerSliderEnabled()
    {
        return Mage::getStoreConfig('bannerslider/general/enabled');
    }

    public function log($params, $type = 'system', $_file = '')
    {
        if (!Mage::getStoreConfig('dev/log/active') || empty($params)) {
            return false;
        }

        if ($type == 'system' || $type == '') {
            if (gettype($params) == 'string') {
                $params = array(array('message' => $params));
            }

            foreach ($params as $param) {
                if (!isset($param['message'])) {
                    continue;
                }
                $message = gettype($param['message']) == 'array' ?
                    print_r($param['message'], true) : $param['message'];
                $level = isset($param['level']) ? $param['level'] : null;
                $file = !empty($_file) ? $_file : self::LOG_FILE;
                if (!empty($param['file'])) {
                    $file = $param['file'];
                }
                if (strpos($file, '.log') === false) {
                    $file .= '.log';
                }
                $forceLog = isset($param['forceLog'])
                    ? $param['forceLog'] : false;

                Mage::log($message, $level, $file, $forceLog);
            }
        } elseif ($type == 'exception') {
            if (get_class($params) != 'Exception') {
                return false;
            }

            Mage::logException($params);
        }

        return true;
    }
}
