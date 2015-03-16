<?php

namespace Sulu\Bundle\DeveloperBundle\DataFixtures\ORM;

/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sulu\Bundle\MediaBundle\Entity\MediaType;
use Sulu\Bundle\MediaBundle\Entity\CollectionType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sulu\Bundle\MediaBundle\Entity\Collection;
use Sulu\Bundle\MediaBundle\Entity\CollectionMeta;

class LoadMediaData implements FixtureInterface, OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $mediaType = new MediaType();
        $mediaType->setName('image');
        $manager->persist($mediaType);
        $collectionType = new CollectionType();
        $collectionType->setName('images');
        $manager->persist($collectionType);

        $collection = new Collection();
        $collection->setType($collectionType);
        $collectionMeta = new CollectionMeta();
        $collectionMeta->setTitle('Bregenzerwald');
        $collectionMeta->setDescription('The Bregenz Forest Mountains,[1] also the Bregenzerwald Mountains (German: Bregenzerwaldgebirge), are a range of the Northern Limestone Alps, named after the town of Bregenz. The Bregenz Forest Mountains are entirely located in the Austrian state of Vorarlberg.');
        $collectionMeta->setLocale('de');
        $collectionMeta->setCollection($collection);
        $collection->addMeta($collectionMeta);
        $manager->persist($collection);
        $manager->persist($collectionMeta);
        $manager->flush();

        $imageDir = realpath(__DIR__ . '/../../Resources/data/images');

        $finder = new Finder();
        $finder->in($imageDir)->name('*.jpg');

        foreach ($finder as $file) {
            $uploadedFile = new UploadedFile($file->getPathname(), $file->getPathname(), null, null, null, true);

            $data = array(
                'id' => null,
                'locale' => 'de',
                'type' => $mediaType->getId(),
                'collection' => $collection->getId(),
                'name' => $file->getBasename(),
                'title' => substr($file->getBasename(), 0, -(strlen($file->getExtension()) + 1)),
            );

            $this->container->get('sulu_media.media_manager')->save($uploadedFile, $data, 1);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 100;
    }
}
