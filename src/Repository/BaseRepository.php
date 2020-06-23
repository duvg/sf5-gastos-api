<?php

declare(strict_types=1);

namespace App\Repository;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;

abstract class BaseRepository
{
    private ManagerRegistry $managerRegistry;

    protected ObjectRepository $objectRepository;

    protected Connection $connection;

    public function __construct(ManagerRegistry $managerRegistry, Connection $connection)
    {
        $this->managerRegistry = $managerRegistry;
        $this->objectRepository = $this->getEntityManager()->getRepository($this->entityClass());
        $this->connection = $connection;
    }

    // Get class name
    abstract protected static function entityClass(): string;

    // Save a entity
    protected function saveEntity($entity): void
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    // Delete a entity
    protected function removeEntity($entity): void
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }

    // Crete query builder
    protected function createQueryBuilder(): QueryBuilder
    {
        return $this->getEntityManager()->createQueryBuilder();
    }

    // Execute query builder
    // Get data

    /**
     * @throws DBALException
     */
    protected function executeFetchQuery(string $query, array $params = []): array
    {
        return $this->connection->executeQuery($query, $params)->fetchAll();
    }

    /**
     * @throws DBALException
     */
    protected function executeInsertQuery(string $query, array $params = []): void
    {
        $this->connection->executeQuery($query, $params);
    }

    // Get entity manager
    private function getEntityManager(): ObjectManager
    {
        $entityManager = $this->managerRegistry->getManager();

        if ($entityManager->isOpen()) {
            return $entityManager;
        }

        return $this->managerRegistry->resetManager();
    }
}
