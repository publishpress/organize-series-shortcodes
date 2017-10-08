<?php

use OrganizeSeries\application\Root;
use OrganizeSeries\domain\model\ClassOrInterfaceFullyQualifiedName;
use OrganizeSeries\ShortcodesAddon\domain\Meta;
use OrganizeSeries\ShortcodesAddon\domain\services\Bootstrap;


Root::initializeExtensionMeta(
    __FILE__,
    OS_SHORTCODE_VER,
    new ClassOrInterfaceFullyQualifiedName(
        Meta::class
    )
);
Root::registerAndLoadExtensionBootstrap(
    new ClassOrInterfaceFullyQualifiedName(
        Bootstrap::class
    )
);
