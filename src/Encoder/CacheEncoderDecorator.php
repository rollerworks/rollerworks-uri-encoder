<?php

/**
 * This file is part of the Rollerworks UriEncoder Component package.
 *
 * (c) 2014 Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\UriEncoder\Encoder;

use Rollerworks\Component\UriEncoder\UriEncoderInterface;
use Rollerworks\Component\UriEncoder\CacheAdapterInterface;

/**
 * CacheEncoderDecorator keeps a cached version of original data
 * and delegates calls back to the original Encoder when no there is no cache.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class CacheEncoderDecorator implements UriEncoderInterface
{
    /**
     * @var CacheAdapterInterface
     */
    private $cacheDriver;

    /**
     * @var UriEncoderInterface
     */
    private $encoder;

    /**
     * Constructor.
     *
     * @param CacheAdapterInterface $cache
     * @param UriEncoderInterface   $encoder
     */
    public function __construct(CacheAdapterInterface $cache, UriEncoderInterface $encoder)
    {
        $this->cacheDriver = $cache;
        $this->encoder = $encoder;
    }

    /**
     * {@inheritdoc}
     */
    public function encodeUri($data)
    {
        $result = $this->encoder->encodeUri($data);
        $this->cacheDriver->save($result, $data);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function decodeUri($data)
    {
        if ($this->cacheDriver->contains($data)) {
            return $this->cacheDriver->fetch($data);
        }

        $result = $this->encoder->decodeUri($data);
        if (null !== $result) {
            $this->cacheDriver->save($data, $result);
        }

        return $result;
    }
}
