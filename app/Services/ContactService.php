<?php

namespace App\Services;


use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Contact;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\Response;
use Throwable;


class ContactService
{
    private $startTime;

    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getContactList(array $request, Carbon $startTime): array
    {

        $pageSize = $request['page_size'] ?? "";
        $paginate = $request['page'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        /** @var Contact|Builder $contactBuilder */
        $contactBuilder = Contact::select([
            'contacts.id',
           'contacts.first_name',
            'contacts.last_name',
            'contacts.email',
            'contacts.profile_photo',
            'contacts.created_at',
            'contacts.updated_at'
        ]);
        /** @var Collection $contacts */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $contacts = $contactBuilder->paginate($pageSize);
            $paginateData = (object)$contacts->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $contacts = $contactBuilder->get();
        }

        $response['data'] = $contacts->toArray()['data'] ?? $contacts->toArray();

        $response['_response_status'] = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "query_time" => $startTime->diffInSeconds(Carbon::now()),
        ];

        return $response;
    }

    /**
     * @param int $id
     * @return Contact
     */
    public function getOneContact(int $id): Contact
    {
        /** @var Contact|Builder $contactBuilder */
        $contactBuilder = Contact::select([
            'contacts.id',
            'contacts.first_name',
            'contacts.last_name',
            'contacts.email',
            'contacts.profile_photo',
            'contacts.created_at',
            'contacts.updated_at',

        ]);


        /** @var Contact $contact */
        return $contactBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return Contact
     * @throws Throwable
     */
    public function store(array $data): Contact
    {
        $contact = app(Contact::class);

            $contact->fill($data);
            $contact->save();
            $contacts=Contact::get()->toArray();
            event(new \App\Events\Contact($contacts));
            return $contact;
    }

    /**
     * @param Contact $contact
     * @param array $data
     * @return Contact
     */
    public function update(Contact $contact, array $data): Contact
    {
        $contact->fill($data);
        $contact->save();
        return $contact;
    }

    /**
     * @param Contact $contact
     * @return bool
     */
    public function destroy(Contact $contact): bool
    {
        return $contact->delete();
    }

}
