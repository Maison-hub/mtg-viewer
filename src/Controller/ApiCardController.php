<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Entity\Card;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/card', name: 'api_card_')]
#[OA\Tag(name: 'Card', description: 'Routes for all about cards')]
class ApiCardController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger
    ) {
    }
    #[Route('/all/{page}', name: 'List all cards', methods: ['GET'])]
    #[OA\Get(description: 'Return paginated list of cards in the database')]
    #[OA\Parameter(name: 'page', description: 'Page number', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'List paginated cards')]
    public function cardAll(int $page = 1): Response
    {
        $this->logger->info('Paginated cards listed', ['page' => $page]);
        $pageSize = 100;
        $offset = ($page - 1) * $pageSize;

        $cards = $this->entityManager->getRepository(Card::class)->createQueryBuilder('c')
            ->setFirstResult($offset)
            ->setMaxResults($pageSize)
            ->getQuery()
            ->getResult();

        return $this->json($cards);
    }

    #[Route('/search', name: 'Search cards by name', methods: ['GET'])]
    #[OA\Parameter(name: 'name', description: 'Name of the card', in: 'query', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'code', description: 'Set code of the card', in: 'query', required: false, schema: new OA\Schema(type: 'string'))]
    #[OA\Get(description: 'Return cards in the database by search name')]
    #[OA\Response(response: 200, description: 'Search cards by name')]
    public function searchCard(Request $request): Response
    {
        $searchInput = $request->query->get('name');
        $code = $request->query->get('code');
        $this->logger->info('Searching for cards', ['searchInput' => $searchInput, 'code' => $code]);

        $queryBuilder = $this->entityManager->getRepository(Card::class)->createQueryBuilder('c')
            ->where('c.name LIKE :searchInput')
            ->setParameter('searchInput', '%' . $searchInput . '%');

        if ($code && $code !== 'All') {
            $queryBuilder->andWhere('c.setCode = :code')
                ->setParameter('code', $code);
        }

        $cards = $queryBuilder->setMaxResults(20)
            ->getQuery()
            ->getResult();

        return $this->json($cards);
    }

    #[Route('/{uuid}', name: 'Show card', methods: ['GET'])]
    #[OA\Parameter(name: 'uuid', description: 'UUID of the card', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Put(description: 'Get a card by UUID')]
    #[OA\Response(response: 200, description: 'Show card')]
    #[OA\Response(response: 404, description: 'Card not found')]
    public function cardShow(string $uuid): Response
    {
        $this->logger->info('Card showed', ['uuid' => $uuid]);
        $card = $this->entityManager->getRepository(Card::class)->findOneBy(['uuid' => $uuid]);
        if (!$card) {
            return $this->json(['error' => 'Card not found'], 404);
        }
        return $this->json($card);
    }

    #[Route('/set-codes', name: 'Show card', methods: ['GET'])]
    #[OA\Get(description: 'Return all set codes')]
    #[OA\Response(response: 200, description: 'List all set codes')]
    public function listSetCodes(): Response
    {
        $this->logger->info('Card showed');
        $setCodes = $this->entityManager->getRepository(Card::class)->createQueryBuilder('c')
            ->select('DISTINCT c.setCode')
            ->getQuery()
            ->getResult();
        return $this->json($setCodes);
    }
}
