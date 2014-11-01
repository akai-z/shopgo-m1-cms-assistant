<?php

class Shopgo_CmsAssistant_Block_Assistant
{
    const CMS_BLOCK_BLOCK           = 'Mage_Cms_Block_Block';
    const CMS_PAGE_BLOCK            = 'Mage_Cms_Block_Page';
    const SHOPGO_BANNERSLIDER_BLOCK = 'Shopgo_BannerSlider_Block_BannerSlider';

    public function coreBlockAbstractToHtmlAfter(Varien_Event_Observer $observer)
    {
        $store = Mage::app()->getStore();

        if (!$store->isAdmin()
            && Mage::getModel('cmsassistant/assistant')->isEnabled($store->getId())) {
            $transport = $observer->getTransport();

            if ($transport->getHtml()) {
                $html = $this->addAssistant(
                    $observer->getBlock(),
                    $transport->getHtml()
                );

                if ($html) {
                    $transport->setHtml($html);
                }
            }
        }
    }

    public function addAssistant($blockObject, $html)
    {
        $assistantHtml = '';

        $blockObjectParentClass = $blockObject
            ? get_class($blockObject->getParentBlock())
            : '';

        $classes = array(
            get_class($blockObject),
            $blockObjectParentClass
        );

        $helper = Mage::helper('cmsassistant');
        $model  = Mage::getModel('cmsassistant/assistant');

        switch (true) {
            case $model->isBlockAssistantEnabled()
                && in_array(self::CMS_BLOCK_BLOCK, $classes):
                $cmsBlockInfo = $helper->getCmsBlockInfo(
                    $blockObject->getBlockId()
                );

                $assistantHtml = $this->getAssistantHtml(
                    array(
                        'type'       => 'block',
                        'title'      => '',
                        'identifier' => $cmsBlockInfo['identifier'],
                        'id'         => $cmsBlockInfo['id']
                    )
                );
                break;
            case $model->isPageAssistantEnabled()
                && in_array(self::CMS_PAGE_BLOCK, $classes):
                $assistantHtml = $this->getAssistantHtml(
                    array(
                        'type'       => 'page',
                        'title'      => '',
                        'identifier' => $blockObject->getPage()->getIdentifier(),
                        'id'         => $blockObject->getPage()->getId()
                    )
                );
                break;
            case $model->isShopgoBannerSliderAssistantEnabled()
                && in_array('Shopgo_Bannerslider_Block_Bannerslider', $classes):
                $assistantHtml = $this->getAssistantHtml(
                    array(
                        'type'  => 'bannerslider',
                        'title' => $helper->__('ShopGo Banner Slider')
                    )
                );
                break;
        }

        $html = $assistantHtml
            ? sprintf($assistantHtml, $html)
            : '';

        return $html;
    }

    public function getAssistantHtml($var)
    {
        $helper = Mage::helper('cmsassistant');

        $editUrlParams  = array(
            'type'  => $var['type'],
            'store' => Mage::app()->getStore()->getStoreId(),
            'id'    => $var['id']
        );
        $assistantId    = "shopgo_cms_{$var['type']}_assistant";
        $assistantTitle = $var['title'];

        if (isset($var['identifier'])) {
            $assistantId   .= "_{$var['identifier']}";
            $assistantTitle = $var['identifier'];
        }

        $editUrl = Mage::getUrl(
            Shopgo_CmsAssistant_Model_Assistant::URL_PATH_CMS_ASSISTANT_EDIT,
            $editUrlParams
        );

        $html = <<<EOF
<div class="shopgo-cms-assistant-wrapper sca-{$var['type']} sca-wrapper-reset" id="$assistantId">
    <div class="shopgo-cms-assistant-content sca-content-reset">
        <div class="shopgo-cms-assistant-label sca-{$var['type']} ribbon sca-label-reset">
            <div class="shopgo-cms-assistant-click-here sca-click-here-reset">{$helper->__('Click bellow to Edit')}</div>
            <a class="shopgo-cms-assistant-link sca-link-reset" href="$editUrl" target="_blank">$assistantTitle</a>
        </div>
        %s
        <div class="shopgo-cms-assistant-content-highlight sca-{$var['type']} sca-content-highlight-reset"></div>
        <div class="clear sca-clear-reset"></div>
    </div>
</div>
EOF;

        return $html;
    }
}
