<?php

namespace Surfnet\GroupService\Service;

use Doctrine\ORM\EntityManagerInterface;
use Janus\Component\ReadonlyEntities\Entities\ConnectionRepository;
use OpenConext\Component\EngineBlockMetadata\Entity\MetadataRepositoryInterface;
use OpenConext\Component\EngineBlockMetadata\Translator\JanusTranslator;

class JanusMetadataRepository implements MetadataRepositoryInterface
{
    private $entityManager;
    private $translator;

    public function __construct(
        EntityManagerInterface $entityManager,
        JanusTranslator $translator
    ) {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
    }

    public function fetchEntityByEntityId($entityId)
    {
        /** @var ConnectionRepository $connectionRepository */
        $connectionRepository = $this->entityManager->getRepository('Janus\Component\ReadonlyEntities\Entities\Connection');
        $connections = $connectionRepository->findBy(array('name' => $entityId));

        if (empty($connections)) {
            throw new \RuntimeException("No entities found for '$entityId'");
        }
        if (count($connections) > 1) {
            throw new \RuntimeException("Multiple connections found for '$entityId'");
        }

        return $this->translator->translate($connections[0]);
    }

    public function fetchEntityIds()
    {
        // TODO: Implement fetchEntityIds() method.
    }
}