<?php

class Shopgo_CmsAssistant_Block_Assistant
{
    const CMS_BLOCK_BLOCK           = 'Mage_Cms_Block_Block';
    const CMS_PAGE_BLOCK            = 'Mage_Cms_Block_Page';
    const SHOPGO_BANNERSLIDER_BLOCK = 'Shopgo_BannerSlider_Block_BannerSlider';

    public function coreBlockAbstractToHtmlAfter(Varien_Event_Observer $observer)
    {
        if (!Mage::app()->getStore()->isAdmin()
            && Mage::getModel('cmsassistant/assistant')->isEnabled()) {
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
                $assistantHtml = $this->getAssistantHtml(
                    array(
                        'type'  => 'block',
                        'title' => '',
                        'id'    => $helper->getCmsBlockIdentifier(
                            $blockObject->getBlockId()
                        )
                    )
                );
                break;
            case $model->isPageAssistantEnabled()
                && in_array(self::CMS_PAGE_BLOCK, $classes):
                $assistantHtml = $this->getAssistantHtml(
                    array(
                        'type'  => 'page',
                        'title' => '',
                        'id'    => $blockObject->getPage()->getIdentifier()
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

        $editUrlParams  = array('type' => $var['type']);
        $assistantId    = "shopgo_cms_{$var['type']}_assistant";
        $assistantTitle = $var['title'];

        if (isset($var['id'])) {
            $editUrlParams['id'] = $var['id'];
            $assistantId        .= "_{$var['id']}";
            $assistantTitle      = $var['id'];
        }

        $editUrl = Mage::getUrl(
            Shopgo_CmsAssistant_Model_Assistant::URL_PATH_CMS_ASSISTANT_EDIT,
            $editUrlParams
        );

        $html = <<<EOF
<div class="shopgo-cms-assistant-wrapper sca-{$var['type']}" id="$assistantId">
    <div class="shopgo-cms-assistant-content">
        <div class="shopgo-cms-assistant-label sca-{$var['type']} ribbon">
            <div class="shopgo-cms-assistant-click-here">{$helper->__('Click bellow to Edit')}</div>
            <a href="$editUrl" target="_blank">$assistantTitle</a>
        </div>
        %s
        <div class="shopgo-cms-assistant-content-highlight sca-{$var['type']}"></div>
        <div class="clear"></div>
    </div>
</div>
EOF;

        return $html;
    }
}
