<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloFormBundle\Autocomplete\Provider;

use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use YnloFramework\YnloFormBundle\Autocomplete\AutocompleteContextInterface;
use YnloFramework\YnloFormBundle\Autocomplete\AutocompleteProviderInterface;
use YnloFramework\YnloFormBundle\Autocomplete\AutocompleteResults;

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
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'entity';
    }

    /**
     * {@inheritdoc}
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

            $this->filter($qb, $request->get('q'), $request->get('related_fields', []), $context);

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
        }

        return $results;
    }

    /**
     * createQueryBuilder.
     *
     * @param AutocompleteContextInterface $context
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function createQueryBuilder(AutocompleteContextInterface $context)
    {
        $dqlParts = $context->getParameter('dql_parts');

        $qb = $this->doctrine->getManager()->createQueryBuilder();
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
     * Filter current query using given term and context.
     *
     * @param QueryBuilder                 $qb
     * @param string                       $term
     * @param array                        $extraParameters
     * @param AutocompleteContextInterface $context
     */
    protected function filter(QueryBuilder $qb, $term, $extraParameters = [], AutocompleteContextInterface $context)
    {
        $alias = $qb->getRootAliases()[0];
        $fields = $context->getParameter('autocomplete');
        if (is_string($fields)) {
            $fields = [$fields];
        }

        if ($term) {
            $or = $qb->expr()->orX();
            $search = $qb->expr()->literal('%'.$term.'%');
            foreach ($fields as $field) {
                $or->add($qb->expr()->like("$alias.$field", $search));
            }
            $qb->andWhere($or);
        }

        foreach ($extraParameters as $paramName => $value) {
            $qb->setParameter($paramName, $value);
        }

        if ($dqlParameters = $context->getParameter('dql_parameters')) {
            array_map([$qb->getParameters(), 'add'], $dqlParameters);
        }
    }

    /**
     * orderBy.
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
     * {@inheritdoc}
     */
    public function buildResponse(AutocompleteResults $results, AutocompleteContextInterface $context)
    {
        $array = [];
        foreach ($results as $id => $result) {
            $array[$id] = (string) $result;
        }

        return new JsonResponse($array);
    }
}
