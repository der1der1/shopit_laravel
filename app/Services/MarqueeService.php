<?php

namespace App\Services;

use App\Repositories\MarqueeRepository;

class MarqueeService
{
    protected $marqueeRepository;

    public function __construct(MarqueeRepository $marqueeRepository)
    {
        $this->marqueeRepository = $marqueeRepository;
    }

    public function getAllMarquees()
    {
        return $this->marqueeRepository->getAllMarquees();
    }

    public function createMarquee(array $data)
    {
        return $this->marqueeRepository->create($data);
    }

    public function updateMarquee(int $id, array $data)
    {
        return $this->marqueeRepository->update($id, $data);
    }

    public function deleteMarquee(int $id)
    {
        return $this->marqueeRepository->delete($id);
    }

    public function findById(int $id)
    {
        return $this->marqueeRepository->findById($id);
    }
}