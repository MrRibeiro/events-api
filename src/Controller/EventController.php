<?php

namespace App\Controller;

use App\Entity\Event;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\EventRepository;
use Doctrine\Persistence\ManagerRegistry;
use DateTime;
use DateTimeZone;

class EventController extends AbstractController
{
  #[Route('/events', name: 'events_list', methods: ['GET'])]
  public function list(EventRepository $eventRepository): JsonResponse
  {
    return $this->json($eventRepository->findAll(), 200);
  }

  #[Route('/events/{event}', name: 'events_get', methods: ['GET'])]
  public function getEvent(int $event, EventRepository $eventRepository): JsonResponse
  {
    $event = $eventRepository->find($event);
    if (!$event) throw $this->createNotFoundException();

    return $this->json($event, 200);
  }

  #[Route('/events', name: 'events_create', methods: ['POST'])]
  public function create(Request $request, EventRepository $eventRepository): JsonResponse
  {
    if ($request->headers->get('Content-Type') == 'application/json') {
      $data = $request->toArray();
    } else {
      $data = $request->request->all();
    }

    $event = new Event();
    $event->setName($data['name']);
    $event->setDescription($data['description']);
    $event->setInitHour($data['initHour']);
    $event->setEndHour($data['endHour']);
    $event->setDate(new DateTime($data['date']));
    $event->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('America/Fortaleza')));
    $event->setUpdatedAt(new \DateTimeImmutable('now', new \DateTimeZone('America/Fortaleza')));

    $eventRepository->add($event, true);

    return $this->json([
      'message' => 'Event created successed!',
      'data' => $event
    ], 201);
  }

  #[Route('/events/{event}', name: 'events_update', methods: ['PUT'])]
  public function update(int $event, Request $request, ManagerRegistry $doctrine, EventRepository $eventRepository): JsonResponse
  {
    if ($request->headers->get('Content-Type') == 'application/json') {
      $data = $request->toArray();
    } else {
      $data = $request->request->all();
    }

    $event = $eventRepository->find($event);

    if (!$event) throw $this->createNotFoundException();

    $event->setName($data['name']);
    $event->setDescription($data['description']);
    $event->setInitHour($data['initHour']);
    $event->setEndHour($data['endHour']);
    $event->setDate(new DateTime($data['date']));
    $event->setUpdatedAt(new \DateTimeImmutable('now', new \DateTimeZone('America/Fortaleza')));

    $doctrine->getManager()->flush();

    return $this->json([
      'message' => 'Event updated successed!',
      'data' => $event
    ], 200);
  }

  #[Route('/events/{event}', name: 'events_delete', methods: ['DELETE'])]
  public function remove(int $event, Request $request, EventRepository $eventRepository): JsonResponse
  {
    $event = $eventRepository->find($event);
    $eventRepository->remove($event, true);


    return $this->json([
      'message' => 'Event deleted successed!',
    ], 200);
  }
}
