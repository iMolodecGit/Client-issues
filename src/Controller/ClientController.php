<?php
namespace App\Controller;

use App\Entity\IssueClient;
use App\FormType\IssueClientType;
use App\Repository\IssueClientRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClientController extends AbstractController
{
    /**
     * @Route("/client/create",  name="app_client_create", methods={"POST"})
     */
    public function create(Request $request, LoggerInterface $logger, IssueClientRepository $issueClientRepository): JsonResponse
    {
        $json = json_decode($request->getContent(),true);

        if (empty($json)) {
            return new JsonResponse(['status' => false, 'errors' => 'Can\'t store a client. JSON invalid!'],
                Response::HTTP_BAD_REQUEST);
        }

        $client = new IssueClient();
        $form = $this->createForm(IssueClientType::class, $client);
        $form->submit($json);

        $existedClient = $issueClientRepository->findOneBy(['name' => $client->getName()]);
        if ($existedClient) {
            return new JsonResponse(['status' => false, 'errors' => 'Can\'t store a client. Client with this name is already exist!'],
                Response::HTTP_BAD_REQUEST);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($client);
                $em->flush();
            } catch (\Exception $e) {
                $logger->error("Can\'t add a client. Problem: ". $e->getCode() . " - ". $e->getMessage());
                return new JsonResponse(
                    ['status' => false, 'errors' => 'Can\'t add a client. Problem: Something went wrong'],
                    Response::HTTP_BAD_REQUEST
                );
            }

            return new JsonResponse(['status' => true, 'client'=> $client->getId()]);
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
}
