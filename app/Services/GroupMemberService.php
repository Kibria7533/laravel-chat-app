<?php

namespace App\Services;


use App\Models\Messege;
use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\GroupMember;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\Response;
use Throwable;


class GroupMemberService
{

    public function getGroupMessege(array $request, int $gid, string $gname, Carbon $startTime): array
    {
        $pageSize = $request['page_size'] ?? "";
        $paginate = $request['page'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        /** @var GroupMember|Builder $groupmMemberBuilder */

        $groupmMemberBuilder = Messege::select([
            'messeges.id',
            'messeges.from_contact_id',
            'messeges.to_contact_id',
            'messeges.messege_text',
            'contacts.first_name as sender_name',
            'messeges.contact_id',
            'messeges.created_at',
            'messeges.updated_at'
        ]);
        $groupmMemberBuilder->where('messeges.from_contact_id', $gid);
        $groupmMemberBuilder->orderBy('messeges.id', 'asc');
        /** @var Collection $contacts */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $contacts = $groupmMemberBuilder->paginate($pageSize);
            $paginateData = (object)$contacts->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $contacts = $groupmMemberBuilder->get();
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
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getGroupMember(array $request, int $initId, int $recId, Carbon $startTime): array
    {
        $pageSize = $request['page_size'] ?? "";
        $paginate = $request['page'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        /** @var GroupMember|Builder $groupmMemberBuilder */

        $groupmMemberBuilder = GroupMember::select([
            'messeges.id',
            'messeges.from_contact_id',
            'messeges.to_contact_id',
            'messeges.messege_text',
            'contacts.first_name as sender_name',
            'messeges.contact_id',
            'messeges.created_at',
            'messeges.updated_at'
        ]);
        $groupmMemberBuilder->where('messeges.from_contact_id', $initId);
        $groupmMemberBuilder->orWhere('messeges.from_contact_id', $recId);
        $groupmMemberBuilder->join('contacts', 'contacts.id', 'messeges.contact_id');
        $groupmMemberBuilder->orderBy('messeges.id', 'asc');
        /** @var Collection $contacts */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $contacts = $groupmMemberBuilder->paginate($pageSize);
            $paginateData = (object)$contacts->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $contacts = $groupmMemberBuilder->get();
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
     * @return GroupMember
     */
    public function getOneGroupMember(int $id): GroupMember
    {
        /** @var GroupMember|Builder $groupmMemberBuilder */
        $groupmMemberBuilder = GroupMember::select([
            'messeges.id',
            'messeges.from_contact_id',
            'messeges.to_contact_id',
            'messeges.messege_text',
            'messeges.contact_id',
            'messeges.created_at',
            'messeges.updated_at'

        ]);


        /** @var GroupMember $GroupMember */
        return $groupmMemberBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return void
     * @throws Throwable
     */
    public function store(array $data, int $conversationId)
    {
        $customize = [];
        foreach ($data as $it) {
            $ar = array("contact_id" =>$it['value'],"conversation_id" => $conversationId);
               array_push($customize,$ar);
        }
        GroupMember::insert($customize);
    }

    /**
     * @param GroupMember $GroupMember
     * @param array $data
     * @return GroupMember
     */
    public function update(GroupMember $GroupMember, array $data): GroupMember
    {
        $GroupMember->fill($data);
        $GroupMember->save();
        return $GroupMember;
    }

    /**
     * @param GroupMember $GroupMember
     * @return bool
     */
    public function destroy(GroupMember $GroupMember): bool
    {
        return $GroupMember->delete();
    }

}
