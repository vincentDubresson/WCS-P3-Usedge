<?php

namespace App\Service;

use App\Entity\ResearchTemplate;

class ComponentUpdateManager
{
    private array $dataComponent;
    private ComponentUpdateUtils $compUpdateUtils;
    private ResearchTemplate $researchTemplate;
    private int $componentId;
    private array $componentNames = [
        'section' => 'section',
        'external-link' => 'externalLink',
        'single-choice' => 'singleChoice',
        'date-picker' => 'datePicker',
    ];

    public function __construct(ComponentUpdateUtils $compUpdateUtils)
    {
        $this->compUpdateUtils = $compUpdateUtils;
    }

    public function updateComponent(
        array $dataComponent,
        ResearchTemplate $researchTemplate,
        int $componentId
    ): int|null {
        $this->dataComponent = $dataComponent;
        $this->researchTemplate = $researchTemplate;
        $this->componentId = $componentId;
        $functionName = $this->componentNames[$this->dataComponent['name']];

        $id = $this->$functionName();
        return $id;
    }

    public function section(): int|null
    {
            $this->compUpdateUtils->loadUpdateSection($this->dataComponent, $this->componentId);
            $id = $this->researchTemplate->getId();
            return $id;
    }

    public function externalLink(): int|null
    {
            $this->compUpdateUtils->loadUpdateExternalLink($this->dataComponent, $this->componentId);
            $id = $this->researchTemplate->getId();
            return $id;
    }

    public function singleChoice(): int|null
    {
            $this->compUpdateUtils->loadUpdateSingleChoice($this->dataComponent, $this->componentId);
            $id = $this->researchTemplate->getId();
            return $id;
    }

    public function datePicker(): int|null
    {
            $this->compUpdateUtils->loadUpdateDatePicker($this->dataComponent, $this->componentId);
            $id = $this->researchTemplate->getId();
            return $id;
    }
}
