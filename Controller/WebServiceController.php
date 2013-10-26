<?php

/*
 * This file is part of the OrkestraWebServiceBundle package.
 *
 * Copyright (c) Orkestra Community
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

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
    public function setRequestContent($content)
    {
        $this->_content = $content;
    }
}
