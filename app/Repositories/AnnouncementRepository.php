<?php

namespace App\Repositories;

use App\Enums\SubscriptionStatus;
use App\Models\Therapists\Announcement;
use Illuminate\Database\Eloquent\Builder;

class AnnouncementRepository
{
    public function getAll(int $pagination = 0)
    {
        $query = Announcement::with(['therapist.user', 'therapist.user.score', 'tags'])
            ->withActiveLocalSubscription()
            ->orderByDesc('scoring');

        if ($pagination) {
            return $query->paginate($pagination);
        }

        return $query->get();
    }

    public function findById(string $id)
    {
        return Announcement::with(['therapist.user', 'therapist.user.score', 'tags'])
            ->withActiveLocalSubscription()
            ->findOrFail($id);
    }

    public function getDestacates(int $cuantity)
    {
        return Announcement::with(['therapist.user', 'therapist.user.score', 'tags'])
            ->withActiveLocalSubscription()
            ->where('is_active', true)
            ->orderByDesc('scoring')
            ->limit($cuantity)
            ->get();
    }
}