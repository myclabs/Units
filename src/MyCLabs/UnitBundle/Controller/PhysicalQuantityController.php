<?php

namespace MyCLabs\UnitBundle\Controller;

use APY\DataGridBundle\Grid\Column\BlankColumn;
use APY\DataGridBundle\Grid\Row;
use APY\DataGridBundle\Grid\Source\Entity;
use MyCLabs\UnitBundle\Entity\PhysicalQuantity\BasePhysicalQuantity;
use MyCLabs\UnitBundle\Entity\PhysicalQuantity\PhysicalQuantity;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PhysicalQuantityController extends Controller
{
    public function listAction()
    {
        // Grid
        $gridSource = new Entity(PhysicalQuantity::class);
        $grid = $this->get('grid');
        $grid->setSource($gridSource);

        // Type column
        $typeColumn = new BlankColumn(['id' => 'type', 'title' => 'Type']);
        $typeColumn->manipulateRenderCell(function ($value, Row $row, $router) {
            return ($row->getEntity() instanceof BasePhysicalQuantity) ? 'Base quantity' : 'Derived quantity';
        });
        $grid->addColumn($typeColumn);

        return $grid->getGridResponse('UnitBundle:PhysicalQuantity:list.html.twig');
    }
}
