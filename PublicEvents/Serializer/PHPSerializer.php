<?php

namespace Elefant\PublicEventsBundle\PublicEvents\Serializer;

use Symfony\Component\Serializer\SerializerInterface;

class PHPSerializer implements SerializerInterface
{

    /**
     * Serializes data in the appropriate format.
     *
     * @param mixed $data any data
     * @param string $format format name
     * @param array $context options normalizers/encoders have access to
     *
     * @return string
     */
    public function serialize($data, $format, array $context = array())
    {
        return serialize($data);
    }

    /**
     * Deserializes data into the given type.
     *
     * @param mixed $data
     * @param string $type
     * @param string $format
     * @param array $context
     *
     * @return object
     */
    public function deserialize($data, $type, $format, array $context = array())
    {
        return unserialize($data);
    }
}