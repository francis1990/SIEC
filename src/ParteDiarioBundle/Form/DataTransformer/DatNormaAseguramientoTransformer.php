<?php

namespace ParteDiarioBundle\Form\DataTransformer;

use Doctrine\Common\Persistence\ObjectManager;
use NomencladorBundle\Entity\DatNormaAseguramiento;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class DatNormaAseguramientoTransformer implements DataTransformerInterface
{
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if ($value instanceof DatNormaAseguramiento)
            return $value->getIdnormaaseg();
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (empty($value))
            return null;

        $aseg = $this->manager->find('NomencladorBundle\Entity\DatNormaAseguramiento', $value);

        if (null === $aseg) {
            throw new TransformationFailedException(sprintf(
                'El id de "NomencladorBundle\Entity\DatNormaAseguramiento" ("%s") no existe!',
                $value
            ));
        }

        return $aseg;
    }
}