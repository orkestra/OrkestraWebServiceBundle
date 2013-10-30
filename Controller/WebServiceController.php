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
abstract class WebServiceController extends Controller
{
    /**
     * @var mixed
     */
    protected $content;

    /**
     * @return string
     */
    abstract protected function getType();

    /**
     * Get denormalized request content
     *
     * @return mixed
     */
    protected function getRequestContent(array $context = array())
    {
        if (!$this->content) {
            $request    = $this->getRequest();
            $format     = $request->getContentType();
            $content    = $request->getContent();
            $serializer = $this->getSerializer();

            $this->content = $serializer->deserialize($content, $this->getType(), $format, $context);
        }

        return $this->content;
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
        $serializer = $this->getSerializer();

        return new Response(
            $serializer->serialize($data, $format),
            200,
            array(
                'Content-type' => $request->getMimeType($request->getRequestFormat())
            ));
    }

    /**
     * @return \Symfony\Component\Serializer\Serializer
     */
    protected function getSerializer()
    {
        return $this->get('serializer');
    }
}
