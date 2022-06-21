<?php

namespace App\Services;


use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Messege;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use phpDocumentor\Reflection\Types\Null_;
use Symfony\Component\HttpFoundation\Response;
use Throwable;


class MessegeService
{
    /**
     * @param array $request
     * @param Carbon $startTime
     * @return array
     */
    public function getMesseges(array $request, int $initId, int $recId , Carbon $startTime): array
    {

        $pageSize = $request['page_size'] ?? "";
        $paginate = $request['page'] ?? "";
        $rowStatus = $request['row_status'] ?? "";
        /** @var Messege|Builder $messegeBuilder */
        $messegeBuilder = Messege::select([
            'messeges.id',
            'messeges.from_contact_id',
            'messeges.to_contact_id',
            'messeges.messege_text',
            'contacts.first_name as sender_name',
            'messeges.conversation_id',
            'messeges.contact_id',
            'messeges.created_at',
            'messeges.updated_at'
        ]);
        $messegeBuilder->whereNull('messeges.conversation_id');
        $messegeBuilder->where('messeges.from_contact_id',$initId);
        $messegeBuilder->orWhere('messeges.from_contact_id',$recId);
        $messegeBuilder->join('contacts','contacts.id','messeges.contact_id');
        $messegeBuilder->orderBy('messeges.id','asc');

        /** @var Collection $contacts */
        if (is_numeric($paginate) || is_numeric($pageSize)) {
            $pageSize = $pageSize ?: 10;
            $contacts = $messegeBuilder->paginate($pageSize);
            $paginateData = (object)$contacts->toArray();
            $response['current_page'] = $paginateData->current_page;
            $response['total_page'] = $paginateData->last_page;
            $response['page_size'] = $paginateData->per_page;
            $response['total'] = $paginateData->total;
        } else {
            $contacts = $messegeBuilder->get();
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
     * @return Messege
     */
    public function getOneContact(int $id): Messege
    {
        /** @var Messege|Builder $messegeBuilder */
        $messegeBuilder = Messege::select([
            'messeges.id',
            'messeges.from_contact_id',
            'messeges.to_contact_id',
            'messeges.messege_text',
            'messeges.contact_id',
            'messeges.created_at',
            'messeges.updated_at'

        ]);


        /** @var Messege $messege */
        return $messegeBuilder->firstOrFail();
    }

    /**
     * @param array $data
     * @return Messege
     * @throws Throwable
     */
    public function store(array $data): Messege
    {
        $messege = app(Messege::class);

//        DB::beginTransaction();
//        try {


        $messege->fill($data);
        $messege->save();



//        } catch (Throwable $e) {
//            DB::rollBack();
//            throw $e;
//        }

        return $messege;
    }

    /**
     * @param Messege $messege
     * @param array $data
     * @return Messege
     */
    public function update(Messege $messege, array $data): Messege
    {
        $messege->fill($data);
        $messege->save();
        return $messege;
    }

    /**
     * @param Messege $messege
     * @return bool
     */
    public function destroy(Messege $messege): bool
    {
        return $messege->delete();
    }

}
