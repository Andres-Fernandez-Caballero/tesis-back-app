<?php

namespace App\Services;

use App\Repositories\AnnouncementRepository;

class AnnouncementService
{
    public function __construct(protected AnnouncementRepository $announcementRepository){}

    private const DEFAULT_PAGINATION = 10;

    public function getAll(int $pagination = self::DEFAULT_PAGINATION)
    {
        return $this->announcementRepository->getAll($pagination);
    }

    public function getDestacates()
    {
        return $this->announcementRepository->getDestacates(5);
    }

    public function findById(string $id)
    {
        return $this->announcementRepository->findById($id);
    }
}