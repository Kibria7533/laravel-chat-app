<?php

namespace App\Http\Controllers;

use App\Models\GroupMember;
use App\Models\Messege;
use App\Services\GroupMemberService;
use Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class GroupMemberController extends Controller
{
    /**
     * @var GroupMemberService
     */
    public GroupMemberService $groupMemberService;

    /**
     * @var Carbon
     */
    private Carbon $startTime;

    /**
     * TrainerController constructor.
     * @param GroupMemberService $groupMemberService
     */

    public function __construct(GroupMemberService $groupMemberService)
    {
        $this->groupMemberService = $groupMemberService;
        $this->startTime = Carbon::now();
    }

    /**
     * @param Request $request
     * @param int $initId
     * @param int $recId
     * @return JsonResponse
     */

    public function getMesseges(Request $request,int $gid,string $gname): JsonResponse
    {


        $response = $this->groupMemberService->getGroupMessege($request->all(),$gid,$gname, $this->startTime);
        return Response::json($response, ResponseAlias::HTTP_OK);
    }

    /**
     * * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function getList(Request $request,int $initId,int $recId): JsonResponse
    {


        $response = $this->groupMemberService->getGroupMember($request->all(),$initId,$recId, $this->startTime);
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
        $groupmMember = $this->groupMemberService->getOneGroupMember($id);

        $response = [
            "data" => $groupmMember,
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
        $dataa = $this->groupMemberService->store($data);
        $response = [
            'data' => $dataa ?: null,
            '_response_status' => [
                "success" => true,
                "session" => $request->session()->get('my_name'),
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "GroupMember added successfully.",
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
        $groupmMember = GroupMember::findOrFail($id);

        $data = $this->groupMemberService->update($groupmMember, $request->all());

        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "GroupMember updated successfully.",
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
        $groupmMember = GroupMember::findOrFail($id);

        $this->groupMemberService->destroy($groupmMember);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "GroupMember deleted successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }


}
