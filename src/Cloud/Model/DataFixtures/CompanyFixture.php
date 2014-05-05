<?php

namespace Cloud\Model\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

use Cloud\Model\Company;

/**
 * Load test companies and test users
 */
class CompanyFixture extends AbstractFixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $em)
    {
        $cumulus = new Company();
        $cumulus->setTitle('Cumulus, Inc.');

        $foobar = new Company();
        $foobar->setTitle('Foobar');

        $em->persist($cumulus);
        $em->persist($foobar);
        $em->flush();

        $this->addReference('cumulus', $cumulus);
        $this->addReference('foobar', $foobar);
    }
}
