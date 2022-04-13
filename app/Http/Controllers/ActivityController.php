<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use phpDocumentor\Reflection\DocBlock\Tags\Throws;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ActivityController extends Controller
{
    public function index()
    {
        try {
            $response = Activity::all()->sortBy("date");

            return response()->json($response, Response::HTTP_OK);
        } catch (Throwable $th) {
            return response()->json(["message" => $th->getMessage(), "statusCode" => Response::HTTP_BAD_REQUEST], Response::HTTP_BAD_REQUEST);
        }
    }

    public function show($id)
    {
        try {

            $response = Activity::findOrFail($id)->first();

            return response()->json($response, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json(["message" => "Activity not found", "statusCode" => Response::HTTP_BAD_REQUEST], Response::HTTP_BAD_REQUEST);
        } catch (Throwable $th) {
            return response()->json(["message" => $th->getMessage(), "statusCode" => Response::HTTP_BAD_REQUEST], Response::HTTP_BAD_REQUEST);
        }
    }

    public function store(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                "name" => "required|string",
                "date" => "required|date",
                "signature" => "required|in:yes,no",
                "descriptions" => "required|string",
            ]);

            if ($validate->fails()) {
                throw new Exception("Invalid activity data");
            }

            Activity::create($request->all());

            return response()->json(["message" => "Activity added succesfully", "statusCode" => Response::HTTP_ACCEPTED], Response::HTTP_ACCEPTED);
        } catch (\Throwable $th) {
            return response()->json(["message" => $th->getMessage(), "statusCode" => Response::HTTP_BAD_REQUEST], Response::HTTP_BAD_REQUEST);
        }
    }

    public function delete($id)
    {
        try {
            $try = Activity::findOrFail($id)->first()->delete();

            return response()->json(["message" => "Activity deleted succesfully", "statusCode" => Response::HTTP_OK], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json(["message" => "Activity not found", "statusCode" => Response::HTTP_BAD_REQUEST], Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $th) {
            return response()->json(["message" => $th->getMessage(), "statusCode" => Response::HTTP_BAD_REQUEST], Response::HTTP_BAD_REQUEST);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validate = Validator::make($request->all(), [
                "name" => "required|string",
                "date" => "required|date",
                "signature" => "required|in:yes,no",
                "descriptions" => "required|string",
            ]);

            if ($validate->fails()) {
                throw new Exception("Invalid activity data");
            }

            Activity::findOrFail($id)->update($request->all());

            return response()->json(["message" => "Activity updated succesfully", "statusCode" => Response::HTTP_ACCEPTED], Response::HTTP_ACCEPTED);
        } catch (\Throwable $th) {
            return response()->json(["message" => $th->getMessage(), "statusCode" => Response::HTTP_BAD_REQUEST], Response::HTTP_BAD_REQUEST);
        }
    }

    public function search(Request $request)
    {
        try {
            $keyword = $request->search;

            $response = Activity::query()->where('name', 'like', '%' . $keyword . '%')->get()->sortBy("date");

            if ($response->count() == 0) {
                throw new Exception("Activity not found");
            }

            return response()->json($response, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json(["message" => "Activity not found", "statusCode" => Response::HTTP_BAD_REQUEST], Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $th) {
            return response()->json(["message" => $th->getMessage(), "statusCode" => Response::HTTP_BAD_REQUEST], Response::HTTP_BAD_REQUEST);
        }
    }

    public function sort(Request $request)
    {
        try {
            $response = Activity::all()->where("date", ">=", Carbon::parse($request->from))->where("date", "<=", Carbon::parse($request->till))->sortBy("date");

            return response()->json($response, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json(["message" => "Activity not found", "statusCode" => Response::HTTP_BAD_REQUEST], Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $th) {
            return response()->json(["message" => $th->getMessage(), "statusCode" => Response::HTTP_BAD_REQUEST], Response::HTTP_BAD_REQUEST);
        }
    }
}
