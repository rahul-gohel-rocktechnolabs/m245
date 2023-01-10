<?php
/**
 * @category  Mageants PdfInvoice
 * @package   Mageants_PdfInvoice
 * @copyright Copyright (c) 2017 mageants
 * @author Mageants Team <info@mageants.com>
 */

namespace Mageants\PdfInvoice\Model\Plugin;

class Config
{
    /**
     * Config constructor.
     *
     * @param \Magento\Backend\Model\UrlInterface $url
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Backend\Model\UrlInterface $url,
        \Magento\Framework\Registry $registry
    ) {
        $this->_url = $url;
        $this->registry = $registry;
    }

    /**
     * After Get Variables WysiwygAction Url function.
     *
     * @param mixed $subject
     * @param mixed $result
     * @return void
     */
    public function afterGetVariablesWysiwygActionUrl($subject, $result)
    {
        if ($this->registry->registry('mageants_pdftemplate')) {
            return $this->getUrl();
        }

        return $result;
    }

    /**
     * Returns the variable url.
     *
     * @return void
     */
    public function getUrl()
    {
        return $this->_url->getUrl('*/variable/template');
    }
}
