<?php

namespace Orkestra\Bundle\WebServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Base class for web service controllers
 */
abstract class WebServiceController extends Controller implements FilterRequestContentInterface
{
    /**
     * @var mixed
     */
    protected $_content;

    /**
     * Sets the request content
     *
     * @param mixed $content
     */
    function setRequestContent($content)
    {
        $this->_content = $content;
    }
}