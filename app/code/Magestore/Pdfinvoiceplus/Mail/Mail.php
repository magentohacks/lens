<?php

namespace Magestore\Pdfinvoiceplus\Mail;
/**
 * Class Mail
 * @package Magestore\Pdfinvoiceplus\Mail
 */
class Mail
{
    /**
     * @var array
     */
    private $templateVars;
    /**
     * @param array $templateVars
     */
    public function setTemplateVars($templateVars)
    {
        $this->templateVars = $templateVars;
    }
    /**
     * @return mixed
     */
    public function getTemplateVars()
    {
        return $this->templateVars;
    }
}