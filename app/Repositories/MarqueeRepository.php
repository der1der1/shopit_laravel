<?php

namespace App\Repositories;

use App\Models\marqeeModel as Marquee;

class MarqueeRepository
{
    public function getAllMarquees()
    {
        return Marquee::getAllMarqee();
    }

    public function findById(int $id)
    {
        return Marquee::find($id);
    }

    public function create(array $data)
    {
        return Marquee::create($data);
    }

    public function update(int $id, array $data)
    {
        $marquee = $this->findById($id);
        
        if (!$marquee) {
            return false;
        }

        foreach ($data as $key => $value) {
            if ($marquee->$key != $value) {
                $marquee->$key = $value;
            }
        }

        return $marquee->save();
    }

    public function delete(int $id)
    {
        $marquee = $this->findById($id);
        
        if (!$marquee) {
            return false;
        }

        return $marquee->delete();
    }
}