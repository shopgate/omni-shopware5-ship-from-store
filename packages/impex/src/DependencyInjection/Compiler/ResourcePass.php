<?php

namespace Dustin\ImpEx\DependencyInjection\Compiler;

use Dustin\ImpEx\Resource\ResourceInterface;
use Dustin\ImpEx\Resource\ResourceRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\ExpressionLanguage\Expression;

class ResourcePass implements CompilerPassInterface
{
    public const TAG_RESOURCE = 'impex.resource';
    public const TAG_RESOURCE_OBTAINER = 'impex.resource.obtainer';
    public const TAG_RESOURCE_FACTORY = 'impex.resource.factory';

    public function process(ContainerBuilder $container)
    {
        /**
         * @var Definition $registryDefinition
         */
        $registryDefinition = $container->findDefinition(ResourceRegistry::class);

        $this->processResourceFactories($container, $registryDefinition);
        $this->processResources($container, $registryDefinition);
        $this->processResourceObtainers($container);
    }

    /**
     * Process resource factories tagged with 'impex.resource.factory'.
     */
    public function processResourceFactories(ContainerBuilder $container, Definition $registryDefinition): void
    {
        /** @var array $resourceFactories */
        $resourceFactories = $container->findTaggedServiceIds(self::TAG_RESOURCE_FACTORY);

        foreach ($resourceFactories as $factoryId => $tags) {
            $registryDefinition->addMethodCall('addFactory', [new Reference($factoryId)]);
        }
    }

    /**
     * Process resources tagged with 'impex.resource'.
     */
    public function processResources(ContainerBuilder $container, Definition $registryDefinition): void
    {
        /** @var array $resources */
        $resources = $container->findTaggedServiceIds(self::TAG_RESOURCE);

        foreach ($resources as $resourceId => $tags) {
            /** @var Definition $resourceDefinition */
            $resourceDefinition = $container->findDefinition($resourceId);

            foreach ((array) $tags as $config) {
                $this->validateResource($resourceDefinition, $config);

                if ($this->isWrappable($config)) {
                    $registryDefinition->addMethodCall('createResource', [
                        new Reference($resourceId),
                        trim((string) $config['resource']),
                        boolval($config['obtainable']),
                    ]);

                    continue;
                }

                $registryDefinition->addMethodCall('addResource', [new Reference($resourceId)]);
            }
        }
    }

    /**
     * Process resource obtainers tagged with 'impex.resource.obtainer'.
     */
    public function processResourceObtainers(ContainerBuilder $container): void
    {
        /** @var array $resourceObtainers */
        $resourceObtainers = $container->findTaggedServiceIds(self::TAG_RESOURCE_OBTAINER);

        foreach ($resourceObtainers as $id => $tags) {
            /** @var Definition $obtainerDefinition */
            $obtainerDefinition = $container->findDefinition($id);

            $expression = 'service("'.
                str_replace('\\', '\\\\', ResourceRegistry::class).
                '").obtain(service("'.
                str_replace('\\', '\\\\', $id).
                '").getNeededResources())';

            $obtainerDefinition->addMethodCall('receiveResources', [
                new Expression($expression),
            ]);
        }
    }

    /**
     * @throws \Exception
     */
    private function validateResource(Definition $definition, array $config)
    {
        if ($definition->getFactory() !== null) {
            if (isset($config['resource']) || isset($config['obtainable'])) {
                throw new \Exception(sprintf(
                    "Resource with class %s cannot be configured by tag attributes 'obtainable' and/or 'resource' as it is created by factory.",
                    $definition->getClass()
                ));
            }

            return;
        }

        $resourceClass = $definition->getClass();

        if (
            is_subclass_of($resourceClass, ResourceInterface::class) &&
            (isset($config['resource']) || isset($config['obtainable']))
        ) {
            throw new \Exception(
                sprintf(
                    "Resource of class %s cannot be configured by tag attributes 'obtainable' and/or 'resource' as it implements %s.",
                    $resourceClass, ResourceInterface::class
                )
            );
        }

        if (
            !is_subclass_of($resourceClass, ResourceInterface::class) &&
            (
                !isset($config['resource']) ||
                !isset($config['obtainable']) ||
                empty(trim($config['resource']))
            )
        ) {
            throw new \Exception(sprintf(
                "Resource of class %s needs non-empty tag attributes 'resource' and 'obtainable' to be used as resource.",
                $resourceClass
            ));
        }
    }

    private function isWrappable(array $config): bool
    {
        return isset($config['resource']) &&
            isset($config['obtainable']) &&
            !empty(trim($config['resource']));
    }
}
