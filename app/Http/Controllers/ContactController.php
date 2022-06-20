<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Services\ContactService;
use Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class ContactController extends Controller
{
    /**
     * @var ContactService
     */
    public ContactService $contactService;

    /**
     * @var Carbon
     */
    private Carbon $startTime;

    /**
     * TrainerController constructor.
     * @param ContactService $contactService
     */

    public function __construct(ContactService $contactService)
    {
        $this->contactService = $contactService;
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


        $response = $this->contactService->getContactList($request->all(), $this->startTime);
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
        $trainer = $this->contactService->getOneContact($id);

        $response = [
            "data" => $trainer,
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
        $dataa = $this->contactService->store($data);
        $response = [
            'data' => $dataa ?: null,
            '_response_status' => [
                "success" => true,
                "session" => $request->session()->get('my_name'),
                "code" => ResponseAlias::HTTP_CREATED,
                "message" => "Contact added successfully.",
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
        $trainer = Contact::findOrFail($id);

        $data = $this->contactService->update($trainer, $request->all());

        $response = [
            'data' => $data,
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Contact updated successfully.",
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
        $trainer = Contact::findOrFail($id);

        $this->contactService->destroy($trainer);
        $response = [
            '_response_status' => [
                "success" => true,
                "code" => ResponseAlias::HTTP_OK,
                "message" => "Contact deleted successfully.",
                "query_time" => $this->startTime->diffInSeconds(Carbon::now()),
            ]
        ];
        return Response::json($response, ResponseAlias::HTTP_OK);
    }


}
