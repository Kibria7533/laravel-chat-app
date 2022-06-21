<?php

namespace App\Services;


use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Conversation;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\Response;
use Throwable;


class ConversationService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getConversationList(array $request,int $id, Carbon $startTime): array
    {

        $pageSize = $request['page_size'] ?? "";
        $paginate = $request['page'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        /** @var Conversation|Builder $conversationBuilder */
        $conversationBuilder = Conversation::select([
            'conversations.id',
            'conversations.conversation_name',
            'conversations.created_at',
            'conversations.updated_at'

        ]);
        $conversationBuilder->join('group_members','group_members.conversation_id','conversations.id');
        $conversationBuilder->where('group_members.contact_id',$id);
        /** @var Collection $contacts */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $contacts = $conversationBuilder->paginate($pageSize);
            $paginateData = (object)$contacts->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $contacts = $conversationBuilder->get();
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
     * @return Conversation
     */
    public function getOneConversastion(int $id): Conversation
    {
        /** @var Conversation|Builder $conversationBuilder */
        $conversationBuilder = Conversation::select([
            'conversations.id',
            'conversations.first_name',
            'conversations.last_name',
            'conversations.email',
            'conversations.profile_photo',
            'conversations.created_at',
            'conversations.updated_at',

        ]);


        /** @var Conversation $contact */
        return $conversationBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return Conversation
     * @throws Throwable
     */
    public function store(array $data): Conversation
    {
        $contact = app(Conversation::class);
        $contact->fill($data);
        $contact->save();
        return $contact;
    }

    /**
     * @param Conversation $contact
     * @param array $data
     * @return Conversation
     */
    public function update(Conversation $contact, array $data): Conversation
    {
        $contact->fill($data);
        $contact->save();
        return $contact;
    }

    /**
     * @param Conversation $contact
     * @return bool
     */
    public function destroy(Conversation $contact): bool
    {
        return $contact->delete();
    }

}
