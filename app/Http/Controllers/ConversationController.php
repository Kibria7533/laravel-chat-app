<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Services\ConversationService;
use App\Services\GroupMemberService;
use Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class ConversationController extends Controller
{
    /**
     * @var ConversationService
     */
    public ConversationService $conversationService;

    /**
     * @var Carbon
     */
    private Carbon $startTime;

    /**
     * TrainerController constructor.
     * @param ConversationService $conversationService
     */

    public function __construct(ConversationService $conversationService)
    {
        $this->conversationService = $conversationService;
        $this->startTime = Carbon::now();
    }


    /**
     * * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function getList(Request $request): JsonResponse
    {


        $response = $this->conversationService->getConversationList($request->all(), $this->startTime);
        $response['session']=$request->session()->get('my_name');
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * * Display the specified resource
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function read(int $id): JsonResponse
    {
        $conversation = $this->conversationService->getOneConversastion($id);

        $response = [
            "data" => $conversation,
            "_response_status" => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "query_time" => $this->startTime->diffInSeconds(\Carbon\Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $data=$request->all();
        $cgname=[
            'conversation_name'=>$data['cgname']
        ];

        $dataa = $this->conversationService->store($cgname);
        $GM=app(GroupMemberService::class);
        $GM->store($data['selectedOption'],$dataa->id);
        $response = [
            'data' => $dataa ?: null,
            '_response_status' => [
                "success" => true,
                "session" => $request->session()->get('my_name'),
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "Conversation added successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     * @throws ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $conversation = Conversation::findOrFail($id);

        $data = $this->conversationService->update($conversation, $request->all());

        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Conversation updated successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_CREATED);
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy(int $id): JsonResponse
    {
        $conversation = Conversation::findOrFail($id);

        $this->conversationService->destroy($conversation);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Conversation deleted successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }


}
