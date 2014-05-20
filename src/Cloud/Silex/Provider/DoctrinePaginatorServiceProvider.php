<?php
/**
 * cloudxxx-api (http://www.cloud.xxx)
 *
 * Copyright (C) 2014 Really Useful Limited.
 * Proprietary code. Usage restrictions apply.
 *
 * @copyright  Copyright (C) 2014 Really Useful Limited
 * @license    Proprietary
 */

namespace Cloud\Silex\Provider;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Tools\Pagination\Paginator;
use JMS\Serializer\SerializerBuilder;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Adapter\DoctrineCollectionAdapter;
use Pagerfanta\Pagerfanta;
use Silex\Application;
use Silex\ServiceProviderInterface;

class DoctrinePaginatorServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function register(Application $app)
    {
        $app['paginator'] = $app->protect(function ($model) use ($app) {
            $list = $app['em']->getRepository($model)->matching(new Criteria());
            $adapter = new DoctrineCollectionAdapter($list);
            $pager = new Pagerfanta($adapter);

            $page = $app['request']->get('page') ?: 1; 
            $perPage = $app['request']->get('per_page') ?: 10;

            $pager
                ->setMaxPerPage($perPage)
                ->setCurrentPage($page);

            return $pager;
        });

        $app['paginator.response.json'] = $app->protect(function ($model, $groups) use ($app) {
            $hostUrl = $app['request']->getSchemeAndHttpHost() . $app['request']->getPathInfo();
            $pager   = $app['paginator']($model);
            $params  = $app['request']->query->all();
            $navlinks   = $this->getLinks($hostUrl, $params, $pager);

            $serializer  = SerializerBuilder::create()->build();
            $jsonContent = $serializer->serialize(
                $pager->getCurrentPageResults(),
                'json', 
                \JMS\Serializer\SerializationContext::create()->setGroups($groups)
            );

            $response = $app->json(json_decode($jsonContent));
            $response->headers->add(['Link' => $navlinks['link']]);
            $response->headers->add(['X-Pagination-Range' => $navlinks['range']]);

            return $response;
        });
    }

    /**
     * {@inheritDoc}
     */
    public function boot(\Silex\Application $app)
    {
    }

    protected function getLinks($hostUrl, $params, $pager)
    {
        $link = $this->getLink($hostUrl, $params, $pager);
        $currentPage = $pager->getCurrentPage();
        $pageSize = $pager->getMaxPerPage();
        $lastPage = ceil($pager->count() / $pageSize);
        $totalItemCount = $pager->getNbResults();

        $navlink = [
            $link(1, 'first'),
            $link($lastPage, 'last'),
            $pager->hasPreviousPage() ? $link($pager->getPreviousPage(), 'prev') : "",
            $pager->hasNextPage() ? $link($pager->getNextPage(), 'next') : "",
        ];
        $rangelink = $this->getRangeLinks($currentPage, $pageSize, $lastPage, $totalItemCount);

        $navlink = str_replace(', ,', ', ', implode(', ', $navlink));

        return [
            'link' => $navlink,
            'range' => $rangelink,
        ];
    }

    protected function getLink($hostUrl, $params, $pager)
    {
        return function($page, $rel) use ($hostUrl, $params) {

            $params['page'] = $page;
            $params = http_build_query($params);
            $link = sprintf('<%s?%s>; rel="%s"', $hostUrl, $params, $rel);

            return $link;
        };

    }

    /*
     * X-Pagination-Range: items 1-10/250; pages 1/25
     */
    protected function getRangeLinks($currentPage, $pageSize, $lastPage, $totalItemCount)
    {
        $currentItem = ($currentPage * $pageSize) - $pageSize;
        $lastItemOfPage = min($currentItem + $pageSize - 1, $totalItemCount);
        $links = "X-Pagination-Range: items $currentItem-$lastItemOfPage/$totalItemCount; page $currentPage/$lastPage";

        return $links;
    }

}
