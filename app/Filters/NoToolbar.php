<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class NoToolbar implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Do something here
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Disable debug toolbar by removing related scripts and styles
        $body = $response->getBody();
        
        // Remove debug toolbar scripts and styles with more comprehensive regex
        $body = preg_replace('/<script[^>]*(?:debugbar|kint)[^>]*>.*?<\/script>/ims', '', $body);
        $body = preg_replace('/<style[^>]*(?:debugbar|kint)[^>]*>.*?<\/style>/ims', '', $body);
        $body = preg_replace('/<!-- DEBUG-VIEW START.*?-->/', '', $body);
        
        // Also remove any remaining debug-related content
        $body = preg_replace('/<script\s+id="debugbar_[^"]*"[^>]*>.*?<\/script>/ims', '', $body);
        $body = preg_replace('/<style\s+class="kint-[^"]*"[^>]*>.*?<\/style>/ims', '', $body);
        
        $response->setBody($body);
        
        return $response;
    }
}
