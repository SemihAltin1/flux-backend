<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Note\StoreNoteRequest;
use App\Http\Requests\Note\UpdateNoteRequest;
use App\Http\Requests\Note\BulkCreateNotesRequest;
use App\Http\Requests\Note\BulkUpdateNotesRequest;
use App\Http\Requests\Note\BulkDeleteNotesRequest;
use App\Services\NoteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class NoteController extends Controller
{
    protected $noteService;

    public function __construct(NoteService $noteService)
    {
        $this->noteService = $noteService;
    }

    /**
     * Get all notes for the authenticated user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            $filters = $request->only(['category_id', 'is_pinned', 'search']);
            $notes = $this->noteService->getUserNotes($user, $filters);

            return response()->json([
                'success' => true,
                'message' => 'Notes retrieved successfully',
                'data' => [
                    'notes' => $notes,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve notes',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create a new note.
     *
     * @param StoreNoteRequest $request
     * @return JsonResponse
     */
    public function store(StoreNoteRequest $request): JsonResponse
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            $note = $this->noteService->createNote($user, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Note created successfully',
                'data' => [
                    'note' => $note,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create note',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get a specific note.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            $note = $this->noteService->getNote($user, $id);

            if (!$note) {
                return response()->json([
                    'success' => false,
                    'message' => 'Note not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Note retrieved successfully',
                'data' => [
                    'note' => $note,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve note',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update a note.
     *
     * @param UpdateNoteRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateNoteRequest $request, int $id): JsonResponse
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            $note = $this->noteService->getNote($user, $id);

            if (!$note) {
                return response()->json([
                    'success' => false,
                    'message' => 'Note not found',
                ], 404);
            }

            $updatedNote = $this->noteService->updateNote($note, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Note updated successfully',
                'data' => [
                    'note' => $updatedNote,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update note',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a note.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            $note = $this->noteService->getNote($user, $id);

            if (!$note) {
                return response()->json([
                    'success' => false,
                    'message' => 'Note not found',
                ], 404);
            }

            $this->noteService->deleteNote($note);

            return response()->json([
                'success' => true,
                'message' => 'Note deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete note',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Toggle pin status of a note.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function togglePin(int $id): JsonResponse
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            $note = $this->noteService->getNote($user, $id);

            if (!$note) {
                return response()->json([
                    'success' => false,
                    'message' => 'Note not found',
                ], 404);
            }

            $updatedNote = $this->noteService->togglePin($note);

            return response()->json([
                'success' => true,
                'message' => 'Note pin status updated successfully',
                'data' => [
                    'note' => $updatedNote,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle pin status',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Bulk create notes.
     *
     * @param BulkCreateNotesRequest $request
     * @return JsonResponse
     */
    public function bulkCreate(BulkCreateNotesRequest $request): JsonResponse
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            $result = $this->noteService->bulkCreateNotes($user, $request->notes);

            $statusCode = empty($result['errors']) ? 201 : 207; // 207 = Multi-Status

            return response()->json([
                'success' => empty($result['errors']),
                'message' => sprintf(
                    'Bulk create completed: %d created, %d failed',
                    count($result['created']),
                    count($result['errors'])
                ),
                'data' => [
                    'created' => $result['created'],
                    'created_count' => count($result['created']),
                    'errors' => $result['errors'],
                    'error_count' => count($result['errors']),
                ],
            ], $statusCode);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bulk create failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Bulk update notes.
     *
     * @param BulkUpdateNotesRequest $request
     * @return JsonResponse
     */
    public function bulkUpdate(BulkUpdateNotesRequest $request): JsonResponse
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            $result = $this->noteService->bulkUpdateNotes($user, $request->notes);

            $statusCode = empty($result['errors']) ? 200 : 207; // 207 = Multi-Status

            return response()->json([
                'success' => empty($result['errors']),
                'message' => sprintf(
                    'Bulk update completed: %d updated, %d failed',
                    count($result['updated']),
                    count($result['errors'])
                ),
                'data' => [
                    'updated' => $result['updated'],
                    'updated_count' => count($result['updated']),
                    'errors' => $result['errors'],
                    'error_count' => count($result['errors']),
                ],
            ], $statusCode);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bulk update failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Bulk delete notes.
     *
     * @param BulkDeleteNotesRequest $request
     * @return JsonResponse
     */
    public function bulkDelete(BulkDeleteNotesRequest $request): JsonResponse
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            $result = $this->noteService->bulkDeleteNotes($user, $request->note_ids);

            $statusCode = empty($result['errors']) ? 200 : 207; // 207 = Multi-Status

            return response()->json([
                'success' => empty($result['errors']),
                'message' => sprintf(
                    'Bulk delete completed: %d deleted, %d failed',
                    count($result['deleted']),
                    count($result['errors'])
                ),
                'data' => [
                    'deleted' => $result['deleted'],
                    'deleted_count' => count($result['deleted']),
                    'errors' => $result['errors'],
                    'error_count' => count($result['errors']),
                ],
            ], $statusCode);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bulk delete failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
