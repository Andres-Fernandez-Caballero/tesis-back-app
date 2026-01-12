<?php

namespace App\Repositories;

use App\Models\Therapists\Announcement;

class AnnouncementRepository
{
    public function getAll(int $pagination = 0)
    {
        $query = Announcement::orderByDesc('scoring');

        if ($pagination) {
            return $query->paginate($pagination);
        }

        return $query->get();
    }

    public function findById(string $id)
    {
        return Announcement::findOrFail($id);
    }

    public function getDestacates(int $cuantity)
    {
        return Announcement::where('is_active', true)
            ->orderByDesc('scoring')
            ->limit($cuantity)
            ->get();
    }
}