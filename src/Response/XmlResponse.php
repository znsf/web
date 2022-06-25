<?php

namespace ZnSf\Web\Response;

use Symfony\Component\HttpFoundation\Response;
use ZnLib\Components\Format\Encoders\XmlEncoder;

class XmlResponse extends Response
{
    protected $data;
    protected $callback;

    public const DEFAULT_ENCODING_OPTIONS = 15;

    protected $encodingOptions = self::DEFAULT_ENCODING_OPTIONS;

    /**
     * @param mixed $data    The response data
     * @param int   $status  The response status code
     * @param array $headers An array of response headers
     * @param bool  $isXml   If the data is already a XML string
     */
    public function __construct($data = null, int $status = 200, array $headers = [], bool $isXml = false)
    {
        parent::__construct('', $status, $headers);

        if ($isXml && !\is_string($data) && !is_numeric($data) && !\is_callable([$data, '__toString'])) {
            throw new \TypeError(sprintf('"%s": If $isXml is set to true, argument $data must be a string or object implementing __toString(), "%s" given.', __METHOD__, get_debug_type($data)));
        }

        if (null === $data) {
            $data = new \ArrayObject();
        }

        $isXml ? $this->setXml($data) : $this->setData($data);
    }

    /**
     * Factory method for chainability.
     *
     * Example:
     *
     *     return XMLResponse::create(['key' => 'value'])
     *         ->setSharedMaxAge(300);
     *
     * @param mixed $data    The XML response data
     * @param int   $status  The response status code
     * @param array $headers An array of response headers
     *
     * @return static
     *
     * @deprecated since Symfony 5.1, use __construct() instead.
     */
    public static function create($data = null, int $status = 200, array $headers = [])
    {
        trigger_deprecation('symfony/http-foundation', '5.1', 'The "%s()" method is deprecated, use "new %s()" instead.', __METHOD__, static::class);
        return new static($data, $status, $headers);
    }

    /*
     * Factory method for chainability.
     *
     * Example:
     *
     *     return JsonResponse::fromJsonString('{"key": "value"}')
     *         ->setSharedMaxAge(300);
     *
     * @param string $data    The JSON response string
     * @param int    $status  The response status code
     * @param array  $headers An array of response headers
     *
     * @return static
     */
//    public static function fromJsonString(string $data, int $status = 200, array $headers = [])
//    {
//        return new static($data, $status, $headers, true);
//    }

    /*
     * Sets the JSONP callback.
     *
     * @param string|null $callback The JSONP callback or null to use none
     *
     * @return $this
     *
     * @throws \InvalidArgumentException When the callback name is not valid
     */
//    public function setCallback(string $callback = null)
//    {
//        if (null !== $callback) {
//            // partially taken from https://geekality.net/2011/08/03/valid-javascript-identifier/
//            // partially taken from https://github.com/willdurand/JsonpCallbackValidator
//            //      JsonpCallbackValidator is released under the MIT License. See https://github.com/willdurand/JsonpCallbackValidator/blob/v1.1.0/LICENSE for details.
//            //      (c) William Durand <william.durand1@gmail.com>
//            $pattern = '/^[$_\p{L}][$_\p{L}\p{Mn}\p{Mc}\p{Nd}\p{Pc}\x{200C}\x{200D}]*(?:\[(?:"(?:\\\.|[^"\\\])*"|\'(?:\\\.|[^\'\\\])*\'|\d+)\])*?$/u';
//            $reserved = [
//                'break', 'do', 'instanceof', 'typeof', 'case', 'else', 'new', 'var', 'catch', 'finally', 'return', 'void', 'continue', 'for', 'switch', 'while',
//                'debugger', 'function', 'this', 'with', 'default', 'if', 'throw', 'delete', 'in', 'try', 'class', 'enum', 'extends', 'super',  'const', 'export',
//                'import', 'implements', 'let', 'private', 'public', 'yield', 'interface', 'package', 'protected', 'static', 'null', 'true', 'false',
//            ];
//            $parts = explode('.', $callback);
//            foreach ($parts as $part) {
//                if (!preg_match($pattern, $part) || \in_array($part, $reserved, true)) {
//                    throw new \InvalidArgumentException('The callback name is not valid.');
//                }
//            }
//        }
//
//        $this->callback = $callback;
//
//        return $this->update();
//    }

    /**
     * Sets a raw string containing a XML document to be sent.
     *
     * @return $this
     */
    public function setXml(string $xml)
    {
        $this->data = $xml;
        return $this->update();
    }

    /**
     * Sets the data to be sent as XML.
     *
     * @param mixed $data
     *
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function setData($data = [])
    {
        $xmlEncoder = new XmlEncoder();
        $data = $xmlEncoder->encode($data);

        /*try {
            $data = json_encode($data, $this->encodingOptions);
        } catch (\Exception $e) {
            if ('Exception' === \get_class($e) && str_starts_with($e->getMessage(), 'Failed calling ')) {
                throw $e->getPrevious() ?: $e;
            }
            throw $e;
        }

        if (\PHP_VERSION_ID >= 70300 && (\JSON_THROW_ON_ERROR & $this->encodingOptions)) {
            return $this->setXml($data);
        }

        if (\JSON_ERROR_NONE !== json_last_error()) {
            throw new \InvalidArgumentException(json_last_error_msg());
        }*/

        return $this->setXml($data);
    }

    /**
     * Returns options used while encoding data to XML.
     *
     * @return int
     */
    public function getEncodingOptions()
    {
        return $this->encodingOptions;
    }

    /**
     * Sets options used while encoding data to XML.
     *
     * @return $this
     */
    public function setEncodingOptions(int $encodingOptions)
    {
        $this->encodingOptions = $encodingOptions;
        $xmlEncoder = new XmlEncoder();
        $data = $xmlEncoder->decode($this->data);
        return $this->setData($data);
    }

    /**
     * Updates the content and headers according to the XML data and callback.
     *
     * @return $this
     */
    protected function update()
    {
        if (null !== $this->callback) {
            // Not using application/javascript for compatibility reasons with older browsers.
            $this->headers->set('Content-Type', 'text/xml');

            return $this->setContent(sprintf('/**/%s(%s);', $this->callback, $this->data));
        }

        // Only set the header when there is none or when it equals 'text/javascript' (from a previous update with callback)
        // in order to not overwrite a custom definition.
        if (!$this->headers->has('Content-Type') || 'text/xml' === $this->headers->get('Content-Type')) {
            $this->headers->set('Content-Type', 'text/xml');
        }

        return $this->setContent($this->data);
    }
}
