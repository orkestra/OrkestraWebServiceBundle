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

use Orkestra\Bundle\ApplicationBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Base class for web service controllers
 */
abstract class WebServiceController extends Controller implements FilterRequestContentInterface
{
    /**
     * @var mixed
     */
    protected $content;

    /**
     * Sets the request content
     *
     * @param mixed $content
     */
    public function setRequestContent($content)
    {
        $this->content = $content;
    }

    /**
     * Returns a response in the appropriate format
     *
     * @param mixed $data
     *
     * @return Response
     */
    protected function respond($data)
    {
        $request    = $this->getRequest();
        $format     = $request->getRequestFormat();
        $serializer = $this->get('serializer');

        return new Response(
            $serializer->serialize($data, $format),
            200,
            array(
                'Content-type' => $request->getMimeType($request->getRequestFormat())
            ));
    }
}
