<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Dolphin\Attributesetchange\Api;

interface AttributesetchangeManagementInterface
{

    /**
     * GET for attributesetchange api
     * @param string $productIds
     * @param string $attribute
     * @return string
     */
    public function getAttributesetchange($productIds,$attribute);
}

