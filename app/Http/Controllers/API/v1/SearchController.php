<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Models\Clinic;
use App\Models\Speciality;
use App\Repositories\ClinicRepository;
use App\Repositories\SpecialityRepository;
use App\Traits\RestTrait;

class SearchController extends ApiController
{
    use RestTrait;

    public function publicSearch()
    {
        $search = request('search', null);
        if ($search == null || trim($search) == '') {
            return $this->noData();
        }
        $data = ClinicRepository::make()->globalQuery()
            ->withWhereHas('user', function ($query) use ($search) {
                $query->where('tags', 'LIKE', "%$search%");
            })->get()->map(fn(Clinic $clinic) => [
                'key' => 'clinic',
                'label' => $clinic->user?->full_name?->translate(),
                'url' => '/customer/clinics/' . $clinic->id
            ]);

        $specialities = SpecialityRepository::make()->globalQuery()
            ->where('tags', 'LIKE', "%$search%")
            ->get()
            ->map(fn(Speciality $spec) => [
                'key' => 'speciality',
                'label' => $spec->name?->translate(),
                'url' => '/customer/specialities/' . $spec->id
            ]);

        return $this->apiResponse(
            $data->push(...$specialities),
            self::STATUS_OK,
            __('site.get_successfully')
        );
    }
}
