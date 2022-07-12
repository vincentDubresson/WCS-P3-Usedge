<?php

namespace App\Controller;

use App\Repository\CanvasWorkshopsRepository;
use App\Entity\ResearchRequest;
use App\Repository\ResearchPlanRepository;
use App\Service\CheckDataUtils;
use App\Service\ResearchPlanUtils;
use App\Service\ResearchRequestMailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResearchPlanController extends AbstractController
{
    #[Route('/research-plan/{id}', name: 'app_research_plan', methods: ['GET', 'POST'])]
    public function index(
        int $id,
        ResearchRequest $researchRequest,
        CanvasWorkshopsRepository $workshopRepository,
        Request $request,
        CheckDataUtils $checkDataUtils,
        ResearchPlanUtils $researchPlanUtils,
        ResearchRequestMailer $mailer,
        ResearchPlanRepository $researchPlanRepo,
    ): Response {

        $dataComponent = $checkDataUtils->trimData($request);
        $researchPlan = $researchPlanRepo->findOneBy(['researchRequest' => $id]);
        $researchPlanErrors = [];

        if (empty($dataComponent)) {
            $workshops = $workshopRepository->findAll();

            return $this->render('research_plan/research_plan.html.twig', [
                'workshops' => $workshops,
                'researchRequest' => $researchRequest,
            ]);
        }

        $researchPlanUtils->researchPlanCheckEmpty($dataComponent);
        $researchPlanUtils->researchPlanCheckLength($dataComponent);
        $researchPlanErrors = $researchPlanUtils->getCheckErrors();
        if (empty($researchPlanErrors)) {
            $researchPlanUtils->initResearchPlan($dataComponent, $researchPlan);
            $mailer->researchPlanSendMail();
        }

        return $this->render('research_plan/confirm.html.twig', ['errors' => $researchPlanErrors]);
    }

    #[Route('/research-plan/{id}/section', name: 'research_plan_new_section', methods: ['GET', 'POST'])]
    public function newSection(
        int $id,
        ResearchRequest $researchRequest,
        CanvasWorkshopsRepository $workshopRepository,
        Request $request,
        CheckDataUtils $checkDataUtils,
        ResearchPlanUtils $researchPlanUtils,
        ResearchPlanRepository $researchPlanRepo,
    ): Response {
        $researchPlan = $researchPlanRepo->findOneBy(['researchRequest' => $id]);

        $dataComponent = $checkDataUtils->trimData($request);
        $researchPlanUtils->initResearchPlan($dataComponent, $researchPlan);

        $workshops = $workshopRepository->findAll();

        return $this->render('research_plan/research_plan.html.twig', [
            'workshops' => $workshops,
            'researchRequest' => $researchRequest,
            'researchPlan' => $researchPlan,
        ]);
    }
}
