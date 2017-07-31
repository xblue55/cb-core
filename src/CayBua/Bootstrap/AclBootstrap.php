<?php

namespace CayBua\Bootstrap;

use CayBua\BootstrapInterface;
use CayBua\Constants\Services;
use CayBua\Constants\AclRoles;
use CayBua\Api;

use Phalcon\Acl;
use Phalcon\Config;
use Phalcon\DiInterface;

class AclBootstrap implements BootstrapInterface
{
    public function run(Api $api, DiInterface $di, Config $config)
    {
        /** @var \PhalconApi\Acl\MountingEnabledAdapterInterface $acl */
        $acl = $di->get(Services::ACL);

        $unauthorizedRole = new Acl\Role(AclRoles::UNAUTHORIZED);
        $authorizedRole = new Acl\Role(AclRoles::AUTHORIZED);

        $acl->addRole($unauthorizedRole);
        $acl->addRole(new Acl\Role(AclRoles::LOCAL_SERVICE), $unauthorizedRole);

        $acl->addRole($authorizedRole);

        $acl->addRole(new Acl\Role(AclRoles::ADMINISTRATOR), $authorizedRole);
        $acl->addRole(new Acl\Role(AclRoles::MANAGER), $authorizedRole);
        $acl->addRole(new Acl\Role(AclRoles::USER), $authorizedRole);


        $acl->mountMany($api->getCollections());
    }
}