<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class CandidatesController extends Controller
{
    /**
     * Get Candidates by Event
     * @description Mengambil daftar kandidat berdasarkan ID event dan, jika diberikan, filter berdasarkan division ID.
     *
     * @status 200
     * @response Candidate[]
     */
    public function index($id, Request $request)
    {
        $query = Candidate::query()->where("event_id", $id);

        if ($request->division_id) {
            $query = $query
                ->where("division_id", $request->division_id)
                ->orderBy("order", "asc");
        }

        return $query->get();
    }

    /**
     * Create New Candidate
     * @description Membuat kandidat baru untuk suatu event dengan detail kandidat, seperti visi, misi, dan gambar.
     */
    public function store(Request $request, $id)
    {
        $request->validate([
            "division_id" => "required|string",
            "order" => "required|integer",
            "first" => "required|string",
            "first_name" => "required|string",
            "second" => "sometimes|string",
            "second_name" => "required_with:second|string",
            "vision" => "required|string",
            "mission" => "required|string",
            // 'picture' => 'required|image|mimes:jpeg,png,jpg,gif'
        ]);

        $order = $request->input("order");
        $division_id = $request->input("division_id");
        $eventCandidateCount = Candidate::where("event_id", $id)
            ->where("order", $order)
            ->where("division_id", $division_id)
            ->count();

        if ($eventCandidateCount > 0) {
            return response()->json(
                [
                    "errors" => [
                        "order" => "No urut sudah digunakan",
                    ],
                ],
                409,
            );
        }

        $candidate = new Candidate();
        $candidate->event_id = $id;
        $candidate->division_id = $request->input("division_id");
        $candidate->first = $request->input("first");
        $candidate->first_name = $request->input("first_name");
        $candidate->second = $request->input("second");
        $candidate->second_name = $request->input("second_name");
        $candidate->vision = $request->input("vision");
        $candidate->mission = $request->input("mission");
        $candidate->order = $request->input("order");
        $candidate->created_by = $request->user()->npm;
        if ($request->hasFile("picture")) {
            $candidate->picture = $request->input("picture");
            $candidate->picture = Storage::disk("public")->put(
                "events/candidates",
                $request->file("picture"),
            );
        }

        $candidate->save();

        return response()->json([
            "message" => "Candidate created successfully",
        ]);
    }

    /**
     * Update Candidate
     * @description Memperbarui data kandidat berdasarkan ID event dan ID kandidat, termasuk mengubah gambar jika diperlukan.
     */
    public function update(Request $request, $event, $id)
    {
        $request->validate([
            "division_id" => "required|string",
            "first" => "required|string",
            "first_name" => "required|string",
            "second" => "sometimes|string",
            "second_name" => "required_with:second|string",
            "order" => "required|integer",
            "vision" => "required|string",
            "mission" => "required|string",
            "picture" => "sometimes|image|mimes:jpeg,png,jpg,gif",
        ]);

        $order = $request->input("order");
        $division_id = $request->input("division_id");
        $eventCandidateCount = Candidate::where("id", "!=", $id)
            ->where("event_id", $event)
            ->where("order", $order)
            ->where("division_id", $division_id)
            ->count();

        if ($eventCandidateCount > 0) {
            return response()->json(
                ["message" => "No urut already exists for this event"],
                409,
            );
        }

        $candidate = Candidate::query()
            ->where("event_id", $event)
            ->where("id", $id)
            ->first();

        if (!$candidate) {
            return response()->json(["message" => "Candidate not found"], 404);
        }

        $candidate->division_id = $request->input("division_id");
        $candidate->first = $request->input("first");
        $candidate->first_name = $request->input("first_name");
        $candidate->second = $request->input("second");
        $candidate->second_name = $request->input("second_name");
        $candidate->vision = $request->input("vision");
        $candidate->mission = $request->input("mission");
        $candidate->order = $request->input("order");

        if ($request->hasFile("picture")) {
            $picture = Storage::disk("public")->put(
                "events/candidates",
                $request->file("picture"),
            );
            $candidate->picture = $picture;
        }

        $candidate->save();

        return response()->json([
            "message" => "Candidate updated successfully",
        ]);
    }

    /**
     * Get Candidate Details
     * @description Mengambil detail lengkap kandidat berdasarkan ID event dan ID kandidat.
     *
     * @status 200
     * @response Candidate
     */
    public function show($event, $candidate)
    {
        return Candidate::where("event_id", $event)
            ->where("id", $candidate)
            ->firstOrFail();
    }

    /**
     * Delete Candidate
     * @description Menghapus kandidat berdasarkan ID event dan ID kandidat.
     */
    public function destroy($event, $candidate)
    {
        $candidate = Candidate::find($candidate);

        if (!$candidate) {
            return response()->json(["message" => "candidate not found"], 404);
        }

        $candidate->delete();

        return response()->json([
            "message" => "candidate deleted successfully",
        ]);
    }

    /**
     * Get Candidates with Accepted Ballots
     * @description Mengambil daftar kandidat yang memiliki surat suara yang diterima untuk suatu event.
     *
     * @status 200
     * @response Candidate[]
     */
    public function ballots($id)
    {
        $candidates = Candidate::where("event_id", $id)
            ->whereHas("ballots", function ($query) {
                $query->where("accepted", "1");
            })
            ->get();

        return response()->json($candidates);
    }
}
