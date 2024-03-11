<?php

namespace App\Repositories;

use App\Models\PhoneNumber;
use App\Repositories\Contracts\BaseRepository;
use App\Repositories\Contracts\IBaseRepository;

/**
 * @extends  BaseRepository<PhoneNumber>
 * @implements IBaseRepository<PhoneNumber>
 */
class PhoneNumberRepository extends BaseRepository
{
    public function __construct(PhoneNumber $phoneNumber)
    {
        parent::__construct($phoneNumber);
    }

    /**
     * @param array<string> $phones
     * @param class-string $phoneableType
     * @param int $phoneableId
     * @return void
     */
    public function insert(array $phones, string $phoneableType, int $phoneableId): void
    {
        $data = [];
        foreach ($phones as $phone) {
            $data[] = [
                'phone' => $phone,
                'phoneable_type' => $phoneableType,
                'phoneable_id' => $phoneableId,
                'created_at' => now()->format('Y-m-d H:i:s'),
                'updated_at' => now()->format('Y-m-d H:i:s'),
            ];
        }

        PhoneNumber::insert($data);
    }
}
