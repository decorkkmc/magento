<?php

namespace Tamara\Checkout\Model;

class AddressRepository {

    const CLICK_AND_COLLECT_METHODS = ['pickupatstore'];

    public function getClickAndCollectMethods() {
        return self::CLICK_AND_COLLECT_METHODS;
    }
}
