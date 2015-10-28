<?php

class Shopgo_CmsAssistant_Adminhtml_Shopgo_Cms_Assistant_IndexController extends Mage_Adminhtml_Controller_Action
{
    public function preDispatch()
    {
        if ($this->getRequest()->getActionName() == 'edit') {
            Mage::getSingleton('adminhtml/url')->turnOffSecretKey();
        }

        parent::preDispatch();
    }

    public function editAction()
    {
        $type  = $this->getRequest()->getParam('type');
        $store = $this->getRequest()->getParam('store');
        $id    = $this->getRequest()->getParam('id');

        $model = Mage::getModel('cmsassistant/assistant');

        if (!$model->isEnabled($store)) {
            $this->_noRoute();
            return;
        }

        $helper = Mage::helper('cmsassistant');

        if (!empty($type)) {
            $url    = '';
            $params = array();

            switch (true) {
                case 'block' == $type
                    && $model->isBlockAssistantEnabled():
                    $url    = 'adminhtml/cms_block/edit';
                    $params = array('block_id' => $id);
                    break;
                case 'page' == $type
                    && $model->isPageAssistantEnabled():
                    $url    = 'adminhtml/cms_page/edit';
                    $params = array('page_id' => $id);
                    break;
                case 'bannerslider' == $type
                    && $model->isShopgoBannerSliderAssistantEnabled():
                    $url    = 'adminhtml/shopgo_bannerslider/index';
                    break;
                default:
                    $helper->log('Invalid URL request.');
                    $this->_noRoute();
                    return;
            }

            Mage::app()->getResponse()->setRedirect(
                Mage::helper('adminhtml')->getUrl($url, $params)
            );
        } else {
            $helper->log('Missing URL parameters.');
            $this->_noRoute();
        }
    }

    private function _noRoute()
    {
        $this->_forward('noRoute');
    }
}
