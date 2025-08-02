<?php

namespace App\Repositories;

use App\Models\FcmToken;
use App\Repositories\Contracts\BaseRepository;

/**
 * @extends  BaseRepository<FcmToken>
 */
class FcmTokenRepository extends BaseRepository
{
    protected string $modelClass = FcmToken::class;

    public function create(array $data, array $relationships = [], array $countable = []): ?FcmToken
    {
        $token = FcmToken::where('token', $data['token'])
            ->where('user_id', $data['user_id'])
            ->first();

        if ($token) {
            return $token->load($relationships)->loadCount($countable);
        }

        return parent::create($data, $relationships, $countable);
    }
}
