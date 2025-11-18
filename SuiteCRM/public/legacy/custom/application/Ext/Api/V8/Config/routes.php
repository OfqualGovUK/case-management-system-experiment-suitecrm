<?php

use SuiteCRM\custom\application\Ext\Api\V8\Factory\OQParamsMiddlewareFactory;
use Api\V8\Param;
use LoggerManager;

try {

    $oqparamsFac = $app->getContainer()->get(OQParamsMiddlewareFactory::class);

    //custom update record route with OQ params middleware
    $app->patch('/oqmodule', 'Api\V8\Controller\ModuleController:updateModuleRecord')
        ->add($oqparamsFac->bind(Param\UpdateModuleParams::class));

    //custom get module records route with OQ params middleware
    $app->get('/oqmodule/{moduleName}', 'Api\V8\Controller\ModuleController:getModuleRecords')
        ->add($oqparamsFac->bind(Param\GetModulesParams::class));

    //custom get a module record route with OQ params middleware
    $app->get('/oqmodule/{moduleName}/{id}', 'Api\V8\Controller\ModuleController:getModuleRecord')
        ->add($oqparamsFac->bind(Param\GetModuleParams::class));

    //custom update record route with OQ params middleware
    $app->post('/oqmodule', 'Api\V8\Controller\ModuleController:createModuleRecord')
        ->add($oqparamsFac->bind(Param\CreateModuleParams::class));

    //custom delete record route with OQ params middleware
    $app->delete('/oqmodule/{moduleName}/{id}', 'Api\V8\Controller\ModuleController:deleteModuleRecord')
        ->add($oqparamsFac->bind(Param\DeleteModuleParams::class));

    //custom get record relationships route with OQ params middleware
    $app->get(
        '/oqmodule/{moduleName}/{id}/relationships/{linkFieldName}',
        'Api\V8\Controller\RelationshipController:getRelationship'
    )
        ->add($oqparamsFac->bind(Param\GetRelationshipParams::class));

    //custom create relationship route with OQ params middleware
    $app->post(
        '/oqmodule/{moduleName}/{id}/relationships',
        'Api\V8\Controller\RelationshipController:createRelationship'
    )
        ->add($oqparamsFac->bind(Param\CreateRelationshipParams::class));

    //custom create relationship by link route with OQ params middleware
    $app->post(
        '/oqmodule/{moduleName}/{id}/relationships/{linkFieldName}',
        'Api\V8\Controller\RelationshipController:createRelationshipByLink'
    )
        ->add($oqparamsFac->bind(Param\CreateRelationshipByLinkParams::class));

    //custom delete relationship route with OQ params middleware
    $app->patch(
        '/oqmodule/{moduleName}/{id}/relationships/{linkFieldName}/{relatedBeanId}',
        'Api\V8\Controller\RelationshipController:deleteRelationship'
    )
        ->add($oqparamsFac->bind(Param\DeleteRelationshipParams::class));

    $app->get(
        '/oq-current-user',
        'SuiteCRM\custom\application\Ext\Api\V8\Controller\OQUserController:getCurrentUser'
    )
        ->add($oqparamsFac->bind(SuiteCRM\custom\application\Ext\Api\V8\Param\OQGetUserParams::class));
} catch (Exception $e) {
    \LoggerManager::getLogger()->error("Error loading OQ routes: " . $e->getMessage());
}
