<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ActivityController extends Controller
{
    public function index()
    {
        try {
            $response = Activity::all();

            return response()->json($response, Response::HTTP_OK);
        } catch (Throwable $th) {
            return response()->json(["message" => "Activity failed to fetch", "statusCode" => Response::HTTP_BAD_REQUEST, "error" => $th->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function show($id)
    {
        try {
            $response = Activity::findOrFail($id)->first();

            return response()->json($response, Response::HTTP_OK);
        } catch (Throwable $th) {
            $find = Activity::where(["id" => $id])->count();
            if ($find == 0) {
                return response()->json(["message" => "Activity not found", "statusCode" => Response::HTTP_NOT_FOUND], Response::HTTP_NOT_FOUND);
            }
            return response()->json(["message" => "Activity failed to fetch", "statusCode" => Response::HTTP_BAD_REQUEST, "error" => $th->getMessage()], Response::HTTP_BAD_REQUEST);
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
                return response()->json(["message" => "Invalid activity data", "statusCode" => Response::HTTP_BAD_REQUEST, "error" => $validate->errors()->first()], Response::HTTP_BAD_REQUEST);
            }

            Activity::create($request->all());

            return response()->json(["message" => "Activity added succesfully", "statusCode" => Response::HTTP_ACCEPTED], Response::HTTP_ACCEPTED);
        } catch (\Throwable $th) {
            return response()->json(["message" => "Activity failed to store", "statusCode" => Response::HTTP_BAD_REQUEST, "error" => $th->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function delete($id)
    {
        try {
            Activity::findOrFail($id)->first()->delete();
            return response()->json(["message" => "Activity deleted succesfully", "statusCode" => Response::HTTP_OK], Response::HTTP_OK);
        } catch (\Throwable $th) {
            $find = Activity::where(["id" => $id])->count();
            if ($find == 0) {
                return response()->json(["message" => "Activity not found", "statusCode" => Response::HTTP_NOT_FOUND], Response::HTTP_NOT_FOUND);
            }
            return response()->json(["message" => "Activity failed to delete", "statusCode" => Response::HTTP_BAD_REQUEST, "error" => $th->getMessage()], Response::HTTP_BAD_REQUEST);
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
                return response()->json(["message" => "Invalid activity data", "statusCode" => Response::HTTP_BAD_REQUEST, "error" => $validate->errors()->first()], Response::HTTP_BAD_REQUEST);
            }

            Activity::findOrFail($id)->update($request->all());

            return response()->json(["message" => "Activity updated succesfully", "statusCode" => Response::HTTP_ACCEPTED], Response::HTTP_ACCEPTED);
        } catch (\Throwable $th) {
            return response()->json(["message" => "Activity failed to update", "statusCode" => Response::HTTP_BAD_REQUEST, "error" => $th->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function search(Request $request)
    {
        try {
            $keyword = $request->search;

            $response = Activity::query()->where('name', 'like', '%' . $keyword . '%')->get()->sortBy("date");

            return response()->json($response, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json(["message" => "Activity not found", "statusCode" => Response::HTTP_NOT_FOUND, "error" => $th->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    public function sort(Request $request)
    {
        try {
            $response = Activity::all()->where("date", ">=", Carbon::parse($request->from))->where("date", "<=", Carbon::parse($request->till))->sortBy("date");

            if ($response->count() === 0) {
                return response()->json(["message" => "Activity not found", "statusCode" => Response::HTTP_NOT_FOUND], Response::HTTP_NOT_FOUND);
            }

            return response()->json($response, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json(["message" => "Something not right", "statusCode" => Response::HTTP_BAD_REQUEST, "error" => $th->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
