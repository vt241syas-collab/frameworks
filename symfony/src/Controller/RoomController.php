<?php

namespace App\Controller;

use App\Entity\Room;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route; 

#[Route('/api/rooms', name: 'api_rooms_')]
class RoomController extends AbstractController
{
   
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(RoomRepository $roomRepository): JsonResponse
    {
        $rooms = $roomRepository->findAll();
        $data = [];

        foreach ($rooms as $room) {
            $data[] = [
                'id' => $room->getId(),
                'title' => $room->getTitle(),
                'description' => $room->getDescription()
            ];
        }

        return $this->json($data);
    }

   #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['title'])) {
            return $this->json(['error' => 'Missing title'], Response::HTTP_BAD_REQUEST);
        }

        $room = new Room();
        $room->setTitle($data['title']);
        $room->setDescription($data['description'] ?? null);

        $em->persist($room);
        $em->flush();

        return $this->json([
            'id' => $room->getId(),
            'title' => $room->getTitle(),
            'description' => $room->getDescription()
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id, RoomRepository $roomRepository): JsonResponse
    {
        $room = $roomRepository->find($id);

        if (!$room) {
            return $this->json(['error' => 'Room not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'id' => $room->getId(),
            'title' => $room->getTitle(),
            'description' => $room->getDescription()
        ]);
    }

    #[Route('/{id}', name: 'update', methods: ['PATCH'])]
    public function update(int $id, Request $request, RoomRepository $roomRepository, EntityManagerInterface $em): JsonResponse
    {
        $room = $roomRepository->find($id);

        if (!$room) {
            return $this->json(['error' => 'Room not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['title'])) {
            $room->setTitle($data['title']);
        }
        if (isset($data['description'])) {
            $room->setDescription($data['description']);
        }

        $em->flush();

        return $this->json([
            'message' => 'Room updated successfully',
            'id' => $room->getId(),
            'title' => $room->getTitle(),
            'description' => $room->getDescription()
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id, RoomRepository $roomRepository, EntityManagerInterface $em): JsonResponse
    {
        $room = $roomRepository->find($id);

        if (!$room) {
            return $this->json(['error' => 'Room not found'], Response::HTTP_NOT_FOUND);
        }

        $em->remove($room);
        $em->flush();

        return $this->json(['message' => 'Room deleted successfully'], Response::HTTP_OK);
    }
}