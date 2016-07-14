<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace YnloFramework\YnloFormBundle\Autocomplete\Provider;

use Doctrine\ORM\QueryBuilder;
use YnloFramework\YnloFormBundle\Autocomplete\AutocompleteContextInterface;
use YnloFramework\YnloFormBundle\Autocomplete\AutocompleteProviderInterface;
use YnloFramework\YnloFormBundle\Autocomplete\AutocompleteResults;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SimpleEntityProvider implements AutocompleteProviderInterface
{
    /**
     * @var RegistryInterface
     */
    protected $doctrine;

    /**
     * SimpleEntityProvider constructor.
     *
     * @param RegistryInterface $doctrine
     */
    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'entity';
    }

    /**
     * @inheritDoc
     */
    public function fetchResults(Request $request, AutocompleteContextInterface $context)
    {
        $maxResults = $context->getParameter('max_results');
        $class = $context->getParameter('class');
        $results = new AutocompleteResults();

        if ($class) {
            $qb = $this->createQueryBuilder($context);
            $qb->setMaxResults($maxResults);

            $this->orderBy($qb, $context);

            $this->filter($qb, $request->get('q'), $context);

            if ($request->get('page')) {
                $offset = $qb->getMaxResults() * ($request->get('page') - 1);
                $qb->setFirstResult($offset);
            }

            $rawResults = $qb->getQuery()->getResult();

            foreach ($rawResults as $result) {
                $ids = $qb->getEntityManager()->getClassMetadata($class)->getIdentifierValues($result);
                $id = current(array_values($ids));
                $results->offsetSet($id, $result);
            }

            $count = $this->getTotalOverAll($qb);

            $results->setTotalOverAll($count);
        }

        return $results;
    }

    /**
     * createQueryBuilder
     *
     * @param AutocompleteContextInterface $context
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function createQueryBuilder(AutocompleteContextInterface $context)
    {
        $dqlParts = $context->getParameter('dql_parts');

        $qb = $this->doctrine->getEntityManager()->createQueryBuilder();
        if (!empty($dqlParts)) {
            foreach ($dqlParts as $name => $parts) {
                if (!empty($parts)) {
                    if (is_object($parts)) {
                        $parts = clone $parts;
                    }
                    $qb->add($name, $parts);
                }
            }
        } else {
            $qb->select('s')->from($context->getParameter('class'), 's');
        }

        return $qb;
    }

    /**
     * Filter current query using given term and context
     *
     * @param QueryBuilder                 $qb
     * @param string                       $term
     * @param AutocompleteContextInterface $context
     */
    protected function filter(QueryBuilder $qb, $term, AutocompleteContextInterface $context)
    {
        $alias = $qb->getRootAliases()[0];
        $fields = $context->getParameter('autocomplete');
        if (is_string($fields)) {
            $fields = [$fields];
        }
        if ($term) {
            $or = $qb->expr()->orX();
            $search = $qb->expr()->literal('%' . $term . '%');
            foreach ($fields as $field) {
                $or->add($qb->expr()->like("$alias.$field", $search));
            }
            $qb->andWhere($or);
        }
    }

    /**
     * Get all possible results for current query without limits
     *
     * @param QueryBuilder $qb
     *
     * @return mixed
     */
    protected function getTotalOverAll(QueryBuilder $qb)
    {
        $alias = $qb->getRootAliases()[0];
        $qb->resetDQLPart('select');
        $qb->select($qb->expr()->count("$alias.id"));
        $qb->setMaxResults(null);
        $qb->setFirstResult(null);

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * orderBy
     *
     * @param QueryBuilder                 $qb
     * @param AutocompleteContextInterface $context
     */
    protected function orderBy(QueryBuilder $qb, AutocompleteContextInterface $context)
    {
        $alias = $qb->getRootAliases()[0];
        $orderBy = $context->getParameter('order_by');
        if (!$orderBy) {
            return;
        }

        if (is_string($orderBy)) {
            $orderBy = [$orderBy];
        }

        foreach ($orderBy as $orderByField => $orderByDir) {
            if (is_int($orderByField)) {
                $orderByField = $orderByDir;
                $orderByDir = null;
            }
            $qb->addOrderBy("$alias.$orderByField", $orderByDir);
        }
    }

    /**
     * @inheritDoc
     */
    public function buildResponse(AutocompleteResults $results, AutocompleteContextInterface $context)
    {
        $array = [];
        foreach ($results as $id => $result) {
            $array[$id] = (string)$result;
        }

        return new JsonResponse($array);
    }
}