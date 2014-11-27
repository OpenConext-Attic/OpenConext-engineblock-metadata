<?php

namespace OpenConext\Component\EngineBlockMetadata\X509;

/**
 * Lazy Proxy for X509 Certificate.
 * Used when parsing / validation of the certificate is meant to be deferred until use
 * (bad idea in theory, sometimes useful in practice).
 */
class X509CertificateLazyProxy
{
    /**
     * @var X509CertificateFactory
     */
    private $factory;

    /**
     * @var string
     */
    private $certData;

    /**
     * @var X509Certificate
     */
    private $certificate = null;

    /**
     * @param X509CertificateFactory $factory
     * @param $certData
     */
    public function __construct(X509CertificateFactory $factory, $certData)
    {
        $this->factory = $factory;
        $this->certData = $certData;
    }

    /**
     * @param $methodName
     * @param $methodArguments
     * @return mixed
     */
    public function __call($methodName, $methodArguments)
    {
        if (!$this->certificate) {
            $this->certificate = $this->factory->fromCertData($this->certData);
        }

        return call_user_func_array(array($this->certificate, $methodName), $methodArguments);
    }
}
