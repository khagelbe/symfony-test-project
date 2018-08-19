<?php

namespace Application\Migrations;

use AppBundle\Entity\Category;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Adds given categories into database
 *
 * @package Application\Migrations
 */
class Version201808 extends AbstractMigration
{
    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema)
    {
        $this->addSql('INSERT INTO `category` (`id`, `name`)
        VALUES
            (108140,\'Kellersanierung\'),
            (402020,\'Holzdielen schleifen\'),
            (411070,\'Fensterreinigung\'),
            (802030,\'Abtransport, Entsorgung und Entrümpelung\'),
            (804040,\'Sonstige Umzugsleistungen\')');
    }

    /**
     * {@inheritdoc}
     */
    public function down(Schema $schema)
    {

    }
}
