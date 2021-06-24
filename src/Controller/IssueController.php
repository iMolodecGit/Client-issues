<?php

namespace App\Controller;

use App\Entity\Issue;
use App\Entity\IssueClient;
use App\FormType\IssueType;
use App\Repository\IssueClientRepository;
use App\Repository\IssueRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IssueController extends AbstractController
{

    /**
     * @Route("/issue/index",  name="app_issue_index", methods={"GET"})
     */
    public function index(IssueRepository $repository): JsonResponse
    {
        $issues = $repository->findAll();

        $data = [];
        foreach ($issues as $issue) {
            $data[] = $issue->toArray();
        }
        return new JsonResponse(['status' => true, 'models' => $data]);
    }

    /**
     * @Route("/issue/by-client/{clientId}",  name="app_issue_index", methods={"GET"})
     */
    public function clientIssues(int $clientId, IssueRepository $issueRepo, IssueClientRepository $issueClientRepo): JsonResponse
    {
        $client = $issueClientRepo->find($clientId);
        if (empty($client)) {
            return new JsonResponse(['status' => false, 'errors' => sprintf("Client with id '%s' not found!", $clientId)],
                Response::HTTP_BAD_REQUEST);
        }

        $issues = $client->getIssues();
        $data = [];
        foreach ($issues as $issue) {
            $data[] = $issue->toArray();
        }
        return new JsonResponse(['status' => true, 'models' => $data]);
    }

    /**
     * @Route("/issue/create",  name="app_issue_create", methods={"POST"})
     */
    public function create(Request $request, LoggerInterface $logger): JsonResponse
    {
        $json = json_decode($request->getContent(), true);

        if (empty($json)) {
            return new JsonResponse(['status' => false, 'errors' => 'Can\'t store a issue. JSON error!'],
                Response::HTTP_BAD_REQUEST);
        }

        $issue = new Issue();
        $form = $this->createForm(IssueType::class, $issue);
        $form->submit($json);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $issue->setCreatedAt(new \DateTime());
                $issue->setUpdatedAt(new \DateTime());
                $em->persist($issue);
                $em->flush();
            } catch (\Exception $e) {
                $logger->error("Can\'t add a issue. Problem: " . $e->getCode() . " - " . $e->getMessage());
                return new JsonResponse(
                    ['status' => false, 'errors' => 'Can\'t add a issue. Problem: Something went wrong'],
                    Response::HTTP_BAD_REQUEST
                );
            }
            return new JsonResponse(['status' => true, 'issue' => $issue->getId()]);
        } else {
            $errors = [];
            foreach ($form->getErrors(true) as $key => $error) {
                $errors[$key] = $error->getMessage();
            }
            if (count($errors)) {
                return new JsonResponse([
                    'status' => false,
                    'errors' => $errors
                ],
                    Response::HTTP_BAD_REQUEST
                );
            }
        }

        return new JsonResponse(['status' => false]);
    }

    /**
     * @Route("/issue/in-work/{id}",  name="app_issue_in_work", methods={"PUT"})
     */
    public function setInWork(int $id, Request $request, LoggerInterface $logger): JsonResponse
    {
        /** @var Issue $issue */
        $issue = $this->getDoctrine()->getRepository(Issue::class)->find($id);

        if (empty($issue)) {
            return new JsonResponse(['status' => false, 'errors' => sprintf("Issue with id '%s' not found!", $id)],
                Response::HTTP_BAD_REQUEST);
        }

        try {
            $em = $this->getDoctrine()->getManager();
            $issue->setInWork(true);
            $em->persist($issue);
            $em->flush();
            return new JsonResponse(['status' => true, 'issue' => $issue->toArray()]);
        } catch (\Exception $e) {
            $logger->error("Can\'t set issue in work. Problem: " . $e->getCode() . " - " . $e->getMessage());
            return new JsonResponse(
                ['status' => false, 'errors' => 'Can\'t set issue in wor. Problem: Something went wrong'],
                Response::HTTP_BAD_REQUEST
            );
        }
    }
}
